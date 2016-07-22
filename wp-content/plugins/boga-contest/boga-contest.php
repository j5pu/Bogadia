<?php
/*
Plugin Name: Bogacontest
Description: Concurso de modelos
*/

include 'class/contestant.php';
$bogacontestant = new contestant();
$bogacontest = new contest();

function bogacontest_install() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE wp_bogacontest_contestant (
        id int UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id bigint(45) NULL,
        contest_id varchar(45) NULL,
        date datetime NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY id (id ASC)
	) $charset_collate;

	CREATE TABLE wp_bogacontest_img (
        id int UNSIGNED NOT NULL AUTO_INCREMENT,
        contestant_id int UNSIGNED NULL,
        main int NULL,
        path varchar(500) NULL,
        date datetime NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY id (id ASC)
	) $charset_collate;

	CREATE TABLE wp_bogacontest_votes(
        id int UNSIGNED NOT NULL AUTO_INCREMENT,
        contestant_id bigint(20) UNSIGNED NULL,
        voter_id bigint(20) NULL,
        date datetime NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY id (id ASC)
	) $charset_collate;

	CREATE TABLE wp_bogacontest(
        id int UNSIGNED NOT NULL AUTO_INCREMENT,
        slug varchar(100) NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY id (id ASC)
	) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    add_role( 'BogaContestant', 'Boga Contestant', array( 'upload_files' => true ) );
    if (!file_exists(WP_CONTENT_DIR .'/uploads/bogacontest')) {
        mkdir(WP_CONTENT_DIR .'/uploads/bogacontest', 0777, true);
    }
}

function flushRules(){
    // Remember to flush_rules() when adding rules
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}

function wp_insertMyRewriteRules($rules)
{
    // Adding a new rule
    $newrules = array();
    $newrules['concursos/([^/]+)/(.+)'] = 'index.php?pagename=bogacontestant&contest=$matches[1]&contestant=$matches[2]';
    $newrules['concursos/([^/]+)'] = 'index.php?pagename=bogacontest&contest=$matches[1]';
    $finalrules = $newrules + $rules;
    return $finalrules;
}

function wp_insertMyRewriteQueryVars($vars)
{
    // Adding the var so that WP recognizes it
    array_push($vars, 'contest');
    array_push($vars, 'contestant');
    return $vars;
}

function bogacontest_assets(){
    wp_register_script('typewatch', '/wp-content/plugins/boga-contest/assets/js/typewatch.js', array('jquery'));
    wp_enqueue_script('typewatch');
    wp_register_script('load_img', '/wp-content/plugins/boga-contest/assets/js/load-image.all.min.js');
    wp_enqueue_script('load_img');
/*    wp_register_script('masonry', '/wp-content/plugins/boga-contest/assets/js/masonry.pkgd.min.js', array('jquery'));
    wp_enqueue_script('masonry');*/
    wp_register_script('velocity', '/wp-content/plugins/boga-contest/assets/js/velocity.min.js', array('jquery'));
    wp_enqueue_script('velocity');
    wp_register_script('velocityui', '/wp-content/plugins/boga-contest/assets/js/velocity.ui.js', array('velocity'));
    wp_enqueue_script('velocityui');
    wp_register_script('bogacontest', '/wp-content/plugins/boga-contest/assets/js/bogacontest.js', array('jquery', 'typewatch', 'load_img', 'velocity', 'velocityui'));
    wp_enqueue_script('bogacontest');
    wp_register_style('bogacontest', '/wp-content/plugins/boga-contest/assets/css/bogacontest.css');
    wp_enqueue_style('bogacontest');
    wp_enqueue_media();
}

function bogacontest_ajax_login()
{
    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
    $info = array();

    $user = get_user_by('email', $_POST['email']);

    if (!empty($user))
    {
        if (wp_check_password( $_POST['password'], $user->data->user_pass, $user->ID))
        {
            $info['user_login'] = $user->data->user_login;
            $info['user_password'] = $_POST['password'];
            $info['remember'] = true;
            $user_signon = wp_signon($info, is_ssl() ? true : false);

            if ( is_wp_error($user_signon) )
            {
                echo json_encode(array('loggedin'=>false, 'case'=>1, 'message'=>__('¡Upps! Ha ocurrido un error')));
            } else
            {
                $bogacontestant = new contestant();
                $bogacontestant->setUserId($user_signon->ID);
                $bogacontestant->setContestId($_POST['contest_id']);
                $bogacontestant->get();

                if (!empty($bogacontestant->ID))
                {
                    echo json_encode(array('loggedin'=>true, 'case'=>8, 'message'=>__('Hola de nuevo '. $bogacontestant->name .'. Te estamos redirigiendo a tu cuenta'), 'user_id'=>$user_signon->ID, 'contestant_id'=>$bogacontestant->ID));
                }else
                {
                    echo json_encode(array('loggedin'=>true, 'case'=>9, 'message'=>__('Hola de nuevo '. $user->data->display_name ), 'user_id'=>$user_signon->ID, 'contestant_id'=>$bogacontestant->ID));
                }
            }
            die();
        }else
        {
            echo json_encode(array('loggedin'=>false, 'case'=>2, 'message'=>__('Contraseña incorrecta. <a class="lost" style="color: white;" href="'. wp_lostpassword_url() .'">¿Has olvidado tu contraseña?</a>')));
            die();
        }
    }else
    {
        echo json_encode(array('loggedin'=>false, 'case'=>0, 'message'=>__('¡Guay! Solo falta tu nombre completo')));
        die();
    }
}

function bogacontest_ajax_register()
{
    global $wpdb;
    check_ajax_referer( 'ajax-register-nonce', 'security' );

    $info = array();
    $info['display_name'] = cut_title($_POST['username'], 250);
    $info['user_nicename'] = sanitize_title(cut_title($_POST['username'], 50));
    $info['user_login'] = cut_by($info['user_nicename'], '-') . '-' . cut_email(sanitize_email($_POST['email'])) . '-' . time();

    if($wpdb->get_row("SELECT user_nicename FROM wp_users WHERE user_nicename = '" . $info['user_nicename'] . "'", 'ARRAY_A'))
    {
        $info['user_nicename'] = $info['user_login'];
    }

    $info['nickname'] = $info['first_name'] = cut_by($info['display_name'], ' ');
    $info['user_pass'] = sanitize_text_field($_POST['password']);
    $info['user_email'] = sanitize_email( $_POST['email']);

    // Register the user
    $user_register = wp_insert_user( $info );

    if ( is_wp_error($user_register) )
    {
        $error  = $user_register->get_error_codes()	;

        if(in_array('empty_user_login', $error))
            echo json_encode(array('loggedin'=>false, 'case'=>3, 'message'=>__($user_register->get_error_message('empty_user_login'))));
        elseif(in_array('existing_user_login',$error))
            echo json_encode(array('loggedin'=>false, 'case'=>4, 'message'=>__('Cambia tu nombre por un mote, o modifícalo un poco por favor.')));
        elseif(in_array('existing_user_email',$error))
            echo json_encode(array('loggedin'=>false, 'case'=>5, 'message'=>__('Este e-mail ya ha sido usado')));
    } else
    {
        $info['id'] = $user_register;
        $info['hash'] = md5( $info['user_pass'] . microtime() );
        add_user_meta( $info['id'], 'hash', $info['hash'] );
        add_user_meta( $info['id'], 'verified', '0' );
        bogacontest_mail_verify($info);
        $login_data['user_login'] = $info['user_login'];
        $login_data['user_password'] = $info['user_pass'];
        $login_data['remember'] = true;
        $user_signon = wp_signon($login_data, is_ssl() ? true : false);

        if ( is_wp_error($user_signon) )
        {
            echo json_encode(array('loggedin'=>false, 'case'=>6, 'message'=>__('Upps! Te has registrado correctamente pero no hemos podido auntenticarte')));
        } else
        {
            echo json_encode(array('loggedin'=>true, 'case'=>7, 'message'=>__('Perfecto '. $info['nickname']  . '. Ya estás registrado.'), 'user_id'=>$user_signon->ID, 'contestant_id'=>''));
        }
    }
    die();
}

function cut_title($title, $limit){
    if ( mb_strlen( $title, 'utf8' ) > $limit ) {
        $last_space = strrpos( substr( $title, 0, $limit ), ' ' );
        return substr( $title, 0, $last_space );
    }
    return $title;
}

function cut_email($email){
    $arroba_position = strrpos( $email, '@' );
    return substr( $email, 0, $arroba_position );
}

function cut_by($string ,$letter){
    $position = strrpos( $string, $letter );
    return substr( $string, 0, $position );
}

function allow_origin() {
    header("Access-Control-Allow-Origin: *");
}

function bogacontest_meta_canonical($string){
    global $contest;
    if (!empty($contest->slug))
    {
        global $contestant_name_or_id;

        if (!empty($contestant_name_or_id))
        {
            global $meta_data_container;
            return 'https://www.bogadia.com/concursos/' . $contest->slug . '/' . $meta_data_container->user_nicename . '/';
        }else{
            return 'https://www.bogadia.com/concursos/' . $contest->slug . '/';
        }
    }else{
        return $string;
    }
}

function bogacontest_meta_title($string)
{
    global $contest;
    $contest = new contest();
    $contest->get_contest_slug_from_url();

    if (!empty($contest->slug))
    {
        global $wpdb;
        global $wp_query;
        global $meta_data_container;
        global $contestant_name_or_id;
        $contestant_name_or_id = urldecode($wp_query->query_vars['contestant']);

        if (!empty($contestant_name_or_id))
        {
            if (is_numeric($contestant_name_or_id))
            {
                $query_lookup_field = 'wp_bogacontest_contestant.ID=' . $contestant_name_or_id;
            } else
            {
                $query_lookup_field = 'wp_users.user_nicename="' . $contestant_name_or_id . '"';
            }

            $meta_data_container = $wpdb->get_row("SELECT wp_users.display_name, wp_users.user_nicename, wp_bogacontest_img.path AS main_photo FROM wp_bogacontest_contestant LEFT JOIN wp_users ON wp_bogacontest_contestant.user_id=wp_users.ID LEFT JOIN wp_bogacontest_img ON wp_bogacontest_img.contestant_id=wp_bogacontest_contestant.ID LEFT JOIN wp_bogacontest ON wp_bogacontest.ID=wp_bogacontest_contestant.contest_id WHERE " . $query_lookup_field . " AND wp_bogacontest.ID='" . $contest->id . "' AND wp_bogacontest_img.main='1';", OBJECT);
            return $meta_data_container->display_name .' - Aspirante a portada de Bogadia';
        }else
        {
            return 'Concurso de modelos - Portada de Bogadia';
        }
    } else
    {
        return $string;
    }
}

function bogacontest_meta_img($string){
    global $contest;
    if (!empty($contest->slug))
    {
        global $contestant_name_or_id;

        if (!empty($contestant_name_or_id))
        {
            global $meta_data_container;
            return $meta_data_container->main_photo ;
        }else{
            return 'https://www.bogadia.com/wp-content/uploads/2016/05/logo_final_negro-2.png';
        }
    }else{
        return $string;
    }
}

function bogacontest_meta_description($string){
    global $contest;
    if (!empty($contest->slug))
    {
        global $contestant_name_or_id;

        if (!empty($contestant_name_or_id))
        {
            global $meta_data_container;
            return '¡Necesito tu voto para ganar! Ayúdame a ser portada de Bogadia' ;
        }else{
            return 'Entra de lleno en el mundo de la moda participando en BogaContest, el primer concurso de modelos para gente como tú.';
        }
    }else{
        return $string;
    }
}

function bogacontest_sitemap_index() {
    $xml = "";

    $url = get_bloginfo( 'url' ) . '/wp-content/plugins/boga-contest/bogacontest_sitemap.php';

    // http://wordpress.stackexchange.com/a/188461
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    $path = get_home_path() . '/wp-content/plugins/boga-contest/bogacontest_sitemap.php';

    // http://wordpress.stackexchange.com/a/8404
    date_default_timezone_set( get_option('timezone_string') );
    $lastmod = date( 'c', filemtime( $path ) );

    $xml .= '<sitemap>' . "\n";
    $xml .= '<loc>' . $url . '</loc>' . "\n";
    $xml .= '<lastmod>' .  $lastmod . '</lastmod>' . "\n";
    $xml .= '</sitemap>' . "\n";

    return $xml;
}

function bogacontest_mail_verify($info)
{
    $user_name = cut_title($info['display_name'], 5);

    $email_subject = "Verifica tu cuenta " . $user_name . "!";

    ob_start();
    ?>

    <p>Buenas, <?php echo $user_name ?>. Gracias por registrarte en Bogadia</p>

    <p>
        Necesitamos que verifiques tu cuenta. <a href="https://www.bogadia.com/wp-content/plugins/boga-contest/email_verify.php?id=<?php echo $info['id'] ?>&hash=<?php echo $info['hash'] ?>>Solo tienes que pulsar aquí.</a>
    </p>

    <p>
        Tu contraseña es <strong style="color:orange"><?php echo $info['user_pass'] ?></strong> <br>
    </p>

    <p>¡Disfruta de bogadia.com! Gracias</p>

    <?php
    $message = ob_get_contents();
    ob_end_clean();

    wp_mail($user_email, $email_subject, $message);
}

function bogacontest_new_user_mail($info)
{
    $user_name = cut_title($info['display_name'], 5);

    $email_subject = "Verifica tu cuenta " . $user_name . "!";

    ob_start();
    ?>

    <p>Buenas, <?php echo $user_name ?>. Gracias por registrarte en Bogadia</p>

    <p>
        Necesitamos que verifiques tu cuenta. <a href="https://www.bogadia.com/wp-content/plugins/boga-contest/email_verify/?id=">Solo tienes que pulsar aquí.</a>
    </p>

    <p>
        Tu contraseña es <strong style="color:orange"><?php echo $info['user_pass'] ?></strong> <br>
    </p>

    <p>¡Disfruta de bogadia.com! Gracias</p>

    <?php
    $message = ob_get_contents();
    ob_end_clean();

    wp_mail($user_email, $email_subject, $message);
}


add_filter( 'wpseo_sitemap_index', 'bogacontest_sitemap_index' );
register_activation_hook( __FILE__, 'bogacontest_install' );
add_filter('rewrite_rules_array','wp_insertMyRewriteRules');
add_filter('query_vars','wp_insertMyRewriteQueryVars');
add_filter('init','flushRules');
add_filter( 'wpseo_canonical', 'bogacontest_meta_canonical' );
add_filter( 'wpseo_title', 'bogacontest_meta_title' );
add_filter( 'wpseo_opengraph_image', 'bogacontest_meta_img' );
add_filter( 'wpseo_metadesc', 'bogacontest_meta_description' );
remove_filter('template_redirect', 'redirect_canonical'); // stop redirecting
add_shortcode( 'bogacontestant', array($bogacontestant, 'print_contestant_page') );
add_shortcode( 'bogacontest', array($bogacontest, 'print_contest_page') );
add_action('wp_enqueue_scripts', 'bogacontest_assets');
add_action( 'wp_ajax_nopriv_bogacontest_ajax_login', 'bogacontest_ajax_login' );
add_action( 'wp_ajax_nopriv_bogacontest_ajax_register', 'bogacontest_ajax_register' );
add_action( 'wp_ajax_nopriv_wp_ajax_upload_attachment', 'wp_ajax_upload_attachment' );
add_action( 'init', 'allow_origin' );