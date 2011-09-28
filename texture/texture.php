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
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($img));

ob_clean();
flush();
readfile($img);
exit();

?>