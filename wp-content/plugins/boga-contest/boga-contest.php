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
        post_id bigint(20) NULL,
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
    wp_enqueue_script('bogacontest');
    wp_register_script('bogacontest', '/wp-content/plugins/boga-contest/assets/js/bogacontest.js', array('jquery', 'typewatch'));
    wp_enqueue_script('bogacontest');
    wp_register_style('bogacontest', '/wp-content/plugins/boga-contest/assets/css/bogacontest.css');
    wp_enqueue_style('bogacontest');
    wp_enqueue_media();
}

function bogacontest_ajax_login(){
    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
    $info = array();

    $user = get_user_by('email', $_POST['email']);
    if (!empty($user)) {
        if (wp_check_password( $_POST['password'], $user->data->user_pass, $user->ID)){
            $info['user_login'] = $user->data->user_login;
            $info['user_password'] = $_POST['password'];
            $info['remember'] = true;
            $user_signon = wp_signon($info, is_ssl() ? true : false);
            if ( is_wp_error($user_signon) ){
                echo json_encode(array('loggedin'=>false, 'message'=>__('¡Upps! Ha ocurrido un error')));
            } else {
                $bogacontestant = new contestant();
                $bogacontestant->setUserId($user_signon->ID);
                $bogacontestant->setContestId($_POST['contest_id']);
                $bogacontestant->get();
                if (!empty($bogacontestant->ID)){
                    echo json_encode(array('loggedin'=>true, 'message'=>__('Hola de nuevo '. $bogacontestant->name .'. Te estamos redirigiendo a tu cuenta'), 'user_id'=>$user_signon->ID, 'contestant_id'=>$bogacontestant->ID));
                }else{
                    echo json_encode(array('loggedin'=>true, 'message'=>__('Hola de nuevo '. $user->data->display_name ), 'user_id'=>$user_signon->ID, 'contestant_id'=>$bogacontestant->ID));
                }
            }
            die();
        }else{
            echo json_encode(array('loggedin'=>false, 'message'=>__('Contraseña incorrecta. <a class="lost" href="'. wp_lostpassword_url() .'">¿Has olvidado tu contraseña?</a>')));
            die();
        }
    }else{
        echo json_encode(array('loggedin'=>false, 'message'=>__('¡Guay! Solo falta tu nombre completo')));
        die();
    }
}

function bogacontest_ajax_register(){
    global $wpdb;
    check_ajax_referer( 'ajax-register-nonce', 'security' );

    $info = array();
    $info['display_name'] = cut_title($_POST['username'], 250);
    $info['user_nicename'] = sanitize_title(cut_title($_POST['username'], 50));
    $info['user_login'] = cut_by($info['user_nicename'], '-') . '-' . cut_email(sanitize_email($_POST['email'])) . '-' . time();
    if($wpdb->get_row("SELECT user_nicename FROM wp_users WHERE user_nicename = '" . $info['user_nicename'] . "'", 'ARRAY_A')) {
        $info['user_nicename'] = $info['user_login'];
    }

    $info['nickname'] = $info['first_name'] = cut_by($info['display_name'], ' ');
    $info['user_pass'] = sanitize_text_field($_POST['password']);
    $info['user_email'] = sanitize_email( $_POST['email']);

    // Register the user
    $user_register = wp_insert_user( $info );
    if ( is_wp_error($user_register) ){
        $error  = $user_register->get_error_codes()	;

        if(in_array('empty_user_login', $error))
            echo json_encode(array('loggedin'=>false, 'message'=>__($user_register->get_error_message('empty_user_login'))));
        elseif(in_array('existing_user_login',$error))
            echo json_encode(array('loggedin'=>false, 'message'=>__('Cambia tu nombre por un mote, o modifícalo un poco por favor.')));
        elseif(in_array('existing_user_email',$error))
            echo json_encode(array('loggedin'=>false, 'message'=>__('Este e-mail ya ha sido usado')));
    } else
    {
/*        wp_new_user_notification( $user_register, wp_unslash( $info['user_pass'] ) );*/
        $login_data['user_login'] = $info['user_login'];
        $login_data['user_password'] = $info['user_pass'];
        $login_data['remember'] = true;
        $user_signon = wp_signon($login_data, is_ssl() ? true : false);
        if ( is_wp_error($user_signon) ){
            echo json_encode(array('loggedin'=>false, 'message'=>__('Upps! Te has registrado correctamente pero no hemos podido auntenticarte')));
        } else {
            echo json_encode(array('loggedin'=>true, 'message'=>__('Perfecto '. $info['nickname']  . '. Ya estás registrado.'), 'user_id'=>$user_signon->ID, 'contestant_id'=>''));
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

function bogacontest_meta(){
    global $wpdb;
    global $wp_query;
    $contest = new contest();
    $contest->get_contest_slug_from_url();

    $contestant_name_or_id = urldecode($wp_query->query_vars['contestant']);
    if (!empty($contestant_name_or_id)) {
        if (is_numeric($contestant_name_or_id)) {
            $query_lookup_field = 'wp_bogacontest_contestant.ID=' . $contestant_name_or_id;
        } else {
            $query_lookup_field = 'wp_users.user_nicename="' . $contestant_name_or_id . '"';
        }
        $results = $wpdb->get_row("SELECT wp_users.display_name, wp_users.user_nicename, wp_bogacontest_img.path as main_photo FROM wp_bogacontest_contestant INNER JOIN wp_users ON wp_bogacontest_contestant.user_id=wp_users.ID INNER JOIN wp_bogacontest_img ON wp_bogacontest_img.contestant_id=wp_bogacontest_contestant.ID INNER JOIN wp_bogacontest ON wp_bogacontest.ID=wp_bogacontest_contestant.contest_id WHERE " . $query_lookup_field . " AND wp_bogacontest.slug='" . $contest->slug . "' AND wp_bogacontest_img.main='1';", OBJECT);
        echo '<meta property="og:title" content="'. $results->display_name .' - Concursante de Bogadia" />';
        echo '<meta property="og:image" content="'. $results->main_photo .'" />';
        echo '<meta property="og:description" content="¡Necesito tu voto para ganar! Ayúdame a cumplir mi sueño." />';
        echo '<meta property="og:url" content="https://www.bogadia.com/concursos/'. $contest->slug .'/'. $contestant_name_or_id .'/">';
    }
}

register_activation_hook( __FILE__, 'bogacontest_install' );
add_filter('rewrite_rules_array','wp_insertMyRewriteRules');
add_filter('query_vars','wp_insertMyRewriteQueryVars');
add_filter('init','flushRules');
add_action( 'init', 'allow_origin' );
remove_filter('template_redirect', 'redirect_canonical'); // stop redirecting
add_shortcode( 'bogacontestant', array($bogacontestant, 'print_contestant_data') );
add_shortcode( 'bogacontest', array($bogacontest, 'print_contest_data') );
add_action('wp_enqueue_scripts', 'bogacontest_assets');
add_action( 'wp_ajax_nopriv_bogacontest_ajax_login', 'bogacontest_ajax_login' );
add_action( 'wp_ajax_nopriv_bogacontest_ajax_register', 'bogacontest_ajax_register' );
add_action( 'wp_ajax_nopriv_wp_ajax_upload_attachment', 'wp_ajax_upload_attachment' );