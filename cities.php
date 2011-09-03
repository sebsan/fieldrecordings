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
mysql_set_charset('utf8');
mysql_select_db(DB_NAME);

$term = isset($_GET['term']) ? $_GET['term'] : null;

if($term == null)
	return;

$ret = array();

$query = sprintf("SELECT c.geonameid,c.name,c.country_code,a.name AS codename FROM cities15 AS c LEFT JOIN admin1codes AS a ON (a.admin = CONCAT(c.country_code,'.',c.admin1)) WHERE c.name LIKE '%s%%'; ", mysql_real_escape_string($term));
$result = mysql_query($query, $link);
while ($row = mysql_fetch_assoc($result)) {
	$ret[] = array(
		'value' => $row['geonameid'],
		'label' => $row['name'] . ", ". $row['codename'] . " (". strtoupper($row['country_code']) . ")"
		);
}

echo json_encode($ret);

?>