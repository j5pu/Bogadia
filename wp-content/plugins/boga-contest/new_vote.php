<?php
require_once('../../../wp-load.php');
$bogacontestant->setID($_POST['contestant_id']);
$bogacontestant->setUserId($_POST['user_id']);

$verified = get_user_meta($id, 'verified');
$fb_id = get_user_meta($id, '_fbid');


if ($verified == 1 || !empty($fb_id)){
    echo $bogacontestant->anotate_vote($_POST['voter_id']);
}else{
    echo 'Revisa tu e-mail y verifica tu cuenta';
    include 'boga-contest.php';
    $hash = get_user_meta($id, 'hash');
    $info['id'] = $_POST['voter_id'];
    $info['hash'] = $hash[0];
    bogacontest_mail_verify($info);
}



