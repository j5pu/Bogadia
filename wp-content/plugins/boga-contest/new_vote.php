<?php
require_once('../../../wp-load.php');
$bogacontestant->setID($_POST['contestant_id']);
$bogacontestant->setUserId($_POST['user_id']);
$voter_id = $_POST['voter_id'];

$verified = get_user_meta($voter_id, 'verified');
$fb_id = get_user_meta($voter_id, '_fbid');


if ($verified[0] == '1' || !empty($fb_id)){
    echo $bogacontestant->anotate_vote($voter_id);
}else{

    $sended_mail = get_user_meta($voter_id, 'send_mail_verify');

    if ($sended_mail[0] = '0' || empty($sended_mail)){
        echo 'Te hemos enviado un mail. Revisa tu correo.';

        $hash = get_user_meta($voter_id, 'hash');
        $info['id'] = $voter_id;
        $info['hash'] = $hash[0];
        $user_info = get_userdata($voter_id);
        $info['user_email'] = $user_info->user_email;
        $info['first_name'] = $user_info->first_name;
        bogacontest_mail_verify($info);
    }else{
        echo 'Revisa tu correo y verifica tu cuenta';
    }
}