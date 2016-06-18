<?php
require_once('../../../wp-load.php');
$bogacontestant->setUserId($_POST['user_id']);
$bogacontestant->setContestId($_POST['contest_id']);
$ID = $bogacontestant->get_or_create();
echo json_encode(array('message'=>__('Perfecto. Ya estás participando. Te llevamos a tu perfil.'), 'user_id'=>$_POST['user_id'], 'contestant_id'=>$ID));
die();