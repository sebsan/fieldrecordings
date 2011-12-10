<?php
/**

Sounds of Europe

%FILE%		soe_eblog.php
%AUTHOR%	Pierre Marchand
%DATE%		2011-07-25

 */

global $blogloc;

?>


<?php 

$args = array(
	'post_type' => 'soe_eblog',
	'order' => 'DESC',
	'orderby' => 'post_date',
	'post_status' => 'publish',
	'posts_per_page'=> -1
);
$the_query = new WP_Query( $args );

$itCount = 0;
$maxItems = 4;
$cCount = 0;
$maxCols = 6;
$startCol = '<span> <div class="index_col">';
$endCol = '</div> </span>';
$lastLoc = 0;
$first = true;
$pages = array();
$columns = array();
$rows = array();
$content = "";
while ( $the_query->have_posts() )
{
	if($cCount === $maxCols)
	{
		$pages[] = $columns;
		$columns = array();
		$cCount = 0;
	}
	$the_query->the_post();
	$custom = get_post_custom($post->ID);
	$loc = $custom['location'][0];
	if($loc != $lastLoc)
	{
		$itCount = 0;
		if(count($rows) > 0)
		{
			$columns[$cCount] = $rows;
			$rows = array();
			$cCount++;
		}
		if($cCount === $maxCols)
		{
			$pages[] = $columns;
			$columns = array();
			$cCount = 0;
		}
		$lastLoc = $loc;
		$lObj = GetLocation($loc);
		$rows[] = ' <div class="menu_category">'.GetCountryName($lObj->country_code).'</div> ';
	}
	
	$rows[] = '<a class="menu_base" href="'.get_permalink($post->ID).'">'.get_the_title().'</a> ';
	
	
	if($itCount == $maxItems)
	{
		$columns[$cCount] = $rows;
		$rows = array();
		$itCount = 0;
		$cCount++;
		if($cCount === $maxCols)
		{
			$pages[] = $columns;
			$columns = array();
			$cCount = 0;
		}
	}
	else
		$itCount++;
}

if(count($rows) > 0)
	$columns[$cCount] = $rows;
if(count($columns) > 0)
	$pages[] = $columns;

// var_dump($pages);

foreach($pages as $idx=>$page)
{
	$visibility = "";
	if($idx > 0)
	{
		$visibility = ' style="display:none;"';
	}
	$nav = '';
	if($idx > 0)
		$nav .= '<div class="menu_page_nav menu_page_prev">← previous</div>';
	if(($idx + 1) < count($pages))
		$nav .= '<div class="menu_page_nav menu_page_next">next →</div>';
	$nav = strlen($nav) == 0 ? '' : '<div class="menu_page_nav_box">' .$nav . '</div>';
	echo '<div id="menu_page_'.$idx.'" class="page"'.$visibility.'>
	'. $nav;
	
	foreach($page as $cols)
	{
		echo $startCol;
		
		foreach($cols as $row)
			echo $row;
		
		echo $endCol;
	}
	
	echo '</div>
	';
}

?>



