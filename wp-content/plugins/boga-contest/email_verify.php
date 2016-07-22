<?php
require_once('../../../wp-load.php');

$id = $_GET['id'];

$hash = get_user_meta($id, 'hash');

if ($hash == $_GET['hash']){
    update_user_meta($id, 'verified', '1');
    echo 'Cuenta verificada. ¡Muchas gracias!';
}else{
    echo '¡Que raro! No se ha podido verificar tu cuenta. Ponte en contacto enviando un mail a info@bogadia.com';
}
