<?php
/**

Sounds of Europe

%FILE%		soe_event.php
%AUTHOR%	Pierre Marchand
%DATE%		2011-07-25

 */

echo '<h1>SOE_EVENT.PHP</h1>';
$tlfn = get_stylesheet_directory() . '/timeline2.html';
$tl = file_get_contents($tlfn);
if($tl === FALSE)
	echo '<p>Failed to read the content of: '.$tlfn.'<p>';
echo $tl;

global $wpdb;
$query = "
SELECT * 
FROM ".$wpdb->posts." AS p
WHERE (p.post_type = 'soe_event');";

?>