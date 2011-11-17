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
$cCount = -1;
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
{
	$pages[] = $content . $endCol;
}

// print_r($pages);

$regPage = get_page_by_title('Artist Registration');
$regBlock = '
<div class="index_col_2">
<div class="menu_category">Sign up</div>
<span class="menu_base_2"><p>If you want to sign up your organisation, 
you can send us an email with a short description of your organisation 
and website at <a class="menu_base_2"href="mailto:info@soundsofeurope.eu">info@soundsofeurope.eu</a></p></span>
</div>
';

if(count($pages) == 0)
{
	echo '<div id="menu_page_0" class="page">
	'. $regBlock .'
	</div>
	';
}

foreach($pages as $idx=>$p)
{
	$visibility = "";
	if($idx > 0)
	{
		$visibility = ' style="display:none;"';
	}
	$nav = '';
	if($idx > 0)
			$nav .= '<span class="menu_page_nav menu_page_prev">← previous</span>';
	if(($idx + 1) < count($pages))
			$nav .= '<span class="menu_page_nav menu_page_next">next →</span>';
	$nav = strlen($nav) == 0 ? '' : '<div class="menu_page_nav_box">' .$nav . '</div>';
	echo '<div id="menu_page_'.$idx.'" class="page"'.$visibility.'>
	'. $regBlock  . $nav . $p . '
	</div>
	';
}

?>

