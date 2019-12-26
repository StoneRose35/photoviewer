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
Vollbild
</title>
</head>
<body>
<?php
$PHOTOS_BASEPATH="/mnt/bete/02_Bilder";
$PHOTOS_THUMBPATH="/var/www/thumbnails";
$IMAGE_EXTENSIONS=array("jpg","png","jpeg");
$basepath = str_replace("/photos/fullscreen.php","",urldecode($_SERVER['REQUEST_URI']));
echo "<img class='fs' src='/bilder".$basepath."'></img>";
?>
</body>
</HTML>