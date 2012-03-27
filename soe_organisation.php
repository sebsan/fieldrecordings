<?php
/**

Sounds of Europe

%FILE%		soe_organisation.php
%AUTHOR%	Pierre Marchand
%DATE%		2011-09-09

 */



global $blogloc;
global $wpdb;

$query = "
SELECT * 
FROM ".$wpdb->posts." AS p 
INNER JOIN ".$wpdb->postmeta." AS m 
ON p.ID = m.post_id 
WHERE (p.post_type = 'soe_organisation' AND m.meta_key = 'location')
ORDER BY p.post_title;
";

$organisations = $wpdb->get_results($query, OBJECT);
$organisationByCountry = getPostsByLocation($organisations);
ksort($organisationByCountry);

// $regPage = get_page_by_title('Artist Registration');
$regBlock = '
	<div class="menu_category">Sign up</div>
	<span class="menu_base_2">
		<p>
		If you want to sign up your organisation, 
		you can send us an email with a short description of your organisation 
		and website at <a class="menu_base_2"href="mailto:info@soundsofeurope.eu">info@soundsofeurope.eu</a>
		</p>
	</span>
';

$pages = makeIndexPages($organisationByCountry, 6,6,$regBlock);

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

