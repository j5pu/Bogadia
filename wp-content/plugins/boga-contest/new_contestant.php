<?php
require_once('../../../wp-load.php');
$bogacontestant->setUserId($_POST['user_id']);
$bogacontestant->setContestId($_POST['contest_id']);
$bogacontest->setSlug($_POST['contest_slug']);
$bogacontest->setId($_POST['contest_id']);
$bogacontestant->setContest($bogacontest);

list($ID, $new) = $bogacontestant->get_or_create();
if ($new == 0){
    echo json_encode(array('message'=>__('¡Nos alegramos de verte de vuelta!'), 'user_id'=>$_POST['user_id'], 'contestant_id'=>$ID));
}else{
    echo json_encode(array('message'=>__('Perfecto. Te llevamos a tu perfil para que subas tus fotos.'), 'user_id'=>$_POST['user_id'], 'contestant_id'=>$ID));
}
die();