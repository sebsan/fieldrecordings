<?php
// Sounds of Europe
// texture

require_once('RugImage.php');

$vrect = array();
$vrect['width'] = $_GET['w'];
$vrect['height'] = $_GET['h'];
$center['x'] = $_GET['cx'];
$center['y'] = $_GET['cy'];

$ri = new RugImage($vrect);
$img = $ri->CenterOn($center);
header('Content-Type: image/png');
readfile($img);


?>