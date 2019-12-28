<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL | E_STRICT);

?>

<HTML>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" type="text/css" href="/photos/styling.css">
<title>
Galerie
</title>
<script type="text/javascript" src="/photos/gallery.js" ></script>
<script type="text/javascript"><?php 
$PAGE_SIZE = 20;
echo "function get_page_size(){ return ".$PAGE_SIZE."; }";
?></script>
</script>
</head>
<body onload="init(<?php 
if (isset($_GET["dia"]))
{
	if ($_GET["dia"] == "fwd")
	{
		echo "1";
	}
	else
	{
		echo "2";
	}
}
else
{
	echo "0";
}
?>)">
<?php
$PHOTOS_BASEPATH="/mnt/bete/02_Bilder";
$IMAGE_EXTENSIONS=array("jpg","png","jpeg");
$PHOTOS_THUMBPATH="/var/www/thumbnails";
$PAGE_SIZE = 20;
$PAGE_COLUMNS = 5;
$imgpath = $_GET["path"];
$page = $_GET["page"];
echo "<h3 id='imgpath_title'>".$imgpath."</h3>";
echo "<div><a href='/photos/index.php/".$imgpath."/../'>Zurück</a></div>";

$files_and_folders = scandir($PHOTOS_BASEPATH."/".$imgpath);
$cnt = 0;
$tablecontent = "<table>\n<tr>";
if (file_exists($PHOTOS_THUMBPATH."/".$imgpath."/rotation_settings.json"))
{
	$rot_string=file_get_contents($PHOTOS_THUMBPATH."/".$imgpath."/rotation_settings.json");
	$rot_json = json_decode($rot_string,true);
}
else
{
	$rot_json = Null;
}
foreach ($files_and_folders as $f)
{
	if(is_dir($PHOTOS_BASEPATH."/".$imgpath."/".$f)==False) {
		$pi =  strtolower(pathinfo($f, PATHINFO_EXTENSION));
		if(in_array($pi,$IMAGE_EXTENSIONS)) {
			if (floor($cnt / $PAGE_SIZE) == $page)
			{
				$index_on_page = $cnt - $page*$PAGE_SIZE;
				if ($index_on_page % $PAGE_COLUMNS == 0 && $index_on_page > 0)
				{
					$tablecontent.="</tr><tr>";
				}
				elseif ($index_on_page % $PAGE_COLUMNS == 0)
				{
					$tablecontent.="<tr>";
				}
				$f = ltrim($f,".");
				$f = ltrim($f,"_");
				if ($rot_json != Null && array_key_exists($f,$rot_json) == True)
				{
					$imgtransform = "style='transform: ".$rot_json[$f].";'";
				}
				else
				{
					$imgtransform = "";
				}
				$tablecontent.="<td class='thumbcontainer'><img src='/photos/thumbnail.php/".$imgpath."/".$f."' onclick='processImageElement(this);' class='thumb' id='thmb".$page."_".$cnt."' ".$imgtransform."></img></td>";
			}
			$cnt++;
		}

	}
}
$tablecontent.=" </tr></table>";

$n_pages = floor($cnt / $PAGE_SIZE);
 echo "<span data-mxpage='".$n_pages."' id='tot_pages'>Seite ".$page."/".$n_pages."</span>";
 if ($page > 0)
 {
	 echo "<div class='gallerynav'><a href='/photos/gallery.php?path=".$imgpath."&page=".($page-1)."'>Zurück</a></div>";
 }
 else
 {
	 echo "<div class='gallerynav'>Zurück</div>";
 }
 if ($page<$n_pages)
 {
	 echo "<div class='gallerynav'><a href='/photos/gallery.php?path=".$imgpath."&page=".($page+1)."'>Vor</a></div>";
 }
 else
 {
	 echo "<div class='gallerynav'>Vor</div>";
 }
 echo "<div class='gallerynav'><input type='checkbox' id='rotate_images' class='padded'>Bilder drehen</input></div>";
 echo $tablecontent;
 ?>
 <div id='overlay' onclick=''><div class='ol_inner' id='id_ol_inner'>
 <img id='olimage' src=''></img></div>
 </div>
<div id='ol_loading'>
<div class='ol_inner large'>Loading</div>
</div>
<div id="ol_nav">
	<img src="icon_back.png" class="icon_nav" onclick="ol_navigate_back()"/>
	<img src="icon_forward.png" class="icon_nav" onclick="ol_navigate_fw()"/>
	<img src="icon_close.png" class="icon_nav" onclick="close_overlay()"/>
</div>
</body>
</HTML>
