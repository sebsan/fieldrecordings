<?php
/**

Sounds of Europe

%FILE%		soe_eblog.php
%AUTHOR%	Pierre Marchand
%DATE%		2011-07-25

 */


global $blogloc;
global $wpdb;

$query = "
SELECT * 
FROM ".$wpdb->posts." AS p 
INNER JOIN ".$wpdb->postmeta." AS m 
ON p.ID = m.post_id 
WHERE (p.post_type = 'soe_eblog' AND p.post_status = 'publish')
ORDER BY p.post_date DESC;
";

// echo $query;
$eblogByCountry = getPostsByLocation($wpdb->get_results($query, OBJECT));

// $maxItems = 6;
// $maxCols = 6;

$pages = makeIndexPages($eblogByCountry, 6,6);

if(count($pages) == 0)
{
	echo '
<div id="menu_page_0" class="page">
</div>
	';
}
else
{
	displayIndexPages($pages);
}


?>



