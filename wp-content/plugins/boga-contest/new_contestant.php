<?php
require_once('../../../wp-load.php');
$bogacontestant->setUserId($_POST['user_id']);
$bogacontestant->setContestId($_POST['contest_id']);
$bogacontest->setSlug($_POST['contest_slug']);
$bogacontest->setId($_POST['contest_id']);
$bogacontestant->setContest($bogacontest);

list($ID, $new) = $bogacontestant->get_or_create();
if ($new == 0){
    $message = '¡Nos alegramos de verte de vuelta!';
}else{
    $message = 'Perfecto. Te llevamos a tu perfil para que subas tus fotos.';
}
echo json_encode(array('message'=>__($message), 'user_id'=>$_POST['user_id'], 'contestant_id'=>$ID, 'new'=>$new));
die();