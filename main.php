<?php
/**
 * 
 * Sounds of Europe
 *	
 * %FILE%	main.php
 * %AUTHOR%	Pierre Marchand
 * %DATE%	2011-09-02
 *	
 */
// get_header();
// global $wpdb;
// global $blogloc;
// 
global $isEntryPoint;
$isEntryPoint = true;
query_posts(array(
	'post_type' => 'soe_eblog',
	'posts_per_page' => '1',
	'post_status' => 'publish',
	));
	
global $wp_query;
$wp_query->is_single = true;
// print_r($wp_query);
get_template_part('single');

?>