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
foreach($artists as $a)
{
	$aloc = GetLocation($a->meta_value);
	if(!isset($artistByCountry[$aloc->country_code]))
		$artistByCountry[$aloc->country_code] = array();
	$artistByCountry[$aloc->country_code][] = $a;
	
}
ksort($artistByCountry);
?>

<div id="menu_index" class="menu_closed">

<?php 


$itCount = 0;
$maxItems = 8;
$startCol = '<span>
<div class="index_col">';
$endCol = '</div>
</span>';
$lastLoc = "";
foreach ( $artistByCountry as $countryCode => $arar )
{
	if($itCount === 0)
		echo $startCol;
	
	$loc = $countryCode;
	if($loc != $lastLoc)
	{
		if($lastLoc > 0 && $itCount == $maxItems)
		{
			$itCount = 0;
			echo $endCol;
			echo $startCol;
		}
		$lastLoc = $loc;
		echo '<div class="menu_category">
		'.GetCountryName($loc).'
		</div>';
	}
	ksort($arar);
	foreach($arar as $a)
	{
		echo '<a class="menu_base" href="'.get_permalink($a->ID).'">'.get_the_title($a->ID).'</a>';
		
		
		if($itCount == $maxItems)
		{
			$itCount = 0;
			echo $endCol;
		}
		else
			$itCount++;
	}
}

?>

</div> <!--menu_index-->
