<?php
require_once('../../../wp-load.php');
$bogacontestant->setID($_POST['contestant_id']);
if($_POST['main'] == 1)
{
    $bogacontestant->quit_main_photo();
}
echo $bogacontestant->create_img($_POST['main'], $_POST['path'], intval($_POST['post_id']));