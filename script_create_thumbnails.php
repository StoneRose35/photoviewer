
<?php
$THUMB_SIZE = 196;
$PHOTOS_BASEPATH="/mnt/bete/02_Bilder";
$PHOTOS_THUMBPATH="/var/www/thumbnails";
$IMAGE_EXTENSIONS=array("jpg","png","jpeg");


function createthumbnail($basepath)
{
	global $PHOTOS_THUMBPATH;
	global $PHOTOS_BASEPATH;
	global $THUMB_SIZE;
	global $IMAGE_EXTENSIONS;
	
	try
	{
		$thumbpath = $PHOTOS_THUMBPATH."/".$basepath;
		if (file_exists($thumbpath))
		{// send cached image
			return 0;
		}
		else
		{

			list($width, $height) = getimagesize($PHOTOS_BASEPATH."/".$basepath);

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
			if ($img){
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
				return 1;
			}
		}
	}
	catch (Exception $e)
	{
		throw $e;
	}
}

function create_thumbnailfolder($imgpath)
{
	global $PHOTOS_THUMBPATH;
	global $PHOTOS_BASEPATH;
	global $THUMB_SIZE;
	global $IMAGE_EXTENSIONS;
	if (empty($imgpath)==true)
	{
		$cur_base = $PHOTOS_BASEPATH;
	}
	else
	{
		$cur_base = $PHOTOS_BASEPATH."/".$imgpath;
	}
	$files_and_folders = scandir($cur_base);
	print "Scanning ".$cur_base."\n";
	$n_thumbs = 0;
	foreach ($files_and_folders as $f)
	{
		if(is_dir($cur_base."/".$f)==false)
		{
			$pi =  strtolower(pathinfo($f, PATHINFO_EXTENSION));
			if(in_array($pi,$IMAGE_EXTENSIONS)) 
			{
				try {
					$f = ltrim($f,".");
					$f = ltrim($f,"_");
					$n_thumbs += createthumbnail($imgpath."/".$f);
				}
				catch (Exception $e)
				{
					print("Error creating ".$imgpath."/".$f."\n");
				}
			}
		}
		elseif ($f != ".." && $f != ".")
		{
			if (empty($imgpath)==true)
			{
				if(file_exists($PHOTOS_THUMBPATH."/".$f)==false)
				{
					mkdir($PHOTOS_THUMBPATH."/".$f);
				}
                                create_thumbnailfolder($f);
			}
			else
			{
				if(file_exists($PHOTOS_THUMBPATH."/".$imgpath."/".$f)==false)
				{
					mkdir($PHOTOS_THUMBPATH."/".$imgpath."/".$f);
				}
				create_thumbnailfolder($imgpath."/".$f);
				
			}
		}
	}
	print "created ".$n_thumbs." thumbnails\n";
}

create_thumbnailfolder("");

?>
