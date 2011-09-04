<?php
/**
 * 
 * Sounds of Europe
 *	
 * %FILE%	index.php
 * %AUTHOR%	Pierre Marchand
 * %DATE%	2011-07-22
 *	
 */

global $tnames;
global $postloc;
global $blogloc;
$postloc = NULL;
$blogloc = NULL;
if(is_singular() && in_array($post->post_type, $tnames))
{
	$custom = get_post_custom($post->ID);
	if(isset($custom['location'][0]))
	{
		$postloc = GetLocation($custom['location'][0]);
	}
}
else
{
	$blogloc = GetLocation( get_option('soe_location') );
	
}


if(is_singular())
{
	get_template_part('single');
}
elseif(is_home())
{
	get_template_part( 'main' );
}
elseif(is_post_type_archive())
{
// 	echo '<h1>Try to load: </h1>' . $post->post_type;
	get_template_part( $post->post_type );
}
else
{
	echo '<h1>Unable to complete request:</h1>';
}

?>
