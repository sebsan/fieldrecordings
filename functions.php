<?php

require_once(get_stylesheet_directory() . '/type.class.inc');
require_once(get_stylesheet_directory() . '/event.class.inc');
require_once(get_stylesheet_directory() . '/artist.class.inc');
require_once(get_stylesheet_directory() . '/post.class.inc');
require_once(get_stylesheet_directory() . '/organisation.class.inc');


add_action('admin_init', 'SOE_locationInit');
add_action('init', 'SOE_customTypesInit');
add_action('init', 'SOE_JSInit');

function SOE_locationSection()
{
	echo '<p>Current location of the blog</p>';
}

function SOE_locationCallback()
{
	$locsetting = get_option('soe_location', false);
	$loc = NULL;
	if($locsetting !== false)
	{
		global $wpdb;
		$locs = $wpdb->get_results("
		SELECT c.geonameid,c.name,c.country_code,a.name AS codename FROM cities15 AS c LEFT JOIN admin1codes AS a ON (a.admin = CONCAT(c.country_code,'.',c.admin1))
		WHERE c.geonameid = ".$locsetting.";" , OBJECT);
		// 			print_r($locs);
		if($locs != NULL)
		{
			$loc = $locs[0];
		}
	}
	$locSource = get_bloginfo('stylesheet_directory').'/cities.php';
	if($loc === NULL)
	{
		echo '
		<div>
		<input type="hidden" id="soe_location" name="soe_location"/> 
		<input type="text" id="location_search"/> 
		</div>';
	}
	else
	{
		echo '
		<div>
		<input type="hidden" id="soe_location" name="soe_location" value="'.$loc->geonameid.'" /> 
		<input type="text" id="location_search" value="'.$loc->name.', '.$loc->codename.' ('.$loc->country_code.')" />
		</div>';
	}
	
	echo '
	<script type="text/javascript">
	// <![CDATA[
	
	jQuery(document).ready(function()
	{
		
		
		jQuery( "#location_search" ).autocomplete(
			{
				minLength: 0,
				source: "'.$locSource.'",
				focus: function( event, ui ) 
		{
			jQuery( "#location_search" ).val( ui.item.label );
		return false;
			},
			select: function( event, ui ) 
		{
			jQuery( "#location_search" ).val(ui.item.label);
			jQuery( "#soe_location" ).val(ui.item.value);
		return false;
			},
			});
			});
		// ]]>
		</script>
		';
}

function SOE_locationInit()
{
	if(!is_admin())
		return;
	
	add_settings_section('soe_location_section', 'Current Location', 'SOE_locationSection', 'general');
	add_settings_field('soe_location', 'Location', 'SOE_locationCallback', 'general', 'soe_location_section');
	register_setting('general','soe_location');
}

function SOE_customTypesInit() 
{
	global $soe_types;
	global $tnames;
	
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
	
	$soe_organisations = new SOE_Organisation(array(
				'name' => 'Organisation',
				'menu' => true,
				'support' => array('post_tag') ) );
	
	$soe_types = array( 
				$soe_events ,
				$soe_artists,
				$soe_posts,
				$soe_organisations
				);
				
	$tnames = array();
	foreach($soe_types as $qt)
	{
		$tnames[] = $qt->WP_type();
	}
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


// Utils

function GetLocation($id)
{
	global $wpdb;
	$locs = $wpdb->get_results("
	SELECT c.geonameid,c.name,c.country_code,a.name AS codename FROM cities15 AS c LEFT JOIN admin1codes AS a ON (a.admin = CONCAT(c.country_code,'.',c.admin1))
	WHERE c.geonameid = ".$id.";" , OBJECT);
	// 			print_r($locs);
	if($locs != NULL)
	{
		return $locs[0];
	}
	return NULL;
}


?>