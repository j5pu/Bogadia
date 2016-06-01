<?php
require_once('../../../wp-load.php');
$bogacontestant->setUserId($_POST['user_id']);
$bogacontestant->setContestId($_POST['contest_id']);
echo $bogacontestant->create();