<?php

require_once("RugImage.php");

$i = new RugImage(null);
$i->SplitImage($_GET['p']);
