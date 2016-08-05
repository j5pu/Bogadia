<?php
require_once('../../../wp-load.php');
$bogacontestant->setUserId($_POST['user_id']);
$bogacontestant->setContestId($_POST['contest_id']);
list($ID, $new) = $bogacontestant->get_or_create();
if ($new == 0){
    echo json_encode(array('message'=>__('¡Nos alegramos de verte de vuelta!'), 'user_id'=>$_POST['user_id'], 'contestant_id'=>$ID));
}else{
    echo json_encode(array('message'=>__('Perfecto. Te llevamos a tu perfil para que subas tus fotos.'), 'user_id'=>$_POST['user_id'], 'contestant_id'=>$ID));
}
die();