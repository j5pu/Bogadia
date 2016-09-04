<?php
require_once('../../../wp-load.php');

$id = $_GET['id'];

$hash = get_user_meta($id, 'hash');

get_header();
get_template_part( 'page-parts/general-title-section' );

get_template_part( 'page-parts/general-before-wrap' );

if ($hash[0] == $_GET['hash']){
    update_user_meta($id, 'verified', '1');
    echo '<img class="img-responsive" style="margin:0 auto;" src="assets/img/sonrisa.png">';
    echo '<h3 class="text-center">Cuenta verificada. Ya puedes votar las veces que quieras. Muchas gracias</h3>';

}else{
    echo '<img class="img-responsive" style="margin:0 auto;" src="assets/img/sad.png">';
    echo '<h3 class="text-center">¡Que raro! No se ha podido verificar tu cuenta. Ponte en contacto enviando un mail a info@bogadia.com</h3>';

}
posts_home();
get_template_part('page-parts/general-after-wrap');
get_footer();

