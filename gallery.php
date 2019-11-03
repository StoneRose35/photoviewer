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
<script type="text/javascript">
function openoverlay(el)
{
	document.getElementById("overlay").style.display = "block";
	var current_src = el.src;
	document.getElementById("olimage").src=current_src.replace(/\/photos\/thumbnail.php\//,'/bilder/');
}

function close_overlay()
{
	document.getElementById("overlay").style.display = "none";
}
</script>
</head>
<body>
<?php
$PHOTOS_BASEPATH="/mnt/bete/02_Bilder";
$IMAGE_EXTENSIONS=array("jpg","png","jpeg");
$PAGE_SIZE = 20;
$PAGE_COLUMNS = 5;
$imgpath = $_GET["path"];
$page = $_GET["page"];
echo "<h3>".$imgpath."</h3>";
echo "<div><a href='/photos/index.php/".$imgpath."/../'>Zurück</a></div>";

$files_and_folders = scandir($PHOTOS_BASEPATH."/".$imgpath);
$cnt = 0;
$tablecontent = "<table>\n<tr>";
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
				$tablecontent.="<td><img src='/photos/thumbnail.php/".$imgpath."/".$f."' onclick='openoverlay(this);' class='thumb'></img></td>";
			}
			$cnt++;
		}

	}
}
$tablecontent.=" </tr></table>";

$n_pages = floor($cnt / $PAGE_SIZE);
 echo "<span>Seite ".$page."/".$n_pages."</span>";
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
 echo $tablecontent;
 ?>
 <div id='overlay' onclick='close_overlay();'><div class='ol_inner'>
 <img id='olimage' class='fs' src=''></div></img>
 </div>
</body>
</HTML>