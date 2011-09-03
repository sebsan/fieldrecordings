<?php

require_once(get_stylesheet_directory() . '/type.class.inc');
require_once(get_stylesheet_directory() . '/event.class.inc');
require_once(get_stylesheet_directory() . '/artist.class.inc');
require_once(get_stylesheet_directory() . '/post.class.inc');


add_action('init', 'SOE_customTypesInit');
add_action('init', 'SOE_JSInit');

function SOE_customTypesInit() 
{
	global $soe_types;
	
	$soe_posts = new SOE_Eblog(array(
		'name' => 'Eblog',
		'menu' => true,
		'support' => array('title', 'editor', 'author', 'excerpt') ) );
		
	$soe_events = new SOE_Event( array(
				'name' => 'Event',
				'menu' => true,
				'support' => array('title', 'editor') ) );
						   
	$soe_artists = new SOE_Artist(array(
				'name' => 'Artist',
				'menu' => true,
				'support' => array('post_tag') ) );
	
	
	$soe_types = array( 
				$soe_events ,
				$soe_artists,
				$soe_posts
				);
}

function SOE_JSInit()
{
	if(!is_admin())
	{ 
		wp_enqueue_script('jquery-jplayer',
		get_bloginfo('template_directory') . '/js/jQuery.jPlayer.2.0.0/jquery.jplayer.min.js',
			     array('jquery'),
			     '2.0.0' );
		wp_enqueue_script('soe_mediaplayer',
		get_bloginfo('template_directory') . '/js/mediaplayer.js',
		array('jquery-jplayer'),
		'1.0' );
		wp_enqueue_script('json-parse',
		get_bloginfo('template_directory') . '/JSON-js/json_parse.js',
		array(),
		'2.0.0' );
		wp_enqueue_script('raphael',
		get_bloginfo('template_directory') . '/js/raphael.js',
		array(),
		'1.0' );
		
		wp_enqueue_script('soe',
		get_bloginfo('template_directory') . '/js/soe.js',
		array('jquery','raphael','json-parse'),
		'1.0' );
	}
	else
	{
		wp_enqueue_script('jquery-ui-autocomplete',  get_stylesheet_directory_uri(). '/js/jquery-ui-autocomplete.js' , array('jquery-ui-core'));
		wp_enqueue_script('jquery-ui-datepicker',  get_stylesheet_directory_uri(). '/js/jquery-ui-datepicker.js' , array('jquery-ui-core'));  
		wp_enqueue_script('datepicker',  get_stylesheet_directory_uri(). '/js/datepicker.js' , array('jquery-ui-datepicker'));
		
		wp_enqueue_script('tiny_mce');
	}
	
}



?>