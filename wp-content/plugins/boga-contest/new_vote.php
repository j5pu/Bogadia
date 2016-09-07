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
/*        echo '. Te hemos enviado un mail';*/

        $info['id'] = $voter_id;
        $user_info = get_userdata($voter_id);
        $info['user_email'] = $user_info->user_email;
        $info['first_name'] = $user_info->first_name;
        $hash = get_user_meta($voter_id, 'hash');
        if (empty($hash[0])){
            $info['hash'] = md5( $user_info->user_nicename. microtime() );
            add_user_meta( $info['id'], 'hash', $info['hash'] );
            add_user_meta( $info['id'], 'verified', '0' );
        }else{
            $info['hash'] = $hash[0];
        }
        bogacontest_mail_verify($info);
    }else{
/*        echo '. Recuerda verificar tu cuenta';*/
    }
}