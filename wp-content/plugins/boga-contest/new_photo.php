<?php

$img = $_POST['async-upload'];
$img = str_replace('data:image/jpeg;base64,', '', $img);
$img = str_replace(' ', '+', $img);

$decoded          = base64_decode($img) ;
require_once('../../../wp-load.php');

$filename = str_replace(' ', '', $_POST['name']);
$file_path = WP_CONTENT_DIR .'/uploads/'. $filename;

file_put_contents( $file_path, $decoded);
$file             = array();
$file['error']    = '';
$file['tmp_name'] = $file_path;
$file['name']     = $filename;
$file['type']     = 'image/jpeg';
$file['size']     = filesize( $file_path);
$_FILES['async-upload'] = $file;

$bogacontestant->setID($_POST['contestant_id']);
if($_POST['main'] == 1)
{
    $bogacontestant->quit_main_photo();
}
$img_id = $bogacontestant->create_img($_POST['main'], '/wp-content/uploads/'. $filename);
echo json_encode(array('id'=>$img_id, 'url'=>'/wp-content/uploads/'. $filename));
die();