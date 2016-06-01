<?php
class contest
{

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getTotalContestants()
    {
        return $this->total_contestants;
    }

    public function setTotalContestants($total_contestants)
    {
        $this->total_contestants = $total_contestants;
    }

    public $id;
    public $slug;
    public $contestants = array();
    public $total_contestants;

    function __construct()
    {
    }

    function create()
    {
        global $wpdb;
        $results = $wpdb->insert(
            'wp_bogacontest',
            array(
                'slug' => $this->slug,
            ),
            array(
                '%s',
            )
        );
        if($results){
            $results = $wpdb->insert_id;
        }
        return $results;
    }


    function get_contest_slug_from_url(){
        global $wp_query;
        $this->slug = urldecode($wp_query->query_vars['contest']);
    }

    function get_contestants($by, $direction)
    {
        global $wpdb;
/*        $results = $wpdb->get_results( "SELECT user_id FROM wp_bogacontest_contestant WHERE contest_id=". $this->id ." ORDER BY ". $by .";", OBJECT );*/
        $results = $wpdb->get_results( "SELECT wp_users.display_name, wp_users.user_nicename, wp_users.ID as user_id,wp_bogacontest_img.path as main_photo, wp_bogacontest_contestant.ID, wp_bogacontest.ID as contest_id FROM wp_bogacontest_contestant INNER JOIN wp_users ON wp_bogacontest_contestant.user_id=wp_users.ID INNER JOIN wp_bogacontest ON wp_bogacontest.ID=wp_bogacontest_contestant.contest_id INNER JOIN wp_bogacontest_img ON wp_bogacontest_img.contestant_id=wp_bogacontest_contestant.ID WHERE wp_bogacontest.slug='". $this->slug ."' AND wp_bogacontest_img.main=1 ORDER BY '. $by .' '. $direction .';", OBJECT );
        $this->contestants = $results;
        return $results;
    }

    function count_contestans(){
        global $wpdb;
        $this->total_contestants = $wpdb->get_var( "SELECT COUNT(*) FROM wp_bogacontest_contestant INNER JOIN wp_users ON wp_bogacontest_contestant.user_id=wp_users.ID INNER JOIN wp_bogacontest ON wp_bogacontest.ID=wp_bogacontest_contestant.contest_id INNER JOIN wp_bogacontest_img ON wp_bogacontest_img.contestant_id=wp_bogacontest_contestant.ID WHERE wp_bogacontest.slug='". $this->slug ."' AND wp_bogacontest_img.main=1 ;", OBJECT );
    }

    function search_contestant($query)
    {
        global $wpdb;
        return $wpdb->get_results( "SELECT wp_bogacontest_contestant.user_id FROM wp_bogacontest_contestant INNER JOIN wp_users ON wp_users.ID=wp_bogacontest_contestant.user_id WHERE wp_users.display_name LIKE '%". $query ."%' ;", OBJECT );
    }

    function print_contest_data()
    {
        self::get_contest_slug_from_url();
        self::get_contestants('RAND()', '');

        if (empty($this->contestants)) {
            global $wpdb;
            $this->id = $wpdb->get_var("SELECT ID FROM wp_bogacontest WHERE slug='". $this->slug ."';");

            if (empty($this->id)) {
                $this->id = self::create();
                self::get_contestants('RAND()', '');

                if (empty($this->contestants)) {
                    echo '<p>¡Hola! Eres el primero en llegar. ¡Ánimate a participar!</p>';
                    self::print_participate_button();
                    return '';
                }
            }
        }else{
            self::count_contestans();
            $this->id = $this->contestants[0]->contest_id;

        }


        self::print_participate_button();

        echo '<div class="row">';
        echo '<div class="col-md-12">';
        echo '<small>Así van las votaciones: '. $this->total_contestants .' participantes.</small><hr>';
        self::print_toolbar();
        self::print_contestants();
        echo '</div>';
        echo '</div>';
        return '';
    }

    function print_toolbar(){
        echo '<div id="toolbar" class="row form-group">';
        echo '<div id="toolbar" class="col-md-6">';
        echo '<input type="text" class="form-control" placeholder="buscar por nombre">';
        echo '</div>';
        echo '<div id="toolbar" class="col-md-6">';
        echo '<div class="radio-inline"><label><input type="radio" name="optradio">Ranking</label></div>';
        echo '<div class="radio-inline"><label><input type="radio" name="optradio">Aleatorio</label></div>';
        echo '<div class="radio-inline"><label><input type="radio" name="optradio">Recientes</label></div>';
        echo '</div>';
        echo '</div>';

    }

    function print_participate_button(){
        echo '<div id="current-user-data-holder" class="row" data-currentuserid="'. get_current_user_id() .'">';
        echo '<div class="col-md-3 ">';
        echo '</div>';
        echo '<div class="col-md-6 ">';
        echo '<button id="participate" type="button" class="btn btn-primary btn-block" data-contestid="'. $this->id .'">PARTICIPAR</button>';
        echo '<a id="login_show" class="kleo-show-login" href="#" style="visibility: hidden; none">Show Login popup</a>';
        echo '</div>';
        echo '<div class="col-md-3 ">';
        echo '</div>';
        echo '</div>';
    }

    function print_contestants(){
        $counter = 0;
        foreach($this->contestants as $contestant_data){
            $contestant = new contestant();
            $contestant->set_contestant($contestant_data);
            $contestant->get_votes();

            if ($counter % 4 == 0){
                if($counter != 0){
                    echo '</div>';
                }
                echo '<div class="row">';
            }
            $contestant->print_mini_card($this->slug);
            $counter++;
        }
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

    public function getID()
    {
        return $this->ID;
    }

    public function setID($ID)
    {
        $this->ID = $ID;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getPhotos()
    {
        return $this->photos;
    }

    public function setPhotos($photos)
    {
        $this->photos = $photos;
    }

    public function getNiceName()
    {
        return $this->nice_name;
    }

    public function setNiceName($nice_name)
    {
        $this->nice_name = $nice_name;
    }

    public function getMainPhoto()
    {
        return $this->main_photo;
    }

    public function setMainPhoto($main_photo)
    {
        $this->main_photo = $main_photo;
    }
    function set_contestant($contestant_data){
        self::setID($contestant_data->ID);
        self::setUserId($contestant_data->user_id);
        self::setContestId($contestant_data->contest_id);
        self::setName($contestant_data->display_name);
        self::setNiceName($contestant_data->user_nicename);
        if (!empty($contestant_data->main_photo)){
            self::setMainPhoto($contestant_data->main_photo);
        }
    }

    public $ID;
    public $user_id;
    public $contest_id;
    public $name;
    public $description;
    public $votes;
    public $main_photo;
    public $photos = array();
    public $nice_name;

    function __construct()
    {
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
        return $this->ID;
    }

    function create()
    {
        global $wpdb;
        $results = $wpdb->insert(
            'wp_bogacontest_contestant',
            array(
                'user_id' => $this->user_id,
                'date' => date("Y-m-d H:i:s"),
                'contest_id' => $this->contest_id,
            ),
            array(
                '%d',
                '%s',
                '%d',
            )
        );
        if($results){
            $results = $wpdb->insert_id;
        }
        return $results;
    }

    function get()
    {
        global $wpdb;
        $results = $wpdb->get_row( "SELECT wp_bogacontest_contestant.ID, wp_users.display_name FROM wp_bogacontest_contestant INNER JOIN wp_users ON wp_bogacontest_contestant.user_id=wp_users.ID WHERE wp_bogacontest_contestant.user_id=". $this->user_id ." AND wp_bogacontest_contestant.contest_id=". $this->contest_id .";", OBJECT );
        if (!empty($results)) {
            $this->ID = $results->ID;
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
            array( 'ID' => $this->ID, ),
            array(
                '%s'
            ),
            array( '%d', )
        );
    }

    function delete()
    {
        global $wpdb;
        $wpdb->delete( 'wp_bogacontest_contestant', array( 'ID' => $this->ID ) );
    }

    function create_img($main, $path)
    {
        global $wpdb;
        $wpdb->insert(
            'wp_bogacontest_img',
            array(
                'contestant_id' => $this->ID,
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
        $results = $wpdb->get_results( "SELECT * FROM wp_bogacontest_img WHERE contestant_id=". $this->ID ." ORDER BY wp_bogacontest_img.date;", OBJECT );
        $this->photos = $results;
        foreach($this->photos as $photo){
            if ($photo->main){
                $this->main_photo = $photo->path;
            }
        }
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
        $this->votes = $wpdb->get_var("SELECT COUNT(*) FROM wp_bogacontest_votes WHERE contestant_id=". $this->ID .";");
    }

    function anotate_vote($voter_id){
        global $wpdb;
        $success = $wpdb->insert(
            'wp_bogacontest_votes',
            array(
                'contestant_id' => $this->ID,
                'voter_id' => $voter_id,
                'date' => date("Y-m-d H:i:s"),
            ),
            array(
                '%d',
                '%d',
                '%s',
            )
        );
        if($success){
            $success = $wpdb->insert_id;
        }
        return $success;
    }

    function print_social_data(){
        echo '<div class="row">';
        echo '<div class="col-xs-3 col-sm-3 col-md-3 text-center">';
        echo '<em class="icon-facebook" style="font-size: 22px;"></em>';
        echo '</div>';
        echo '<div class="col-xs-3 col-sm-3 col-md-3 text-center">';
        echo '<em class="icon-twitter" style="font-size: 22px;"></em>';
        echo '</div>';
        echo '<div class="col-xs-3 col-sm-3 col-md-3 text-center">';
        echo '<em class="icon-instagram" style="font-size: 22px;"></em>';
        echo '</div>';
        echo '<div class="col-xs-3 col-sm-3 col-md-3 text-center">';
        echo '<em class="icon-pinterest" style="font-size: 22px;"></em>';
        echo '</div>';
        echo '</div>';
    }

    function print_vote_button(){
        echo '<button id="vote-contestant-'. $this->ID .'" type="button" class="btn btn-primary btn-block vote" data-id="'. $this->ID .'">VOTAR</button>';
    }

    function print_mini_card($contest_slug){
        echo '<div class="col-md-3 portada_post">';
        echo '<div style="height: 120px; overflow-y: hidden;">';
        echo '<img id="contestant-'. $this->ID .'" class="img-responsive" src="'. $this->main_photo .'" >';
        echo '</div>';
        echo '<h3><a href="/concursos/'. $contest_slug .'/'. $this->nice_name .'">'. $this->name .'</a></h3>';
        echo $this->votes .' votos';
        self::print_social_data();
        self::print_vote_button();
        echo '</div>';
    }

    function get_id_or_nicename_from_url(){

    }

    function print_contestant_data(){
        global $wp_query;
        global $wpdb;
        $contador = 0;
        $contestant_name_or_id = urldecode($wp_query->query_vars['contestant']);
        if (is_numeric($contestant_name_or_id)){
            $query_lookup_field = 'wp_bogacontest_contestant.ID='. $contestant_name_or_id;
        }else{
            $query_lookup_field = 'wp_users.user_nicename="'. $contestant_name_or_id.'"';
        }
        $contest_name = urldecode($wp_query->query_vars['contest']);
        $current_user_id = get_current_user_id();
        $results = $wpdb->get_row( "SELECT wp_users.display_name, wp_users.user_nicename, wp_users.ID as user_id, wp_bogacontest_contestant.ID, wp_bogacontest.ID as contest_id  FROM wp_bogacontest_contestant INNER JOIN wp_users ON wp_bogacontest_contestant.user_id=wp_users.ID INNER JOIN wp_bogacontest ON wp_bogacontest.ID=wp_bogacontest_contestant.contest_id WHERE ". $query_lookup_field ." AND wp_bogacontest.slug='". $contest_name ."';", OBJECT );
        if (empty($results)) {
            return 'Concursante no encontrado';
        }
        self::set_contestant($results);
        self::get_imgs();
        self::get_votes();

        echo '<div id="current-user-data-holder" class="row" data-currentuserid="'. $current_user_id .'">';
        echo '<a id="login_show" class="kleo-show-login" href="#" style="display: none">Show Login popup</a>';
        echo '<div class="col-sm-6 col-md-6">';
        if (!empty($this->main_photo)){
            echo '<a id="main_photo_holder" href="'. $this->main_photo .'">';
            echo '<img id="main_photo" src="'. $this->main_photo .'" class="img-responsive">';
            echo '</a>';
        }else{
            echo '<img id="no_main_photo" src="/wp-content/plugins/boga-contest/assets/img/______2757470_orig.jpg" class="img-responsive">';
        }
        echo '</div>';
        echo '<div class="col-sm-6 col-md-6">';
        if($current_user_id == $this->user_id){
            echo '<div class="row">';
            echo '<div class="col-sm-4 col-md-4">';
            echo '<button id="upload_alias" type="button" class="btn btn-primary btn-block">Subir foto</button>';
            echo '<input id="upload" type="file" class="form-control" data-nonce="'. wp_create_nonce("media-form")  .'" style="display: none;" data-contestantid="'. $this->ID .'">';
/*            echo '<div class="progress"><div id="upload_progress_bar" class="bar progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:0%"></div></div>';*/
            echo '<div id="progress_bar_container" class="progress" style="display: none;"><div id="upload_progress_bar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0;"><span id="upload_progress_bar_text" class="sr-only"></span></div></div>';
            echo '</div>';
            echo '<div class="col-sm-4 col-md-4">';
            echo '<button type="button" class="btn btn-primary btn-block">Editar mi perfil</button>';
            echo '</div>';
            echo '<div class="col-sm-4 col-md-4">';
            echo '<button type="button" class="btn btn-primary btn-block">Cambiar foto principal</button>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';


        echo '<div class="row">';
        echo '<div class="col-sm-6 col-md-6">';
        echo '<h1>'. $this->name .'</h1>';
        echo '<h3>Posición actual: '. $this->votes .'<a id="votes-'. $this->ID .'" data-votes="'. $this->votes .'" style="float:right;">'. $this->votes .' votos.</a></h3>';
        self::print_social_data();
        self::print_vote_button();
        echo '</div>';
        echo '</div>';

        echo '<hr>';

        echo '<div class="row">';
        echo '<div id="gallery" class="col-md-12" style="margin: 10px 15px 10px 15px;">';
        if (!empty($this->photos)){
            foreach($this->photos as $photo){
                if($photo->main == 0){
                    if ($contador % 4 == 0){
                        if($contador != 0){
                            echo '</div>';
                        }
                        echo '<div class="row gallery-row" style="">';
                    }
                    echo '<div class="col-xs-6 col-sm-6 col-md-3" style="padding: 0 0 0 0 !important; height: 100px; overflow-y: hidden;">';
                    echo '<a id="main_photo_holder" href="'. $photo->path .'">';
                    echo '<img id="contestant-'. $contador .'" class="img-responsive contestant-photo" src="'. $photo->path .'" >';
                    echo '</a>';
                    echo '</div>';
                    $contador++;
                }
            }
        }else{
            echo '<div class="row" style="">';
            echo '<div class="col-xs-6 col-sm-6 col-md-3" style="padding: 0 0 0 0 !important; height: 100px; overflow-y: hidden;">';
            echo '<img id="contestant-'. $contador .'" class="img-responsive contestant-photo" src="/wp-content/plugins/boga-contest/assets/img/facebook-girl-avatar.png" >';
            echo '</div>';
            echo '<div class="col-xs-6 col-sm-6 col-md-3" style="padding: 0 0 0 0 !important; height: 100px; overflow-y: hidden;">';
            echo '<img id="contestant-'. $contador .'" class="img-responsive contestant-photo" src="/wp-content/plugins/boga-contest/assets/img/pro_justice___facebook_no_profile_by_officialprojustice-d6zqggi.jpg" >';
            echo '</div>';
            echo '<div class="col-xs-6 col-sm-6 col-md-3" style="padding: 0 0 0 0 !important; height: 100px; overflow-y: hidden;">';
            echo '<img id="contestant-'. $contador .'" class="img-responsive contestant-photo" src="/wp-content/plugins/boga-contest/assets/img/sexy_facebook_avatar_by_tesne-d3feuml.jpg" >';
            echo '</div>';
            echo '<div class="col-xs-6 col-sm-6 col-md-3" style="padding: 0 0 0 0 !important; height: 100px; overflow-y: hidden;">';
            echo '<img id="contestant-'. $contador .'" class="img-responsive contestant-photo" src="/wp-content/plugins/boga-contest/assets/img/facebook-girl-avatar.png" >';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';

/*        echo '<div class="row">';
        echo '<div class="col-md-3 ">';
        echo '</div>';
        echo '<div class="col-md-6 ">';
        self::print_social_data();
        self::print_vote_button();
        echo '</div>';
        echo '<div class="col-md-3 ">';
        echo '</div>';
        echo '</div>';*/
        return '';
    }

}