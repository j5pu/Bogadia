<?php
require_once('../../../wp-load.php');
$bogacontestant->setID($_POST['contestant_id']);
$bogacontestant->setUserId($_POST['user_id']);
$current_user_id = $_POST['voter_id'];
if ($current_user_id == 20 || $current_user_id == 11 || $current_user_id == 56){
    echo $bogacontestant->delete();
}
