<?php
/**

Sounds of Europe

%FILE%		soe_eblog.php
%AUTHOR%	Pierre Marchand
%DATE%		2011-07-25

 */

global $blogloc;

?>

<div id="menu_index" class="menu_closed">

<?php 

$args = array('post_type' => 'soe_eblog',
'order' => 'DESC',
'orderby' => 'post_date',
'posts_per_page'=> -1
);
$the_query = new WP_Query( $args );

$itCount = 0;
$maxItems = 8;
$startCol = '<span>
<div class="index_col">';
$endCol = '</div>
</span>';
$lastLoc = 0;
while ( $the_query->have_posts() )
{
	if($itCount === 0)
		echo $startCol;
	$the_query->the_post();
	$custom = get_post_custom($post->ID);
	
	$loc = $custom['location'][0];
	if($loc != $lastLoc)
	{
		if($lastLoc > 0 && $itCount == $maxItems)
		{
			$itCount = 0;
			echo $endCol;
			echo $startCol;
		}
		$lastLoc = $loc;
		$lObj = GetLocation($loc);
		echo '<div class="menu_category">
		'.$lObj->name.'
		</div>';
	}
	
	echo '<a class="menu_base" href="'.get_permalink($post->ID).'">'.get_the_title().'</a>';
	
	
	if($itCount == $maxItems)
	{
		$itCount = 0;
		echo $endCol;
	}
	else
		$itCount++;
}
	
?>

</div> <!--menu_index-->


