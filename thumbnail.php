<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);



$THUMB_SIZE = 196;
$PHOTOS_BASEPATH="/mnt/bete/02_Bilder";
$PHOTOS_THUMBPATH="/var/www/thumbnails";
$IMAGE_EXTENSIONS=array("jpg","png","jpeg");
$basepath = str_replace("/photos/thumbnail.php","",urldecode($_SERVER['REQUEST_URI']));
$thumbpath = $PHOTOS_THUMBPATH."/".$basepath;

if (file_exists($thumbpath))
{// send cached image
	$img = imagecreatefromjpeg ($thumbpath);
	header('Content-type: image/jpeg');
	imagejpeg($img, null, 100);
	
}
else
{
	try {
		list($width, $height) = getimagesize($PHOTOS_BASEPATH."/".$basepath);
	}
	catch (Exception $e)
	{
		echo "Fehler beim Lesen von ".$PHOTOS_BASEPATH."/".$basepath;
	}

	if ($width > $height)
	{
		$newwidth = $THUMB_SIZE;
		$newheight = $height*$THUMB_SIZE/$width;
	}
	else
	{
		$newheight = $THUMB_SIZE;
		$newwidth = $width*$THUMB_SIZE/$height;
	}

	$create = imagecreatetruecolor($newwidth, $newheight); 
	$img = @imagecreatefromjpeg($PHOTOS_BASEPATH."/".$basepath); 
	
	if ($img)
	{
		imagecopyresampled($create, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		
		$thumb_folders = explode("/",$thumbpath);
		$cur_path = "";
		$k = 0;
		for ($k = 0; $k < count($thumb_folders)-1; $k++)
		{
			$cur_path .= "/".$thumb_folders[$k];
			if (file_exists($cur_path)==false)
			{
				mkdir($cur_path);
			}
		}
		imagejpeg($create, $thumbpath, 100);
		
		header('Content-type: image/jpeg');
		imagejpeg($create, null, 100); 
	}
	else
	{
		echo "Fehler beim Erstellen von ".$PHOTOS_BASEPATH."/".$basepath;
	}
}

?>