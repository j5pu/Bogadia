<?php
class contest
{
    public $id;
    public $slug;
    public $contestants = array();
    public $total_contestants;
    public $ranking;


    // Getters y Setters
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

    // Métodos Gestión
    function create()
    {
        /* Crea un nuevo concurso. Devuelve el id del concurso creado, 0 en caso de error */

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
            return $wpdb->insert_id;
        }else
            return 0;
    }

    function get_contest_slug_from_url(){
        /* Extrae el slug del concurso de la url */
        global $wpdb;
        global $wp_query;
        $this->slug = urldecode($wp_query->query_vars['contest']);
        $this->id = $wpdb->get_var('SELECT ID FROM wp_bogacontest WHERE slug="'. $this->slug .'" ;');
    }

    // Métodos
    function get_contestants($by, $direction, $search)
    {
        /* Rescata de base de datos Concursantes. Acepta buscar por nombre, ordenar por algún atributo y/o de menor a mayor y viceversa */

        global $wpdb;
        $query_search = "";
        $query_filter_var = "";
        $group_by = "";
        $left_join = "";

        // Composición de la query
        if (!empty($search))
        {
            // Busqueda por nombre
            $query_search = "AND wp_users.display_name LIKE '%". $search ."%'";
        }

        if (!empty($by))
        {
            // Ordenacion
            if ($by == 'votes')
            {
                // Query especial para ordenacion por votos (Ranking)
                $query_filter_var = ', COUNT(wp_bogacontest_votes.contestant_id) as votes ';
                $group_by = 'GROUP BY wp_bogacontest_votes.contestant_id';
                $left_join = 'LEFT JOIN wp_bogacontest_votes ON wp_bogacontest_contestant.ID=wp_bogacontest_votes.contestant_id';
            }

            $by = 'ORDER BY ' . $by ;
            $direction = 'DESC';
        }

        $variables = "wp_users.display_name, wp_users.user_nicename, wp_users.ID as user_id,wp_bogacontest_img.path as main_photo, wp_bogacontest_contestant.ID, wp_bogacontest.ID as contest_id ". $query_filter_var ." ";
        $tables = "wp_bogacontest_contestant INNER JOIN wp_users ON wp_bogacontest_contestant.user_id=wp_users.ID INNER JOIN wp_bogacontest ON wp_bogacontest.ID=wp_bogacontest_contestant.contest_id INNER JOIN wp_bogacontest_img ON wp_bogacontest_img.contestant_id=wp_bogacontest_contestant.ID ". $left_join ." ";
        $conditions = "wp_bogacontest.slug='". $this->slug ."' AND wp_bogacontest_img.main=1 ". $query_search ." ". $group_by . " " . $by ." ". $direction ;
        $query = "SELECT ". $variables . " FROM ". $tables ." WHERE ". $conditions .";";

        // Ejecución
        $results = $wpdb->get_results( $query, OBJECT );

        // Evaluacion
        if (!empty($results))
        {
            $this->contestants = $results;
            if ($by == 'votes') {
                $this->ranking;
            }
            return $results;
        } else
        {
            return 0;
        }
    }

    function get_ranking()
    {
        /* Calcula el ranking de los concursantes del concurso*/

        if (empty($this->ranking))
        {
            global $wpdb;
            $this->ranking = $wpdb->get_results("SELECT contestant_id, COUNT(*) as votes FROM wp_bogacontest_votes GROUP BY contestant_id ORDER BY votes DESC;", OBJECT);
        }
    }

    function count_contestans()
    {
        /* Calcula el numero total de concursantes inscritos en el concurso */

        global $wpdb;
        $this->total_contestants = $wpdb->get_var( "SELECT COUNT(*) FROM wp_bogacontest_contestant INNER JOIN wp_users ON wp_bogacontest_contestant.user_id=wp_users.ID INNER JOIN wp_bogacontest ON wp_bogacontest.ID=wp_bogacontest_contestant.contest_id INNER JOIN wp_bogacontest_img ON wp_bogacontest_img.contestant_id=wp_bogacontest_contestant.ID WHERE wp_bogacontest.slug='". $this->slug ."' AND wp_bogacontest_img.main=1 ;");
    }

    // Imprimir
    function print_contest_page()
    {
        /* Imprime la pagina individual de un concurso */

        self::get_contest_slug_from_url();
        self::get_contestants('RAND()', '', '');
        self::get_ranking();
        self::print_login_register_form();

        // PRESENTACION CONCURSO:
        echo '<img class="aligncenter img-responsive" src="http://alo.co/sites/default/files/imagecache/Main_Galeria_Vertical_720_438/_can8835.jpg">';
        echo '<h1>Sé portada de Bogadia</h1>';
        echo '<hr>';
        echo '<p>Entra de lleno en el mundo de la moda participando en BogaContest, el primer <strong>concurso de modelos</strong> para <strong>gente como tú</strong>. Podrás convertirte en la <strong>imagen de Bogadia</strong>, ganar un <strong>book de fotos profesional</strong> valorado en 300€ y promoción en todas nuestras <strong>redes sociales</strong>. ¿A qué esperas? ¡Haz click en participar para <strong>crear tu cuenta</strong>!</p>';
        self::print_participate_button();

        // CONCURSANTES
        echo '<h2 id="contestants_forest_header"><span id="contestants_forest_header_span">Así van las votaciones </span> </h2>';
        self::count_contestans();
        self::print_toolbar();
        echo '<div class="text-center" style="min-height: 500px">';
        echo '<img id="toolbar_loader" class="image-responsive" src="/wp-content/plugins/boga-contest/assets/img/BoganimationN2.gif" style="width: 200px;margin: 0 auto; display: none;">';
/*        echo '<div class="container-fluid">';*/
        echo '<div class="grid">';
        echo '<div class="grid-sizer col-xs-6 col-sm-4 col-md-3"></div>';
        echo '<div id="contestants_container">';

        if (empty($this->contestants))
        {
            echo '<p>¡Hola! Eres el primero en llegar. ¡Ánimate a participar!</p>';
            return '';
        }
        else
        {
            self::print_contestants();
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        return '';
    }

    function print_toolbar()
    {
        /* Imprime la barra de filtrado y busqueda de concursantes */

        echo '<div id="toolbar" class="row form-group text-center" data-slug="'. $this->slug .'">';
        echo '<div id="toolbar_counter" class="col-md-4">';
        echo '<small>'. $this->total_contestants .' concursantes</small>';
        echo '</div>';
        echo '<div id="toolbar_search" class="col-md-4">';
        echo '<input id="search_query_input" type="text" class="form-control" placeholder="buscar por nombre">';
        echo '</div>';
        echo '<div id="toolbar_filter" class="col-md-4">';
        echo '<div class="radio-inline"><label><input type="radio" name="optradio" value="votes">Ranking</label></div>';
        echo '<div class="radio-inline"><label><input type="radio" name="optradio" value="RAND()" checked="checked">Aleatorio</label></div>';
        echo '<div class="radio-inline"><label><input type="radio" name="optradio" value="wp_bogacontest_contestant.date">Recientes</label></div>';
        echo '</div>';
        echo '</div>';
    }

    function print_participate_button(){
        /* Imprime el boton de participar */

        echo '<div id="current-user-data-holder" class="row" data-currentuserid="'. get_current_user_id() .'" data-is_mobile="'. wp_is_mobile()  .'">';
        echo '<div class="col-md-3 ">';
        echo '</div>';
        echo '<div class="col-md-6 ">';
        echo '';
        echo '';
        echo '<button id="participate" type="button" class="btn btn-primary btn-block" data-contestid="'. $this->id .'"><div class="text-center" style="min-height: 18px"><img id="participate_loader" class="image-responsive" src="/wp-content/plugins/boga-contest/assets/img/Boganimation2.gif" style="width: 10%;margin: 0 auto; display: none; max-height: 18px;"><span id="participate_text">PARTICIPAR</span></div></button>';
        echo '</div>';
        echo '<div class="col-md-3 ">';
        echo '</div>';
        echo '</div>';
    }

    function print_contestants(){
        /* Imprime los concursantes inscritos en el concurso*/

        $counter = 0;
        self::get_ranking();
        foreach($this->contestants as $contestant_data){
            $contestant = new contestant();
            $contestant->set_contestant($contestant_data, $this);
            $contestant->get_position();
            $contestant->print_mini_card($this->slug);
            $counter++;
        }
    }

    function print_login_register_form(){
        /* Imprime el formulario modal de login y registro */

        echo '<div class="modal" id="bogacontest_login_modal" tabindex="-1" role="dialog" aria-labelledby="interstitialLabel" aria-hidden="true">';


        echo '<div  class="modal-dialog">';
        echo '<div id="bogacontest_login_modal_dialog" class="modal-content text-right">';

        // Encabezado del modal (ahora mismo vacio)
        echo '<div id="bogacontest_login_header" class="modal-header">';
        echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        echo '<h4 id="bogacontest_login_title" class="modal-title text-center"></h4>';
        echo '</div>';

        // Cuerpo del modal
        echo '<div class="modal-body">';
        echo '<div class="row">';
        //// Parte de la foto de la modelo
        echo '<div class="col-xs-5 col-sm-6 col-md-6">';
        echo '</div>';

        //// Parte del formulario
        echo '<div id="bogacontest_login_body" class="col-xs-7 col-sm-6 col-md-6">';
        echo '<div id="form_wrapper">';
        ////// Boton facebook
        echo '<button id="bogacontest_fb_login" type="button" class="btn btn-primary btn-lg"><em class="icon-facebook"></em> | Entrar con facebook</button>';
        echo '<hr>';
        ////// Formulario registro
        echo '<h4 id="register_help_text" style="color: white;"></h4>';
        echo '<small id="email_validate_text" style="display: none; color: chartreuse;">¡Hey! Revisa el email que has introducido, parece que hay algo mal</small>';

        echo '<div id="first_form">';
        echo '<form id="login_form_form" method="post" action="">';
        echo '<input id="bogacontest_up_login_email" class="form-control" type="email" name="email" placeholder="Correo electrónico">';
        echo '<input id="bogacontest_up_login_password" class="form-control" type="password" name="password" placeholder="Contraseña">';
        echo '<button id="bogacontest_up_login" type="submit" class="btn btn-primary " data-ajaxurl="'. admin_url( 'admin-ajax.php' ) .'"><div class="text-center" style="min-height: 18px"><img id="login_loader" class="img-responsive" src="/wp-content/plugins/boga-contest/assets/img/Boganimation2.gif" style="margin: 0 auto; display: none; width: 54px;"><span id="login_text">Entrar</span></div></button>';
        echo '</form>';
        echo '</div>';

        echo '<div id="second_form" style="display: none;">';
        echo '<form id="register_form_form" method="post" action="">';
        echo '<input id="bogacontest_up_login_username" class="form-control" type="text" name="username" placeholder="Nombre completo" >';
        echo '<button id="bogacontest_up_register" type="submit" class="btn btn-primary " data-ajaxurl="'. admin_url( 'admin-ajax.php' ) .'"><div class="text-center" style="min-height: 18px"><img id="register_loader" class="img-responsive" src="/wp-content/plugins/boga-contest/assets/img/Boganimation2.gif" style="margin: 0 auto; display: none; width: 54px;"><span id="register_text">Registrarme</span></div></button>';
        echo '<button id="go_back" class="btn btn-default">Volver atrás</button>';
        echo '</form>';
        echo '</div>';

        echo wp_nonce_field( 'ajax-login-nonce', 'bogacontest_up_login_security' );
        echo wp_nonce_field( 'ajax-register-nonce', 'bogacontest_up_register_security' );
        echo '</div>';

        echo '<input id="bogacontest_up_login_action_after_login" class=  "form-control" type="hidden" name="action_after_login" value="0">';
        echo '</div>';

        echo '</div>';
        echo '</div>';
        echo '<img id="login_succes_loader" class="img-responsive" src="/wp-content/plugins/boga-contest/assets/img/Boganimation2.gif" style="margin: 0 auto; display: none; width: 100px;">';

        // Footer del modal
/*        echo '<div id="bogacontest_login_footer" class="modal-footer">';
        echo '<a class="lost" href="'. wp_lostpassword_url() .'">¿Has olvidado tu contraseña?</a>';
        echo '</div>';*/

        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}

class contestant
{
    public $ID;
    public $user_id;
    public $name;
    public $description;
    public $votes;
    public $main_photo;
    public $photos = array();
    public $nice_name;
    public $position;
    public $contest;
    public $mail;

    // Getters y Setters
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

    public function getContest()
    {
        return $this->contest;
    }

    public function setContest($contest)
    {
        $this->contest = $contest;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->contest = $position;
    }

    public function getVotes()
    {
        return $this->votes;
    }

    public function setVotes($votes)
    {
        $this->votes = $votes;
    }

    function set_contestant($contestant_data, $contest){
        self::setID($contestant_data->ID);
        self::setUserId($contestant_data->user_id);
        self::setContestId($contestant_data->contest_id);
        self::setName($contestant_data->display_name);
        self::setNiceName($contestant_data->user_nicename);
        if (!empty($contestant_data->main_photo)){
            self::setMainPhoto($contestant_data->main_photo);
        }
        self::setContest($contest);
        if (!empty($contestant_data->position)){
            self::setPosition($contestant_data->position);
        }
        if (!empty($contestant_data->votes)){
            self::setVotes($contestant_data->votes);
        }
    }

    // Metodos de gestion
    function get_or_create(){
        $results = self::get();
        if (empty($results)){
            self::create();
            self::get();
            self::new_bogacontestant_notification();

        }
        self::get_imgs();
        self::get_votes();
        return $this->ID;
    }

    function new_bogacontestant_notification()
    {
        $email_subject = "A por todas " . cut_title($this->name, 15) . "!";

        ob_start();
        ?>

        <p>Buenas, <?php echo cut_title($this->name, 15)  ?>. Gracias por participar en Bogacontest</p>

        <p>
            Te deseamos toda la suerte del mundo.
        </p>

        <p>
            Tu página web personal es <a href="https://www.bogadia.com/concursos/portada-mayo/<?php echo $this->nice_name ?>">https://www.bogadia.com/concursos/portada-mayo/<?php echo $this->nice_name ?></a>
        </p>

        <p>
            Ahí es donde tus amigos y todo el mundo podrá ver tus fotos y, lo más importante, votarte.
        </p>
        <p>
            Si tienes cualquier problema, duda o sugerencia, escribe un mail a info@bogadia.com
        </p>

        <p>¡Disfruta de bogadia.com! Gracias</p>

        <?php
        $message = ob_get_contents();
        ob_end_clean();

        wp_mail($this->mail, $email_subject, $message);
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
            $u = new WP_User( $this->user_id );
            $u->remove_role( 'subscriber' );
            $u->add_role( 'BogaContestant' );
        }
        return $results;
    }

    function get()
    {
        global $wpdb;
        $results = $wpdb->get_row( "SELECT wp_bogacontest_contestant.ID, wp_users.display_name, wp_users.user_nicename, wp_users.user_email FROM wp_bogacontest_contestant INNER JOIN wp_users ON wp_bogacontest_contestant.user_id=wp_users.ID WHERE wp_bogacontest_contestant.user_id=". $this->user_id ." AND wp_bogacontest_contestant.contest_id=". $this->contest_id .";", OBJECT );
        if (!empty($results)) {
            $this->ID = $results->ID;
            $this->name = $results->display_name;
            $this->nice_name = $results->user_nicename;
            $this->mail = $results->user_email;
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
        $results = $wpdb->get_results( "SELECT * FROM wp_bogacontest_img WHERE contestant_id=". $this->ID ." ORDER BY wp_bogacontest_img.date DESC;", OBJECT );
        $this->photos = $results;
        foreach($this->photos as $photo){
            if ($photo->main){
                $this->main_photo = $photo->path;
                $this->main_photo_id = $photo->post_id;
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

    function quit_main_photo()
    {
        /* Desselecciona una foto como principal ya que se va a subir una foto principal nueva */
        global $wpdb;
        return $wpdb->update(
            'wp_bogacontest_img',
            array(
                'main' => 0
            ),
            array( 'main' => 1, 'contestant_id' => $this->ID),
            array(
                '%d',
                '%d',
            ),
            array( '%d', '%d' )
        );
    }

    function delete_img($img_id)
    {
        global $wpdb;
        $first = $wpdb->delete( 'wp_bogacontest_img', array( 'id' => $img_id, 'contestant_id' => $this->ID) );
        if ($first == 'false'){
            return 'No se ha podido borrar la foto';
        }else{
            return 'Foto borrada con éxito';
        }
    }

    function get_votes()
    {
        if (empty($this->votes)){
            global $wpdb;
            $this->votes = $wpdb->get_var("SELECT COUNT(*) FROM wp_bogacontest_votes WHERE contestant_id=". $this->ID .";");
            if (is_null($this->votes)){
                $this->votes = 0;
            }

        }
    }

    function anotate_vote($voter_id){
        if ($this->user_id == $voter_id){
            return '¡Ehh tramposo! No vale votarte a ti mismo';
        }
        global $wpdb;
        $last_user_vote = $wpdb->get_var("SELECT date FROM wp_bogacontest_votes WHERE contestant_id=". $this->ID ." AND voter_id=". $voter_id ." ORDER BY date DESC;");
        if ($last_user_vote){
            $last_user_vote = new DateTime("$last_user_vote");
            $date_to_vote_again = date_add($last_user_vote, date_interval_create_from_date_string('1 days'));
            $now = new DateTime("now");
            $time_to_vote_again = date_diff($now, $date_to_vote_again);

            if (! ($time_to_vote_again->invert == 1)){
                return 'Podrás votarle en '. $time_to_vote_again->format('%h horas y %i minutos') .'.';
            }

        }

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
            return '¡Genial! Voto contado';
        }
        return '¡Upps! Tu voto no se ha contado';

    }

    function get_position(){
        if(empty($this->position)){
            if (!empty($this->contest->ranking)){
                $counter = 1;
                foreach($this->contest->ranking as $contestant_position){
                    if ($contestant_position->contestant_id == $this->ID){
                        $this->position = $counter;
                        $this->votes = $contestant_position->votes;

                        break;
                    }
                    $counter++;
                }
            }
            if (is_null($this->votes)){
                $this->votes = 0;
            }
        }
    }

    function get_contestant_from_slug(){
        global $wp_query;
        global $wpdb;
        $contestant_name_or_id = urldecode($wp_query->query_vars['contestant']);

        if (is_numeric($contestant_name_or_id))
        {
            $query_lookup_field = 'wp_bogacontest_contestant.ID='. $contestant_name_or_id;
        }else
        {
            $query_lookup_field = 'wp_users.user_nicename="'. $contestant_name_or_id.'"';
        }

        $this->contest = new contest();
        $this->contest->get_contest_slug_from_url();
        $this->contest->get_ranking();

        $results = $wpdb->get_row( "SELECT wp_users.display_name, wp_users.user_nicename, wp_users.ID as user_id, wp_bogacontest_contestant.ID, wp_bogacontest.ID as contest_id  FROM wp_bogacontest_contestant INNER JOIN wp_users ON wp_bogacontest_contestant.user_id=wp_users.ID INNER JOIN wp_bogacontest ON wp_bogacontest.ID=wp_bogacontest_contestant.contest_id WHERE ". $query_lookup_field ." AND wp_bogacontest.slug='". $this->contest->slug ."';", OBJECT );

        if (empty($results))
        {
            return 'Concursante no encontrado';
        }
        return $results;
    }

    // Imprimir
    function print_share_buttons()
    {
        echo '<div class="row bogacontest_social_row">';
        echo '<div class="col-xs-3 col-sm-3 col-md-3 text-center">';
        echo '<em class="icon-facebook bogacontest_social"></em>';
        echo '</div>';
        echo '<div class="col-xs-3 col-sm-3 col-md-3 text-center">';
        echo '<em class="icon-twitter bogacontest_social"></em>';
        echo '</div>';
        echo '<div class="col-xs-3 col-sm-3 col-md-3 text-center">';
        echo '<i class="icon-instagramm bogacontest_social"></i>';
        echo '</div>';
        echo '<div class="col-xs-3 col-sm-3 col-md-3 text-center">';
        echo '<i class="icon-pinterest-circled bogacontest_social"></i>';
        echo '</div>';
        echo '</div>';
    }

    function print_vote_button($primary)
    {
        $button = '<button id="vote-contestant-'. $this->ID .'" type="button" class="btn ';

        if($primary == True)
        {
            $button .= ' btn-primary ';
        }else{
            $button .= ' btn-default ';
        }

        $button .= 'btn-block vote" data-id="'. $this->ID .'" data-contestantuserid="'. $this->user_id .'">VOTAR</button>';
        echo $button;
    }

    function print_mini_card($contest_slug)
    {
        echo '<div class="grid-item col-xs-6 col-sm-4 col-md-3 mini_image">';
        echo '<a target="_blank" href="/concursos/'. $contest_slug .'/'. $this->nice_name .'">';
        echo '<img id="contestant-'. $this->ID .'"  src="'. $this->main_photo .'" >';
        echo '<h6 class="mini-name"><span class="mini_span">'. cut_title($this->name, 15) .'</span></h6>';
        echo '<h6 class="mini-votes"><span class="mini_span">'. $this->votes .' <i class="icon-star" aria-hidden="true"></i></span></h6>';
        echo '</a>';
        echo '</div>';
    }

    function print_contestant_page()
    {
        global $current_user_id;
        $current_user_id = get_current_user_id();
        $results = self::get_contestant_from_slug();
        self::set_contestant($results, $this->contest);
        self::get_imgs();
        self::get_votes();
        self::get_position();
        global $current_user_is_editing;
        $current_user_is_editing = false;
        if(isset($_GET['preview'])) {
           if ($_GET['preview'] == 'true'){
               $current_user_is_editing = false;
           }
        }elseif ($current_user_id == $this->user_id) {
            $current_user_is_editing = true;
        }

        $this->contest->print_login_register_form();
        self::print_photos_manager();

        // Navegación
        echo '<div class="row">';
        echo '<div class="col-md-12">';
        echo '<p id="bogacontest_breadcrumb"><a style="color: #444444 !important;" href="/concursos/'. $this->contest->slug .'">Bogacontest</a> / '. $this->name ;
        if (!$current_user_is_editing) {
            echo '<a id="participate" data-contestid="' . $this->contest->id . '" style="float: right; cursor: pointer; color: #444444 !important;" >Participa</a>';
        }
        echo '</p>';
        echo '</div>';
        echo '</div>';

        // Foto principal y nombre
        echo '<div id="current-user-data-holder" class="row" data-currentuserid="'. $current_user_id .'" data-contestantuserid="'. $this->user_id .'">';
        self::print_main_photo();
        echo '<div class="col-sm-6 col-md-6">';
        echo '<h2 id="" style="font-size: 250%;"><span id="">'. $this->name .'</span></h2>';
        echo '<h3 style="margin-top: 40px;"><a id="votes-'. $this->ID .'" data-votes="'. $this->votes .'" style="float:left;">'. $this->votes .' votos</a> <a style="float:right;">';

        if(!empty($this->position))
        {
            echo 'Posición '. $this->position ;
        }

        echo '</a></h3>';
        echo '</div>';
        echo '</div>';

        // Botones
        echo '<div class="row">';
        echo '<div class="col-md-3 ">';
        echo '</div>';
        echo '<div id="interaction_buttons_wrapper" class="col-md-6">';
        if (!$current_user_is_editing){
            self::print_share_buttons();
            self::print_vote_button(True);
        }
        echo '</div>';
        echo '<div class="col-md-3 ">';
        echo '</div>';
        echo '</div>';

        echo '<hr>';

        // Galeria
        self::print_contestant_gallery();

        echo '</div>';
        if ($current_user_is_editing) {
            echo '<div style="margin-top: 100px;">';
            echo '<div class="col-md-3 ">';
            echo '</div>';
            echo '<div class="col-md-6 ">';
            echo '<button id="preview" type="button" class="btn btn-default btn-block" data-contestid="'. $this->contest->id .'" data-nicename="'. $this->nice_name .'"><div class="text-center" style="min-height: 18px"><img id="participate_loader" class="image-responsive" src="/wp-content/plugins/boga-contest/assets/img/Boganimation2.gif" style="width: 10%;margin: 0 auto; display: none; max-height: 18px;"><span id="participate_text"><i class="icon-eye" aria-hidden="true"></i>PREVISUALIZAR</span></div></button>';
            echo '</div>';
            echo '<div class="col-md-3 ">';
            echo '</div>';
            echo '</div>';
        }
        echo '<div id="toolbar" class="row form-group text-center" data-slug="'. $this->contest->slug .'"></div>';


        return '';
    }

    function print_photos_manager()
    {
        $contador = 0;

        echo '<div class="modal fade" id="bogacontest_manager_modal" tabindex="-1" role="dialog" aria-labelledby="interstitialLabel" aria-hidden="true">';

        echo '<div  class="modal-dialog">';
        echo '<div id="bogacontest_manager_modal_dialog" class="modal-content text-right">';

        echo '<div id="bogacontest_manager_header" class="modal-header">';
        echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        echo '<h4 id="bogacontest_manager_title" class="modal-title text-center">Selecciona una foto</h4>';
        echo '</div>';


        echo '<div class="modal-body">';
        echo '<div class="row">';

        echo '<div id="photo_manager_select" class="col-xs-12 col-sm-12 col-md-12" style="height: 250px; overflow-y: scroll;">';

        if (!empty($this->photos))
        {
            foreach($this->photos as $photo)
            {
                if (!$photo->main)
                {
                    echo '<div id="manager_image_container_'. $photo->id .'" class="col-xs-4 col-sm-4 col-md-4" style="margin-bottom: 15px;">';
                    echo '<label class="manager_photo" >';
                    echo '<input type="radio" name="photo_to_edit" value="'. $photo->id .'" />';
                    echo '<img id="manager-contestant-'. $contador .'" class="img-responsive contestant-photo" src="'. $photo->path .'" >';
                    echo '</label>';
                    echo '</div>';
                    $contador++;
                }
            }
        }

        echo '</div>';
        echo '</div>';
        echo '</div>';


        echo '<div id="bogacontest_manager_footer" class="modal-footer">';
        echo '<button id="delete_selected_photo" class="btn btn-primary">Borrar foto</button>';
        echo '</div>';

        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    function print_contestant_gallery()
    {
        global $current_user_is_editing;
        if($current_user_is_editing)
        {
            echo '<div class="row">';
            echo '<div class="col-md-12">';
            echo '<button id="upload_alias" type="button" class="btn ';
            if (empty($this->main_photo)){
                echo 'btn-default';
            }else{
                echo 'btn-primary';
            }
            echo ' btn-block"><i class="icon-upload" aria-hidden="true"></i>Subir foto a tu galería</button>';
            echo '<input id="upload" type="file" class="form-control" data-nonce="'. wp_create_nonce("media-form")  .'" style="display: none;" data-contestantid="'. $this->ID .'">';
            echo '<div id="progress_gallery_bar_container" class="progress" style="display: none;"><div id="upload_progress_gallery_bar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0;"><span id="upload_progress_gallery_bar_text" class="sr-only"></span></div></div>';
            echo '<button id="delete" type="button" class="btn btn-default btn-block"><i class="icon-trash" aria-hidden="true"></i>Borrar foto</button>';
            echo '</div>';
            echo '</div>';
        }
        echo '<div class="row">';
        echo '<div id="gallery" class="col-md-12" style="margin: 10px 15px 10px 15px;">';


        if (!empty($this->photos))
        {
            $contador = 0;
            $row_counter = 1;

            foreach($this->photos as $photo)
            {
                if($photo->main == 0)
                {
                    echo '<div id="gallery_image_container_'. $photo->id .'" class="col-xs-6 col-sm-6 col-md-3 gallery_container" style="padding: 0 0 0 0 !important; height: 100px; overflow-y: hidden;">';
                    echo '<a class="main_photo_holder_link" href="'. $photo->path .'">';
                    echo '<img id="contestant-'. $contador .'" class="img-responsive contestant-photo" src="'. $photo->path .'" >';
                    echo '</a>';
                    echo '</div>';
                    $contador++;
                }
            }
        }else
        {
            echo '<div class="row gallery-row" style="">';
            echo '<div id="fake_photo_1" class="col-xs-6 col-sm-6 col-md-3 fake_photo" style="padding: 0 0 0 0 !important; height: 100px; overflow-y: hidden;">';
            echo '<img id="contestant-0" class="img-responsive contestant-photo" src="/wp-content/plugins/boga-contest/assets/img/facebook-girl-avatar.png" >';
            echo '</div>';
            echo '<div id="fake_photo_2" class="col-xs-6 col-sm-6 col-md-3 fake_photo" style="padding: 0 0 0 0 !important; height: 100px; overflow-y: hidden;">';
            echo '<img id="contestant-1" class="img-responsive contestant-photo" src="/wp-content/plugins/boga-contest/assets/img/pro_justice___facebook_no_profile_by_officialprojustice-d6zqggi.jpg" >';
            echo '</div>';
            echo '<div id="fake_photo_3" class="col-xs-6 col-sm-6 col-md-3 fake_photo" style="padding: 0 0 0 0 !important; height: 100px; overflow-y: hidden;">';
            echo '<img id="contestant-2" class="img-responsive contestant-photo" src="/wp-content/plugins/boga-contest/assets/img/sexy_facebook_avatar_by_tesne-d3feuml.jpg" >';
            echo '</div>';
            echo '<div id="fake_photo_4" class="col-xs-6 col-sm-6 col-md-3 fake_photo" style="padding: 0 0 0 0 !important; height: 100px; overflow-y: hidden;">';
            echo '<img id="contestant-3" class="img-responsive contestant-photo" src="/wp-content/plugins/boga-contest/assets/img/facebook-girl-avatar.png" >';
            echo '</div>';
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
    }

    function print_main_photo()
    {
        global $current_user_is_editing;
        $button_text = '';
        $button_class = '';

        echo '<div id="gallery_image_container_'. $this->main_photo_id .'" class="col-sm-6 col-md-6" data-main="1">';

        // Foto principal
        if (!empty($this->main_photo))
        {
            echo '<a id="main_photo_link" class="main_photo_holder_link" href="'. $this->main_photo .'">';
            echo '<img id="main_photo" src="'. $this->main_photo .'" class="img-responsive" style="margin: 0 auto;">';
            echo '</a>';
            $button_text = 'Cambia tu foto principal';
            $button_class = 'btn-default';
        }else
        {
            echo '<a id="main_photo_link" class="main_photo_holder_link" href="/wp-content/plugins/boga-contest/assets/img/______2757470_orig.jpg">';
            echo '<img id="main_photo" class="fake_main_photo img-responsive" src="/wp-content/plugins/boga-contest/assets/img/______2757470_orig.jpg" style="margin: 0 auto;">';
            echo '</a>';
            $button_text = '¡Sube tu foto principal!';
            $button_class = 'btn-primary';
        }

        // Boton de subida
        if($current_user_is_editing)
        {
            echo '<button id="upload_main_alias" type="button" class="btn '. $button_class .' btn-block"><i class="icon-upload" aria-hidden="true"></i>'. $button_text .'</button>';
            echo '<input id="upload_main" accept="image/*" type="file" class="form-control" data-nonce="'. wp_create_nonce("media-form")  .'" style="display: none;" data-contestantid="'. $this->ID .'">';
            echo '<div id="progress_bar_container" class="progress" style="display: none;"><div id="upload_progress_bar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0;"><span id="upload_progress_bar_text" class="sr-only"></span></div></div>';
        }
        echo '</div>';
    }
}