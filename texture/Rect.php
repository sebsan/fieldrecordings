<?php
require_once('RugImage.php');

$vrect = array();
$vrect['width'] = $_POST['w'];
$vrect['height'] = $_POST['h'];
$center['x'] = $_POST['cx'];
$center['y'] = $_POST['cy'];

// TODO handle errors here

$ri = new RugImage($vrect);
$img = $ri->CenterOn($center, true);

$ret = '<img src="' . $img . '" width="' . $vrect['width'] . '" height="' . $vrect['height'] . '"/>';
echo $ret;


