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


$artistByCountry = array();
$ps = array();
foreach($artists as $a)
{
	$aloc = GetLocation($a->meta_value);
	if(!isset($artistByCountry[$aloc->country_code]))
		$artistByCountry[$aloc->country_code] = array();
	if(!in_array($a->ID , $ps))
	{
		$ps[] = $a->ID;
		$artistByCountry[$aloc->country_code][] = $a;
	}
}
ksort($artistByCountry);
?>


<?php 


$itCount = 0;
$maxItems = 8;
$cCount = -1;
$maxCols = 5;
$startCol = '<span><div class="index_col">';
$endCol = '</div></span>';
$lastLoc = "";
$first = true;
$pages = array();
$content = "";
foreach ( $artistByCountry as $countryCode => $arar )
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

$regPage = get_page_by_title('Artist Registration');
$regBlock = '
<div class="index_col">
<div class="menu_category">Join</div>
<a class="menu_base" target="_blank" href="'.get_permalink($regPage->ID).'">'.apply_filters('the_content', $regPage->post_content).'</a>
</div>
';

if(count($pages) == 0)
{
	echo '<div id="menu_page_0" class="page">
	'. $regBlock .'
	</div>
	';
}
else
{
	foreach($pages as $idx=>$p)
	{
		$visibility = "";
		if($idx > 0)
		{
			$visibility = ' style="display:none;"';
		}
		$nav = '<div id="menu_page_nav_box">';
		if($idx > 0)
			$nav .= '<span class="menu_page_nav menu_page_prev">previous</span>';
		if(($idx + 1) < count($pages))
			$nav .= '<span class="menu_page_nav menu_page_next">next</span>';
		$nav .= '</div>';
		echo '<div id="menu_page_'.$idx.'" class="page"'.$visibility.'>
		'. $regBlock . $nav . $p . '
		</div>
		';
	}
}

?>

