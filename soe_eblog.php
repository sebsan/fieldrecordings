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
$maxItems = 8;
$cCount = 0;
$maxCols = 6;
$startCol = '<span> <div class="index_col">';
$endCol = '</div> </span>';
$lastLoc = 0;
$first = true;
$pages = array();
$content = "";
while ( $the_query->have_posts() )
{
	if($cCount === $maxCols)
	{
		$pages[] = $content;
		$content = "";
		$cCount = 0;
	}
	if($itCount === 0 && $first === false)
		$content .= $startCol;
	$the_query->the_post();
	$custom = get_post_custom($post->ID);
	
	$loc = $custom['location'][0];
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
		$lObj = GetLocation($loc);
		$content .= '<div class="menu_category">
		'.GetCountryName($lObj->country_code).'
		</div>';
	}
	
	$content .=  '<a class="menu_base" href="'.get_permalink($post->ID).'">'.get_the_title().'</a>';
	
	
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
	}
	else
		$itCount++;
}

if($content != "")
	$pages[] = $content . $endCol;

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
	'. $nav . $p  . '
	</div>
	';
}

?>



