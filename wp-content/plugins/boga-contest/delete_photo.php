<?php
require_once('../../../wp-load.php');
$bogacontestant->setID($_POST['contestant_id']);
echo $bogacontestant->delete_img(intval($_POST['post_id']));