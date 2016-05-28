<?php
class contest
{
    public $id;
    public $slug;
    public $contestants = array();

    function __construct($contest_id)
    {
        $this->id = $contest_id;
        self::get_contestants('RAND()');
    }

    function get_contestants($by)
    {
        global $wpdb;
        $results = $wpdb->get_results( "SELECT user_id FROM wp_bogacontest_contestant WHERE contest_id=". $this->id ." ORDER BY ". $by .";", OBJECT );
        $this->contestants = $results;
        return $results;
    }

    function search_contestant($query)
    {
        global $wpdb;
        return $wpdb->get_results( "SELECT wp_bogacontest_contestant.user_id FROM wp_bogacontest_contestant INNER JOIN wp_users ON wp_users.ID=wp_bogacontest_contestant.user_id WHERE wp_users.display_name LIKE '%". $query ."%' ;", OBJECT );
    }
}
class contestant
{
    public function getContestId()
    {
        return $this->contest_id;
    }

    public function setContestId($contest_id)
    {
        $this->contest_id = $contest_id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public $id = '';
    public $user_id = '';
    public $contest_id = '';
    public $name = '';
    public $description = '';
    public $votes = '';
    public $photos = array();

    function __construct()
    {
        add_shortcode( 'bogacontestant', array($this, 'print_data') );
    }

    function print_data(){
        global $wp_query;
        global $wpdb;
        $contestant_name = urldecode($wp_query->query_vars['contestant']);
        $contest_name = urldecode($wp_query->query_vars['contest']);
        $results = $wpdb->get_row( "SELECT wp_bogacontest_contestant.ID, wp_users.display_name FROM wp_bogacontest_contestant INNER JOIN wp_users ON wp_bogacontest_contestant.user_id=wp_users.ID INNER JOIN wp_bogacontest ON wp_bogacontest.ID=wp_bogacontest_contestant.contest_id WHERE wp_users.user_nicename='". $contestant_name ."' AND wp_bogacontest.slug='". $contest_name ."';", OBJECT );
        if (empty($results)) {
            return 'Concursante no encontrado';
        }
        $this->id = $results->ID;
        $this->name = $results->display_name;
        self::get_imgs();
        self::get_votes();
        echo '<div class="col-md-6">';
        foreach($this->photos as $photo){
            if ($photo->main){
                echo '<img src="'. $photo->path .'" class="img-responsive">';
            }
        }
        echo '</div>';
        echo '<div class="col-md-6">';
        echo '<h1>'. $this->name .'</h1>';
        echo '<h3>'. $this->votes .'</h3>';
        echo '</div>';

        return '';
    }

    function get_all_data($user_id, $contest_id){
        $this->user_id = $user_id;
        $this->contest_id = $contest_id;
        $results = self::get();
        if (empty($results)){
            self::create();
            self::get();
        }
        self::get_imgs();
        self::get_votes();
        return $this->id;
    }

    function create()
    {
        global $wpdb;
        $wpdb->insert(
            'wp_bogacontest_contestant',
            array(
                'user_id' => $this->user_id,
                'date' => date("Y-m-d H:i:s"),
                'contest_id' => $this->contest_id,
            ),
            array(
                '%s',
                '%s',
                '%s',
            )
        );
        $this->id = $wpdb->insert_id;
        return $this->id;
    }

    function get()
    {
        global $wpdb;
        $results = $wpdb->get_row( "SELECT wp_bogacontest_contestant.ID, wp_users.display_name FROM wp_bogacontest_contestant INNER JOIN wp_users ON wp_bogacontest_contestant.user_id=wp_users.ID WHERE wp_bogacontest_contestant.user_id=". $this->user_id ." AND wp_bogacontest_contestant.contest_id=". $this->contest_id .";", OBJECT );
        if (!empty($results)) {
            $this->id = $results->ID;
            $this->name = $results->display_name;
        }
        return $results;
    }

    function update()
    {
        global $wpdb;
        return $wpdb->update(
            'wp_bogacontest_contestant',
            array(
                'contest_id' => $this->contest_id
            ),
            array( 'ID' => $this->id, ),
            array(
                '%s'
            ),
            array( '%d', )
        );
    }

    function delete()
    {
        global $wpdb;
        $wpdb->delete( 'wp_bogacontest_contestant', array( 'ID' => $this->id ) );
    }

    function create_img($main, $path)
    {
        global $wpdb;
        $wpdb->insert(
            'wp_bogacontest_img',
            array(
                'contestant_id' => $this->id,
                'main' => $main,
                'path' => $path,
                'date' => date("Y-m-d H:i:s"),
            ),
            array(
                '%s',
                '%s',
                '%s',
                '%s',
            )
        );
        return $wpdb->insert_id;
    }

    function get_imgs()
    {
        global $wpdb;
        $results = $wpdb->get_results( "SELECT * FROM wp_bogacontest_img WHERE contestant_id=". $this->id ." ORDER BY wp_bogacontest_img.date;", OBJECT );
        $this->photos = $results;
        return $results;
    }

    function update_img($img_id, $main )
    {
        global $wpdb;
        return $wpdb->update(
            'wp_bogacontest_img',
            array(
                'main' => $main
            ),
            array( 'ID' => $img_id ),
            array(
                '%s'
            ),
            array( '%d' )
        );
    }

    function delete_img($img_id)
    {
        global $wpdb;
        return $wpdb->delete( 'wp_bogacontest_img', array( 'ID' => $img_id ) );
    }

    function get_votes()
    {
        global $wpdb;
        $this->votes = $wpdb->get_var("SELECT COUNT(*) FROM wp_bogacontest_votes WHERE contestant_id=". $this->id .";");
    }


}
/*require_once('../../../../wp-load.php');
$bogacontest = new contest(1);*/
