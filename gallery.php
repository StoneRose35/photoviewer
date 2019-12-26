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
function init()
{
	var img_el = document.getElementById("olimage");
	img_el.addEventListener("load",function(){on_overlay_loaded(img_el);},false);
}

function processImageElement(el)
{
	if (document.getElementById("rotate_images").checked == true)
	{
		var cur_style = el.style.transform;
		var cur_src = el.src;
		var new_rot;
		cur_src = cur_src.replace(/http:\/\/.*\/photos\/thumbnail.php\//,"");
		var c_path = document.getElementById("imgpath_title").textContent;
		cur_src = cur_src.replace(c_path + "/","");
		if (cur_style == "")
		{
			new_rot = "rotate(90deg)";
		}
		else if (cur_style == "rotate(90deg)")
		{
			new_rot = "rotate(180deg)";
		}
		else if (cur_style == "rotate(180deg)")
		{
			new_rot = "rotate(270deg)";
		}
		else
		{
			new_rot = "";
		}
		el.style.transform = new_rot;
		
		const Http = new XMLHttpRequest();
		const url='/photos/rotate_image.php?path=' + c_path + "&image=" + cur_src + "&rotation=" + new_rot;
		Http.open("GET", url);
		Http.send();

		Http.onreadystatechange = (e) => {
		  console.log(Http.responseText)
		}
	}
	else
	{
		openoverlay(el);
	}
}

function openoverlay(el)
{
	var current_src = el.src;
        var olimage = document.getElementById("olimage");
        olimage.style.transform = el.style.transform;
	olimage.src=current_src.replace(/\/photos\/thumbnail.php\//,'/bilder/');
	var el_loading =  document.getElementById("ol_loading");
    	el_loading.style.padding = document.documentElement.scrollHeight/2 + "px 0";
        el_loading.style.display = "block";
}

function on_overlay_loaded(el)
{
        document.getElementById("ol_loading").style.display = "none";
	document.getElementById("overlay").style.display = "block";
        var olimage = document.getElementById("olimage");

        var ol_inner=document.getElementById("overlay");
	if (olimage.style.transform=="rotate(90deg)" || olimage.style.transform=="rotate(270deg)")
	{
        	var img_width = olimage.naturalHeight;
        	var img_height = olimage.naturalWidth;
	} 
	else
	{
       		var img_width = olimage.naturalWidth;
        	var img_height = olimage.naturalHeight;
	}
        var img_ratio = img_height/img_width;
    var c_height=ol_inner.clientHeight;
    var c_width=ol_inner.clientWidth;
    var c_img_ratio = c_height/c_width;
    if (olimage.style.transform=="rotate(90deg)" || olimage.style.transform=="rotate(270deg)")
    {
	olimage.style.transformOrigin = "left";
	olimage.style.transform = "translate(50%, -50%) " + olimage.style.transform;
    if (img_ratio > c_img_ratio)
    {
        olimage.style.width = c_height;
        olimage.style.height = c_height/img_ratio;

    }
    else
    {   
        olimage.style.height = c_width;
        olimage.style.width = c_width/img_ratio;
    }

	}
    else
    {
            olimage.style.transformOrigin = "inherit";
	    //olimage.style.transform = el.style.transform;
	    if (img_ratio > c_img_ratio)
	    {
		olimage.style.height = c_height;
		olimage.style.width = c_height/img_ratio;
	    }
	    else
	    {   
		olimage.style.width = c_width;
		olimage.style.height = c_width*img_ratio;
	    }
    }

}

function close_overlay()
{
	document.getElementById("overlay").style.display = "none";
}
</script>
</head>
<body onload="init()">
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
				$tablecontent.="<td class='thumbcontainer'><img src='/photos/thumbnail.php/".$imgpath."/".$f."' onclick='processImageElement(this);' class='thumb' ".$imgtransform."></img></td>";
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
 echo "<div class='gallerynav'><input type='checkbox' id='rotate_images' class='padded'>Bilder drehen</input><button class='padded'>Diashow</button></div>";
 echo $tablecontent;
 ?>
 <div id='overlay' onclick='close_overlay();'><div class='ol_inner' id='id_ol_inner'>
 <img id='olimage' src='' onload=""></img></div>
 </div>
<div id='ol_loading'>
<div class='ol_inner large'>Loading</div>
</div>
</body>
</HTML>
