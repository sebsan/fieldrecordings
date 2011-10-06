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

// echo $query;

$organisations = $wpdb->get_results($query, OBJECT);


$organisationByCountry = array();
foreach($organisations as $a)
{
	$aloc = GetLocation($a->meta_value);
	if(!isset($organisationByCountry[$aloc->country_code]))
		$organisationByCountry[$aloc->country_code] = array();
	$organisationByCountry[$aloc->country_code][] = $a;
	
}
ksort($organisationByCountry);
?>


<?php 


$itCount = 0;
$maxItems = 8;
$cCount = 0;
$maxCols = 6;
$startCol = '<span><div class="index_col">';
$endCol = '</div></span>';
$lastLoc = "";
$first = true;
$pages = array();
$content = "";
foreach ( $organisationByCountry as $countryCode => $arar )
{
	if($cCount === $maxCols)
	{
		$pages[] = $content;
		$content = "";
		$cCount = 0;
	}
	if($itCount === 0 && $first === false)
		$content .= $startCol;

	$loc = $countryCode;
	if($loc != $lastLoc)
	{
		$itCount = 0;
		if($first === false)
			$content .= $endCol;
		$cCount++;
		if($cCount === $maxCols)
		{
			$pages[] = $content;
			$content = "";
			$cCount = 0;
		}
		$content .= $startCol;
		$lastLoc = $loc;
		$content .= '<div class="menu_category">'.GetCountryName($loc).'</div>';
	}
	$first = false;
	ksort($arar);
	foreach($arar as $a)
	{
		if($itCount == $maxItems)
		{
			$itCount = 0;
			$content .= $endCol;
			$cCount++;
			if($cCount === $maxCols)
			{
				$pages[] = $content;
				$content = "";
				$cCount = 0;
			}
			$content .= $startCol;
		}
		else
			$itCount++;
		
		$content .= '<a class="menu_base" href="'.get_permalink($a->ID).'">'.get_the_title($a->ID).'</a>';
	}
}

if($content != "")
	$pages[] = $content . $endCol;

// print_r($pages);

foreach($pages as $idx=>$p)
{
	$visibility = "";
	if($idx > 0)
	{
		$visibility = ' style="display:none;"';
	}
	$nav = '<div id="menu_page_nav_box">';
	if($idx > 0)
		$nav .= '<div class="menu_page_nav menu_page_prev">previous</div>';
	if(($idx + 1) < count($pages))
		$nav .= '<div class="menu_page_nav menu_page_next">next</div>';
	$nav .= '</div>';
	echo '<div id="menu_page_'.$idx.'" class="page"'.$visibility.'>
	' . $nav . $p . '
	</div>
	';
}

?>

