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
    function get_contestants($by, $direction, $search, $exclude, $off)
    {
        /* Rescata de base de datos Concursantes. Acepta buscar por nombre, ordenar por algún atributo y/o de menor a mayor y viceversa */

        global $wpdb;
        global $limit;
        $query_search = "";
        $query_filter_var = "";
        $group_by = "";
        $left_join = "";
        $not_in = "";
        $offset = "";
        $limit = 25;

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
            if($by == 'RAND()'){
                if (!empty($exclude)){
                    $not_in = "AND wp_bogacontest_contestant.ID NOT IN ( '" . implode($exclude, "', '") . "' )";
                }

            }else{
                if (!empty($off)){
                    $offset = " OFFSET ". ($off * $limit);
                }
            }

            $by = 'ORDER BY ' . $by ;
            $direction = 'DESC';
        }

        $variables = "wp_users.display_name, wp_users.user_nicename, wp_users.ID as user_id,wp_bogacontest_img.path as main_photo, wp_bogacontest_contestant.ID, wp_bogacontest.ID as contest_id ". $query_filter_var ." ";
        $tables = "wp_bogacontest_contestant INNER JOIN wp_users ON wp_bogacontest_contestant.user_id=wp_users.ID INNER JOIN wp_bogacontest ON wp_bogacontest.ID=wp_bogacontest_contestant.contest_id INNER JOIN wp_bogacontest_img ON wp_bogacontest_img.contestant_id=wp_bogacontest_contestant.ID ". $left_join ." ";
        $conditions = "wp_bogacontest.slug='". $this->slug ."' AND wp_bogacontest_img.main=1 ". $not_in ." ". $query_search ." ". $group_by . " " . $by ." ". $direction ." LIMIT ". $limit . $offset ;
        $query = "SELECT ". $variables ." FROM ". $tables ." WHERE ". $conditions .";";

        // Ejecución
        $results = $wpdb->get_results( $query, OBJECT );

        // Evaluacion
        if (!empty($results))
        {
            $this->contestants = $results;
            if ($by == 'votes') {
                $this->ranking = $results;
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
            $this->ranking = $wpdb->get_results("SELECT contestant_id, COUNT(*) as votes FROM wp_bogacontest_votes INNER JOIN wp_bogacontest_contestant ON wp_bogacontest_votes.contestant_id=wp_bogacontest_contestant.ID WHERE wp_bogacontest_contestant.contest_id='". $this->id ."' GROUP BY contestant_id ORDER BY votes DESC;", OBJECT);
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
        self::print_contest_presentation();




        self::print_contestant_forest();
        self::print_login_register_form(0);

    }

    function print_vote_page(){
        $this->slug = 'concurso-modelos';
        $this->id = 4;
        echo '<h1 class="text-center">¡<strong>Vota</strong> a la <strong>modelo</strong> que será la portada de nuestra revista en Septiembre y podras ganar 100 € / 120 US $!</h1>';
        echo '<h2 class="text-center">Vota a tus favoritos en nuestro casting online para modelos y gana dinero con nosotros</h2>';
        echo '<p>Entre todas las personas que voten se sortearán estos <strong>tres premios:</strong>';
        echo '<ul>';
        echo '<li>1º Premio: 100€ </li>';
        echo '<li>2º Premio: 50€</li>';
        echo '<li>3º Premio: 25€</li>';
        echo '</ul>';
        echo 'Además, <strong>si compartes en tus redes sociales</strong> la candidatura del modelo o los modelos que hayas votado, <strong>entrarás en el concurso de 50€ adicionales</strong>.</p>';
        echo '<p>Puedes votar hasta el día 15 de septiembre, será entonces cuando se cierren las votaciones. <strong>Cuanto más votes y compartas las candidaturas, ¡más posibilidades tienes de ganar!</strong></p>';
        echo '<p>Recuerda que con tus votos estás eligiendo quién protagonizará la portada de septiembre de Bogadia y se llevará un book de fotos valorado en 300€.</p>';
        echo '<p><strong>¡Participa y sé parte de nuestro jurado!</strong></p>';
        echo '<small>Concurso ante notario. Los ganadores del concurso serán publicados en nuestra web con el método y las reglas el 18 de Septiembre. Bases legales en nuestra web.</small>';
        echo '<small id="description">Se pagará por paypal en pagos internacionales y/o cuenta bancaria en España.</small>';

        self::print_contestant_forest();
        self::print_login_register_form();
        echo '<section id="participate" data-contestid="'. $this->id .'" title="Casting para ser modelo de Bogadia - Concurso de portada">';

        echo '<div id="current-user-data-holder" class="row" data-currentuserid="'. get_current_user_id() .'" data-is_mobile="'. wp_is_mobile()  .'">';
        echo '</section>';
        echo '<p> </p>';

    }

    function print_contest_presentation(){

        // PRESENTACION CONCURSO:
        echo '<h1 class="text-center"><strong>Concurso</strong> de <strong>modelos</strong></h1>';
        echo '<h2 class="text-center">Primer <strong>casting online</strong> para gente como tú</h2>';
/*        echo '<section id="participate" data-contestid="'. $this->id .'" title="Casting para ser modelo de Bogadia - Concurso de portada">';

        echo '<div id="current-user-data-holder" class="row" data-currentuserid="'. get_current_user_id() .'" data-is_mobile="'. wp_is_mobile()  .'">';
        echo '</section>';*/
        echo '<img class="img-responsive" style="width: 50%;" src="https://www.bogadia.com/wp-content/uploads/2016/05/logo_final_negro-2.png" >';
        echo '<img class="img-responsive" style="padding-top: 10px;" src="/wp-content/plugins/boga-contest/assets/img/IMAGEN_GANADORA_CONCURSO.jpg" alt="Ganadora del concurso de modelos">';

        echo '<p id="description">Sube tu foto, consigue votos y conviértete en <strong>modelo</strong> de la portada de nuestra revista mediante nuestro <strong>casting online</strong>. Ganarás un <strong>book</strong> profesional valorado en 300 Euros y entrar de lleno en el mundo de la moda. Además, se seleccionará a l@s que serán imagen de los diseñadores que colaborarán en el lanzamiento de nuestra tienda online.</p>';

    }

    function print_contestant_forest(){
        self::get_contestants('RAND()', '', '', '', '');
        self::get_ranking();
        // CONCURSANTES
        echo '<h3 id="contestants_forest_header"><span id="contestants_forest_header_span">Modelos en este casting </span> </h3>';
        self::count_contestans();
        self::print_toolbar();
        echo '<div class="text-center" style="min-height: 500px; margin-bottom: 50px;">';
        echo '<img id="toolbar_loader" class="image-responsive" src="/wp-content/plugins/boga-contest/assets/img/BoganimationN2.gif" style="width: 200px;margin: 0 auto; display: none;">';
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
        echo '<button id="load_more" class="btn btn-primary" style="display: none; margin: 0 auto;"><div class="text-center" style="min-height: 18px"><img id="load_more_loader" class="img-responsive" src="/wp-content/plugins/boga-contest/assets/img/Boganimation2.gif" style="margin: 0 auto; display: none; width: 74px;"><span id="load_more_text">Cargar más modelos</span></div></button>';
        echo '</div>';
        echo '</div>';

        return '';
    }

    function print_toolbar()
    {
        /* Imprime la barra de filtrado y busqueda de concursantes */
        global $limit;

        echo '<div id="toolbar" class="row form-group text-center" data-slug="'. $this->slug .'">';
        echo '<div id="toolbar_counter" class="col-md-4">';
        echo '<small id="toolbar_counter_number" data-total_contestant="'. $this->total_contestants .'" data-limit="'. $limit .'">'. $this->total_contestants .' modelos</small>';
        echo '</div>';
        echo '<div id="toolbar_search" class="col-md-4">';
        echo '<input id="search_query_input" type="text" class="form-control" placeholder="buscar por nombre">';
        echo '</div>';
        echo '<div id="toolbar_filter" class="col-md-4">';
        echo '<div class="radio-inline"><label class="toolbar_label"><input type="radio" name="optradio" value="votes"><span>Ranking</span></label></div>';
        echo '<div class="radio-inline"><label class="toolbar_label"><input type="radio" name="optradio" value="RAND()" checked="checked"><span>Aleatorio</span></label></div>';
        echo '<div class="radio-inline"><label class="toolbar_label"><input type="radio" name="optradio" value="wp_bogacontest_contestant.date"><span>Recientes</span></label></div>';
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

    function print_login_register_form($only_facebook){
        /* Imprime el formulario modal de login y registro */
        echo '<script src=\'https://www.google.com/recaptcha/api.js\'></script>';


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

        //// Parte del formulario
        echo '<div id="bogacontest_login_body" class="col-xs-12 col-sm-12 col-md-12">';
        echo '<img class="img-responsive" src="/wp-content/plugins/boga-contest/assets/img/logo_tnsprnte-min.png" style="margin: 0 auto;">';
        echo '<div id="form_wrapper">';
        ////// Boton facebook
        echo '<button id="bogacontest_fb_login" type="button" class="btn btn-primary btn-lg"><em class="icon-facebook"></em> | Entrar con facebook</button>';
        if ($only_facebook == 0){
            echo '<h4 class="text-center">o</h4>';
            ////// Formulario registro
            echo '<h4 id="register_help_text" style="color: red;"></h4>';
            echo '<small id="email_validate_text" style="display: none; color: red;">¡Hey! Revisa el email que has introducido, parece que hay algo mal</small>';

            echo '<div id="first_form">';
            echo '<form id="login_form_form" method="post" action="">';
            echo '<input id="bogacontest_up_login_email" class="form-control" type="email" name="email" placeholder="Correo electrónico">';
            echo '<input id="bogacontest_up_login_password" class="form-control" type="password" name="password" placeholder="Contraseña">';
            echo '<button id="bogacontest_up_login" type="submit" class="btn btn-primary " data-ajaxurl="'. admin_url( 'admin-ajax.php' ) .'"><div class="text-center" style="min-height: 18px"><img id="login_loader" class="img-responsive" src="/wp-content/plugins/boga-contest/assets/img/Boganimation2.gif" style="margin: 0 auto; display: none; width: 54px;"><span id="login_text">Registrarme</span></div></button>';
            echo '<button id="bogacontest_up_login_2" class="btn btn-default"><div class="text-center" style="min-height: 18px"><img id="login_loader_2" class="img-responsive" src="/wp-content/plugins/boga-contest/assets/img/BoganimationN2.gif" style="margin: 0 auto; display: none; width: 54px;"><span id="login_2_text">Iniciar sesión</span></div></button>';
            echo '</form>';
            echo '</div>';

            echo '<div id="second_form" style="display: none;">';
            echo '<form id="register_form_form" method="post" action="">';
            echo '<input id="bogacontest_up_login_username" class="form-control" type="text" name="username" placeholder="Nombre completo" >';
            echo '<div class="g-recaptcha" data-sitekey="6LcZlygTAAAAAEkuQ_eJ6sLMVL6l6hGLtSdelq_Q"></div>';
            echo '<button id="bogacontest_up_register" type="submit" class="btn btn-primary " data-ajaxurl="'. admin_url( 'admin-ajax.php' ) .'"><div class="text-center" style="min-height: 18px"><img id="register_loader" class="img-responsive" src="/wp-content/plugins/boga-contest/assets/img/Boganimation2.gif" style="margin: 0 auto; display: none; width: 54px;"><span id="register_text">Registrarme</span></div></button>';
            echo '<button id="go_back" class="btn btn-default">Volver atrás</button>';
            echo '</form>';
            echo '</div>';
        }

        echo wp_nonce_field( 'ajax-login-nonce', 'bogacontest_up_login_security' );
        echo wp_nonce_field( 'ajax-register-nonce', 'bogacontest_up_register_security' );
        echo '</div>';
        echo '<img id="login_succes_loader" class="img-responsive" src="/wp-content/plugins/boga-contest/assets/img/BoganimationN2.gif" style="margin: 0 auto; display: none; width: 100px;">';
        echo '<input id="bogacontest_up_login_action_after_login" class=  "form-control" type="hidden" name="action_after_login" value="0">';
        echo '</div>';

        echo '</div>';
        echo '</div>';

        // Footer del modal
        echo '<div id="bogacontest_login_footer" class="modal-footer">';
        echo '<small>Al registrarte aceptas nuestra <a href="/wp-content/plugins/boga-contest/assets/pdf/politicadeprivacidadconcursomodelosBogadia.pdf" style="text-decoration: underline;">política de privacidad</a> y <a href="/wp-content/plugins/boga-contest/assets/pdf/BaseslegalesConcursodemodelosAgosto.pdf" style="text-decoration: underline;">bases legales</a>. Además, afirmas ser mayor de edad o mayor de 14 años y que cuentas con autorización paterna.</small>';
        echo '</div>';

        echo '</div>';
        echo '</div>';
        echo '</div>';
        // facebook pixel
        $pixel_code = '<!-- Facebook Pixel Code -->';
        $pixel_code .= '<script>';
        $pixel_code .= '!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?';
        $pixel_code .= 'n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;';
        $pixel_code .= "n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;";
        $pixel_code .= 't.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,';
        $pixel_code .= "document,'script','https://connect.facebook.net/en_US/fbevents.js');";
        $pixel_code .= "fbq('init', '1749402588641150');";
        $pixel_code .= "fbq('track'";
        $pixel_code .= ', "PageView");</script>';
        $pixel_code .= '<noscript><img height="1" width="1" style="display:none"';
        $pixel_code .= 'src="https://www.facebook.com/tr?id=1749402588641150&ev=PageView&noscript=1"';
        $pixel_code .= '/></noscript>';
        $pixel_code .= '<!-- End Facebook Pixel Code -->';
        echo $pixel_code;
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
        $new = 0;
        $results = self::get();
        if (empty($results)){
            self::create();
            self::get();
            self::new_bogacontestant_notification();
            $new = 1;
        }
        self::get_imgs();
        self::get_votes();
        return array($this->ID, $new);
    }

    function new_bogacontestant_notification()
    {
        $email_subject = "A por todas " . cut_title($this->name, 15) . "!";

        ob_start();
        ?>

        <p>Buenas <?php echo cut_title($this->name, 15)  ?>. Gracias por participar en el concurso de modelos de bogadia</p>

        <p>
            Te deseamos toda la suerte del mundo.
        </p>

        <p>
            Tu página web personal es <a href="https://www.bogadia.com/concursos/<?php echo $this->contest->slug ?>/<?php echo $this->nice_name ?>">https://www.bogadia.com/concursos/<?php echo $this->contest->slug ?>/<?php echo $this->nice_name ?></a>
        </p>

        <p>
            Ahí es donde tus amigos y todo el mundo podrá ver tus fotos y, lo más importante, votarte.
        </p>
        <p>
            Recuerda que puedes añadir más fotos y cambiar tu imagen principal siempre que quieras.
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
        $result = $wpdb->delete( 'wp_bogacontest_contestant', array( 'ID' => $this->ID ), array( '%d' ) );
        if ($result==false){
            return 'No se ha podido borrar al usuario';
        }else{
            return 'Usuario borrado';
        }
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
/*        if ($this->user_id == $voter_id){
            return '¡Ehh tramposo! No vale votarte a ti mismo';
        }*/
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
        function encodeURIComponent($str) {
            $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
            return strtr(rawurlencode($str), $revert);
        }

        $title = 'Vota a '. $this->name .' y gana 50€ #modeloBogadia @Bogadiamag';
        echo '<div class="row bogacontest_social_row">';
        echo '<div class="col-xs-3 col-sm-3 col-md-3 text-center">';
        echo '<a id="bogacontest_facebook" target="_blank" href="http://www.facebook.com/sharer/sharer.php?u='. $this->print_affiliate_link('facebook') .'"><em class="icon-facebook bogacontest_social"></em></a>';
        echo '</div>';
        echo '<div class="col-xs-3 col-sm-3 col-md-3 text-center">';
        echo '<a id="bogacontest_twitter" target="_blank" href="http://twitter.com/intent/tweet?status='. encodeURIComponent($title .' '. $this->print_affiliate_link('twitter') ) .'"><em class="icon-twitter bogacontest_social"></em></a>';
        echo '</div>';
        echo '<div class="col-xs-3 col-sm-3 col-md-3 text-center">';
        echo '<a id="bogacontest_whatsapp" target="_blank" href="whatsapp://send?text='. encodeURIComponent($title .' '. $this->print_affiliate_link('whatsapp') ) .'" data-action="share/whatsapp/share"><i class="icon-whatsapp bogacontest_social"></i></a>';
        echo '</div>';
        echo '<div class="col-xs-3 col-sm-3 col-md-3 text-center">';
        echo '<a id="bogacontest_pinterest" target="_blank" href="http://pinterest.com/pin/create/bookmarklet/?media=https://www.bogadia.com'. $this->main_photo .'&url='. $this->print_affiliate_link('pinterest') .'&is_video=false&description='. $title .'"><i class="icon-pinterest-circled bogacontest_social"></i></a>';
        echo '</div>';
        echo '</div>';
    }

    function print_affiliate_link($redsocial){
        global $current_user_id;
        return 'https://www.bogadia.com/concursos/'. $this->contest->slug .'/'. $this->nice_name .'?utm_source='. $redsocial .'&utm_medium=www.bogadia.com&utm_term='. $current_user_id .'&utm_content='. $this->ID .'&utm_campaign=SHARE' ;
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
        global $current_user_id;

        echo '<div class="grid-item col-xs-6 col-sm-4 col-md-3 mini_image" data-contestant_id="'. $this->ID .'"   width="500px" height="281px">';
        echo '<a href="/concursos/'. $contest_slug .'/'. $this->nice_name .'">';
        echo '<img id="contestant-'. $this->ID .'"  src="'. $this->main_photo .'" alt="foto de '. $this->name .', modelo de Bogadia">';
        echo '<h6 class="mini-name"><span class="mini_span">'. cut_title($this->name, 10) .'</span></h6>';
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
        if(isset($_GET['edit'])) {
           if ($_GET['edit'] == 'true' && ($current_user_id == $this->user_id)){
               $current_user_is_editing = true;
           }
        }

        if ($current_user_is_editing){
            self::print_photos_manager();
        }

        echo '<div id="current-user-data-holder" class="row" data-currentuserid="'. $current_user_id .'" data-contestantuserid="'. $this->user_id .'">';
        // COLUMNA IZQUIERDA
        // Foto principal y nombre
        echo '<div class="col-sm-5 col-md-5 col-lg-4">';
        self::print_main_photo();

        // TARJETA: NOMBRE, VOTOS, POSICION , COMPARTIR Y VOTAR
        echo '<div id="interaction_buttons_wrapper" class="fixed_to_bottom" style="padding-bottom: 0px;">';
        // Nombres, votos y posicion
        echo '<div class="row" >';
        echo '<div class="col-md-12 text-center">';
        echo '<h1 id="contestant_name" style="font-size: 150%;">'. $this->name .'</h1>';
        echo ' <small id="votes-'. $this->ID .'" data-votes="'. $this->votes .'"  >'. $this->votes .' votos</small>';
        if(!empty($this->position))
        {
            echo ' <small>Puesto nº '. $this->position .'</small>';
        }
        echo '</div>';
        echo '</div>';
        // Botones
        echo '<div id="interaction_buttons" class="row">';
        echo '<div class="col-md-12">';
        if (!$current_user_is_editing){
            self::print_share_buttons();
/*            self::print_vote_button(True);*/
            if(($current_user_id == $this->user_id) && !(isset($_GET['edit']))){
                echo '<button id="back_to_edit" type="button" class="btn btn-default btn-block" data-contestid="'. $this->contest->id .'" data-nicename="'. $this->nice_name .'" style="margin-left: 0px;"><div class="text-center" style="min-height: 18px"><span id="">Editar mi perfil</span></div></button>';
            }
        }else{
            echo '<div style="margin-top: 10px; margin-bottom: 10px;">';
            echo '<button id="edit" type="button" class="btn btn-default btn-block" data-contestid="'. $this->contest->id .'" data-nicename="'. $this->nice_name .'"><div class="text-center" style="min-height: 18px"><img id="participate_loader" class="image-responsive" src="/wp-content/plugins/boga-contest/assets/img/Boganimation2.gif" style="width: 10%;margin: 0 auto; display: none; max-height: 18px;"><span id=""><i class="icon-eye" aria-hidden="true"></i>FINALIZAR</span></div></button>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';

        echo '<div id="toolbar" class="row form-group text-center" data-slug="'. $this->contest->slug .'" style="margin-bottom: 0px;"></div>';
        echo '</div>';
        echo '</div>';
        // FIN TARJETA


        // FIN COLUMNA IZQUIERDA


        echo '<div id="rigth_column" class="col-sm-7 col-md-7 col-lg-8">';
        // Galeria
        if(($current_user_id == $this->user_id) && !(isset($_GET['edit']))){
            echo '<div class="alert alert-success"><strong>¡Todo listo!</strong> Ya estás participando. Recuerda que puedes subir más fotos cuando quieras. ¡Comparte esta página con tus amigos para que te voten y entren en el sorteo de 50€!</div>';
        }
        self::print_contestant_gallery();
        echo '</div>';

        echo '</div>';

        echo '</div>';
        // Navegación
/*        echo '<div class="row" style="margin-top: 100px">';
        echo '<div class="col-md-12">';
        echo '<p id="bogacontest_breadcrumb"><a style="color: #444444 !important;" href="/concursos/'. $this->contest->slug .'">Bogacontest</a> / '. $this->name ;
        if (!$current_user_is_editing) {
            echo '<a id="participate" data-contestid="' . $this->contest->id . '" style="float: right; cursor: pointer; color: #444444 !important;" >Participa</a>';
        }
        echo '</p>';
        echo '</div>';
        echo '</div>';*/

        if ($current_user_id == 20 || $current_user_id == 11 || $current_user_id == 56){
            self::get_voters();
            echo '<button id="delete_contestant" class="btn-primary btn" data-id="'. $this->ID .'" data-contestantuserid="'. $this->user_id .'">BORRAR CONCURSANTE</button>';
        }
        if(!$current_user_is_editing){
            $this->contest->print_contestant_forest();
            $this->contest->print_contest_presentation();
        }

        $this->contest->print_login_register_form(0);
        if(isset($_GET['status'])) {
            if ($_GET['status'] == 'complete' && ($current_user_id == $this->user_id)){

            }
        }

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
            echo '<div id="button_upload_col" class="col-sm-6 col-md-6">';
            echo '<button id="upload_alias" type="button" class="btn ';
            if (empty($this->main_photo)){
                echo 'btn-default';
            }else{
                echo 'btn-primary';
            }
            echo ' btn-block"><i class="icon-arrow-down" aria-hidden="true"></i>Añadir más fotos</button>';
            echo '<input id="upload" type="file" class="form-control" data-nonce="'. wp_create_nonce("media-form")  .'" style="display: none;" data-contestantid="'. $this->ID .'" >';
            echo '<div id="progress_gallery_bar_container" class="progress" style="display: none;"><div id="upload_progress_gallery_bar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0;"><span id="upload_progress_gallery_bar_text" class="sr-only"></span></div></div>';
            echo '</div>';
            echo '<div id="button_delete_col" class="col-sm-6 col-md-6">';
            echo '<button id="delete" type="button" class="btn btn-default btn-block"><i class="icon-trash" aria-hidden="true"></i>Borrar foto</button>';
            echo '</div>';
            echo '</div>';
        }

        echo '<div id="gallery" class="';
        if (!$current_user_is_editing){
            echo 'contestant_grid ';
        }
        echo '">';
        if (!$current_user_is_editing){
            echo '<div class="contestant_grid-sizer col-xs-6 col-sm-6 col-md-6 col-lg-6"></div>';
        }
        if (!empty($this->photos))
        {
            $contador = 0;
            $row_counter = 1;

            foreach($this->photos as $photo)
            {
                if($photo->main == 0)
                {
                    echo '<div id="gallery_image_container_'. $photo->id .'" class="';
                    if (!$current_user_is_editing){
                        echo 'contestant_grid-item ';
                    }
                    if ($current_user_is_editing){
                        echo 'col-xs-4 col-sm-4 col-md-4 col-lg-3 gallery_container" style="padding: 1px !important; ';
                        echo 'height: 100px; overflow-y: hidden;';
                    }else{
                        echo 'col-xs-6 col-sm-6 col-md-6 col-lg-6 gallery_container" style="padding: 1px !important; ';
                    }
                    echo ' ">';
                    echo '<a class="main_photo_holder_link" href="'. $photo->path .'">';
                    echo '<img id="contestant-'. $contador .'" class="img-responsive contestant-photo" src="'. $photo->path .'" >';
                    echo '</a>';
                    echo '</div>';
                    $contador++;
                }
            }
        }else
        {
            echo '<div id="fake_photo_1" class="col-xs-4 col-sm-4 col-md-4 col-lg-3 fake_photo" style="padding: 1px !important; height: 100px; overflow-y: hidden;">';
            echo '<img id="contestant-0" class="img-responsive contestant-photo" src="/wp-content/plugins/boga-contest/assets/img/facebook-girl-avatar.png" >';
            echo '</div>';
            echo '<div id="fake_photo_2" class="col-xs-4 col-sm-4 col-md-4 col-lg-3 fake_photo" style="padding: 1px !important; height: 100px; overflow-y: hidden;">';
            echo '<img id="contestant-1" class="img-responsive contestant-photo" src="/wp-content/plugins/boga-contest/assets/img/pro_justice___facebook_no_profile_by_officialprojustice-d6zqggi.jpg" >';
            echo '</div>';
            echo '<div id="fake_photo_3" class="col-xs-4 col-sm-4 col-md-4 col-lg-3 fake_photo" style="padding: 1px !important; height: 100px; overflow-y: hidden;">';
            echo '<img id="contestant-2" class="img-responsive contestant-photo" src="/wp-content/plugins/boga-contest/assets/img/sexy_facebook_avatar_by_tesne-d3feuml.jpg" >';
            echo '</div>';
        }
        echo '</div>';
    }

    function print_main_photo()
    {
        global $current_user_is_editing;
        $button_text = '';
        $button_class = '';
        echo '<div class="row">';
        echo '<div id="gallery_image_container_'. $this->main_photo_id .'"  data-main="1">';

        // Foto principal
        if (!empty($this->main_photo))
        {
            echo '<a id="main_photo_link" class="main_photo_holder_link" href="'. $this->main_photo .'">';
            echo '<img id="main_photo" src="'. $this->main_photo .'" class="img-responsive" style="margin: 0 auto;" alt="foto principal de '. $this->name .', modelo de Bogadia">';
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
            echo '<button id="upload_main_alias" type="button" class="btn '. $button_class .' btn-block"><i class="icon-arrow-up" aria-hidden="true"></i>'. $button_text .'</button>';
            echo '<input id="upload_main" accept="image/*" type="file" class="form-control" data-nonce="'. wp_create_nonce("media-form")  .'" style="display: none;" data-contestantid="'. $this->ID .'">';
            echo '<div id="progress_bar_container" class="progress" style="display: none;"><div id="upload_progress_bar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0;"><span id="upload_progress_bar_text" class="sr-only"></span></div></div>';
        }
        echo '</div>';
        echo '</div>';
    }

    function get_voters()
    {
        global $wpdb;

        $query = 'SELECT user_email FROM wp_users INNER JOIN wp_bogacontest_votes ON wp_bogacontest_votes.voter_id=wp_users.ID WHERE contestant_id=' . $this->ID . ' GROUP BY wp_users.ID;';
        $results = $wpdb->get_results($query, OBJECT);
        echo '<h2>Votantes de '. $this->name .'</h2>';
        echo '<ul class="list-group">';
        foreach ($results as $voter) {
            echo '<li class="list-group-item"> '. $voter->user_email . ' </li>';
        }
        echo '</ul>';
    }


}