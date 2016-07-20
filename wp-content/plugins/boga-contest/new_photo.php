<?php

$img = $_POST['async-upload'];
$img = str_replace('data:image/jpeg;base64,', '', $img);
$img = str_replace(' ', '+', $img);

$decoded          = base64_decode($img) ;

file_put_contents('../wp-content/'. $_POST['name'], $decoded);
require_once('../../../wp-load.php');
$file             = array();
$file['error']    = '';
$file['tmp_name'] = WP_CONTENT_DIR .'/'. $_POST['name'];
$file['name']     = $_POST['name'];
$file['type']     = 'image/jpeg';
$file['size']     = filesize( WP_CONTENT_DIR .'/'. $_POST['name']);
$_FILES['async-upload'] = $file;

$bogacontestant->setID($_POST['contestant_id']);
if($_POST['main'] == 1)
{
    $bogacontestant->quit_main_photo();
}
$img_id = $bogacontestant->create_img($_POST['main'], '/wp-content/'. $_POST['name']);
echo json_encode(array('id'=>$img_id, 'url'=>'/wp-content/'. $_POST['name']));
die();