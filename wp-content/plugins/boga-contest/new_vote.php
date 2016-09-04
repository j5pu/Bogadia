<?php
require_once('../../../wp-load.php');
$bogacontestant->setID($_POST['contestant_id']);
$bogacontestant->setUserId($_POST['user_id']);
$voter_id = $_POST['voter_id'];
echo $bogacontestant->anotate_vote($voter_id);

$verified = get_user_meta($voter_id, 'verified');
$fb_id = get_user_meta($voter_id, '_fbid');


if ($verified[0] == '1' || !empty($fb_id)){
}else{

    $sended_mail = get_user_meta($voter_id, 'send_mail_verify');

    if ($sended_mail[0] = '0' || empty($sended_mail)){
        echo 'Te hemos enviado un mail. Revisa tu correo.';

        $info['id'] = $voter_id;
        $user_info = get_userdata($voter_id);
        $info['user_email'] = $user_info->user_email;
        $info['first_name'] = $user_info->first_name;
        $info['hash'] = $hash[0];
        $hash = get_user_meta($voter_id, 'hash');
        bogacontest_mail_verify($info);
    }else{
        echo 'Revisa tu correo y verifica tu cuenta';
    }
}