<?php
/**

Sounds of Europe

%FILE%		soe_writing.php
%AUTHOR%	Pierre Marchand
%DATE%		2011-07-25

 */

global $blogloc;

?>


<?php 

$args = array('post_type' => 'soe_writing',
'order' => 'DESC',
'orderby' => 'post_date',
'posts_per_page'=> -1
);
$the_query = new WP_Query( $args );

$itCount = 0;
$maxItems = 1;
$cCount = 0;
$maxCols = 4;
if($the_query->found_posts < $maxCols)
	$maxItems = 0;
$startCol = '<span> <div class="index_col">';
$endCol = '</div> </span>';
$first = true;
$pages = array();
$content = $startCol;
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
	$pm = get_permalink($post->ID);
	$wc = get_post_custom($post->ID);
	
	$author = $wc['writing_author'][0];
	$pdf_url = '#';
	if(isset($wc['writing_pdf']))
	{
		$pdf_url = wp_get_attachment_url( $wc['writing_pdf'][0] );
	}
	
	$content .= '
	<div class="writings_titre">
	<a class="menu_writings" href="'.$pdf_url.'">'.get_the_title().'</a>
	<div class="writings_author">'.$author.'</div> 
	</div>
	';
	
	
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
	
	$first = false;
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
	$nav = '';
	if($idx > 0)
		$nav .= '<div class="menu_page_nav menu_page_prev">← previous</div>';
	if(($idx + 1) < count($pages))
		$nav .= '<div class="menu_page_nav menu_page_next">next →</div>';
	$nav = strlen($nav) == 0 ? '' : '<div class="menu_page_nav_box">' .$nav . '</div>';
	echo '<div id="menu_page_'.$idx.'" class="page"'.$visibility.'>
	'.  $nav . $p .'
	</div>
	';
}

?>



