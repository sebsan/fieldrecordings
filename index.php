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

if(is_page() && get_page_by_title('Artist Registration') == $post)
{
	get_template_part('artist_form');
}
elseif(is_singular())
{
	get_template_part('single');
}
elseif(is_home())
{
	get_template_part( 'main' );
}
elseif(is_post_type_archive())
{
	get_template_part( $post->post_type );
}
else
{
	get_template_part( 'main' );
}

?>
