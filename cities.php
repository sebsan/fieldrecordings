<?php

/** 
 cities.php
*/

header('Content-Type: text/plain; charset=utf8');

require_once('../../../wp-config.php');

$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$link) {
	die('Could not connect: ' . mysql_error());
}

mysql_select_db(DB_NAME);

$term = isset($_GET['term']) ? $_GET['term'] : null;

if($term == null)
	return;

$ret = array();

$query = sprintf("SELECT * FROM cities WHERE city LIKE '%s%%'", mysql_real_escape_string($term));
$result = mysql_query($query, $link);
while ($row = mysql_fetch_assoc($result)) {
// 	echo $row['id'];
// 	echo $row['city'];
// 	echo $row['lat'];
// 	echo $row['lon'];
// 	$ret[] = $row['city'];
	$ret[] = array(
		'value' => $row['id'],
		'label' => $row['city']
		);
}

echo json_encode($ret);

?>