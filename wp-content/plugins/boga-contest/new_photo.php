<?php

$img = $_POST['async-upload'];
$img = str_replace('data:image/jpeg;base64,', '', $img);
$img = str_replace(' ', '+', $img);

$decoded          = base64_decode($img) ;
require_once('../../../wp-load.php');

$filename = str_replace(' ', '', $_POST['name']);

$filename  = md5( $filename . microtime() );


$contestant_folder = WP_CONTENT_DIR .'/uploads/bogacontest/'. $_POST['contest_slug'] .'/'. $_POST['contestant_id'];
$path = '/wp-content/uploads/bogacontest/'. $_POST['contest_slug'] .'/'. $_POST['contestant_id'] .'/'. $filename;
if (!file_exists($contestant_folder)) {
    mkdir($contestant_folder, 0777, true);
}
$file_path =  $contestant_folder .'/'. $filename;

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
$img_id = $bogacontestant->create_img($_POST['main'], $path);
echo json_encode(array('id'=>$img_id, 'url'=>$path));
die();