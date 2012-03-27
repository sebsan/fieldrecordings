<?php
/**

Sounds of Europe

%FILE%		soe_artist.php
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
WHERE (p.post_type = 'soe_artist' AND m.meta_key = 'location')
ORDER BY p.post_title;
";

// echo $query;

$artists = $wpdb->get_results($query, OBJECT);


$artistByCountry = getPostsByLocation($artists);
ksort($artistByCountry);

$regPage = get_page_by_title('Artist Registration');
$regBlock = '
<div class="menu_category">Join</div>
<a class="menu_base" target="_blank" href="'.get_permalink($regPage->ID).'">'.apply_filters('the_content', $regPage->post_content).'</a>

';
$pages = makeIndexPages($artistByCountry, 6,6,$regBlock);
if(count($pages) == 0)
{
	echo '<div id="menu_page_0" class="page">
	'. $regBlock .'
	</div>
	';
}
else
{
 displayIndexPages($pages);
}

?>

