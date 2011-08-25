<?php
/**

Sounds of Europe

%FILE%		soe_eblog.php
%AUTHOR%	Pierre Marchand
%DATE%		2011-07-25

 */

global $soe_types;
$postType = $post->post_type;

?>

<div id="menu_index" class="menu_closed">

<?php 

$args = array('post_type' => $post_type,
'order' => 'DESC',
'orderby' => 'post_date',
'posts_per_page'=> -1
);
$the_query = new WP_Query( $args );

$maxItems = 8;
$startCol = '<span>
<div class="index_col">';
$endCol = '</div>
</span>';
$lastLoc = "";
while ( $the_query->have_posts() )
{
	$the_query->the_post();
	$custom = get_post_custom($post->ID);
	
	$loc = $custom['city'][0];
	if($loc != $lastLoc)
	{
		$lastLoc = $loc;
		echo '<div class="menu_category">
		'.$lastLoc.'
		</div>';
	}
}
	
?>

</div> <!--menu_index-->


