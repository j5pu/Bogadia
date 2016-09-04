<?php
require_once('../../../wp-load.php');
$bogacontestant->setID($_POST['contestant_id']);
$bogacontestant->setUserId($_POST['user_id']);
$voter_id = $_POST['voter_id'];

$verified = get_user_meta($voter_id, 'verified');
$fb_id = get_user_meta($voter_id, '_fbid');


if ($verified == 1 || !empty($fb_id)){
    echo $bogacontestant->anotate_vote($voter_id);
}else{
    echo 'Revisa tu e-mail y verifica tu cuenta';
/*    include 'boga-contest.php';
    $hash = get_user_meta($id, 'hash');
    $info['id'] = $voter_id;
    $info['hash'] = $hash[0];
    $user_info = get_userdata($info['id']);
    $info['user_email'] = $user_info->user_email;
    $info['first_name'] = $user_info->first_name;
    bogacontest_mail_verify($info);*/
}