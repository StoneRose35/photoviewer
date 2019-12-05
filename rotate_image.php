<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);



$THUMB_SIZE = 196;
$PHOTOS_BASEPATH="/mnt/bete/02_Bilder";
$PHOTOS_THUMBPATH="/var/www/thumbnails";
$IMAGE_EXTENSIONS=array("jpg","png","jpeg");
$basepath = str_replace("/photos/rotate_image.php","",urldecode($_SERVER['REQUEST_URI']));
$rotation_fname = $PHOTOS_THUMBPATH."/".$_GET["path"]."/rotation_settings.json";

if (file_exists($rotation_fname)==True)
{
	$rot_string=file_get_contents($rotation_fname);
	$rot_json = json_decode($rot_string,true);
}
else
{
	$rot_json=array();
}
$rot_json[$_GET["image"]] = $_GET["rotation"];
file_put_contents($rotation_fname, json_encode($rot_json));
header('Content-type: application/json');
echo "{'result': 'success'}";

?>