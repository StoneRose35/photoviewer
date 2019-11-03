<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);


function list_files($basepath,$do_recurse) {
	$PHOTOS_BASEPATH="/mnt/bete/02_Bilder";
	$IMAGE_EXTENSIONS=array("jpg","png","jpeg");
	$VIDEO_EXTENSIONS=array("mov","mp4","avi");
	$photo_cntr = 0;
	$video_cntr = 0;
	$other_cntr = 0;
	
	$basepath = str_replace("/photos/","",$_SERVER['REQUEST_URI']);
	$basepath = str_replace("index.php","",$basepath);
	$basepath = trim($basepath, "/");
	
	$files_and_folders = scandir($PHOTOS_BASEPATH."/".$basepath);
	$cnt = 0;
	echo "<h3>".$basepath."</h3>";
	if ($basepath != "") {
		echo "<div class='folderbox odd'><div class='folder'><a href='/photos/index.php/".$basepath."/../'>..</a></div>";
		echo "</div>";
	}
	foreach ($files_and_folders as $f)
	{
		if(is_dir($PHOTOS_BASEPATH."/".$basepath."/".$f)==False) {

		}
		elseif ($f != "." && $f != ".." && substr($f,0,1) != "." && $do_recurse == True) 
		{
			$newbase = $basepath . "/" . $f;
			list_files($newbase);
		}

		else {
			
			$photo_cntr = 0;
			$video_cntr = 0;
			$other_cntr = 0;
	
			$folderfiles = scandir($PHOTOS_BASEPATH."/".$basepath."/".$f);
			foreach($folderfiles as $f2)
			{
				$pi =  strtolower(pathinfo($f2, PATHINFO_EXTENSION));
				if(in_array($pi,$IMAGE_EXTENSIONS)) {
					$photo_cntr++;
				}
				elseif(in_array($pi,$VIDEO_EXTENSIONS))
				{
					$video_cntr++;
				}
				else
				{
					$other_cntr++;
				}
			}
			
			if ($cnt % 2 == 0)
			{
				$bgclass = "even";
			}
			else
			{
				$bgclass = "odd";
			}
			if ($f != "." && $f != ".." && substr($f,0,1) != ".") {
				if ($basepath != "")
				{
					$basepath_link = $basepath . "/";
				}
				else
				{
					$basepath_link = $basepath;
				}
				echo "<div class='folderbox ".$bgclass."'><div class='folder'><a href='/photos/index.php/".$basepath_link.$f."'>".$f."</a></div><div class='navright'><div class='gallerylink'><a href='/photos/gallery.php?path=".$basepath_link.$f."&page=0'>Galerie</a></div>";
				echo "<div class='descr'>".$photo_cntr." Photos, ".$video_cntr." Videos, ".$other_cntr." Anderes</div></div></div>";
			}
			$cnt++;
		}
		//}
	}

	return 0;
}


?>

<HTML>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" type="text/css" href="/photos/styling.css">
<title>
<?php
echo "Photoviewer";
?>
</title>
</head>
<body>
<?php
list_files("",False);
 ?>
</body>
</HTML>