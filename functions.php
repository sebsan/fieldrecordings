<?php

require_once(get_stylesheet_directory() . '/type.class.inc');
require_once(get_stylesheet_directory() . '/event.class.inc');
require_once(get_stylesheet_directory() . '/artist.class.inc');
require_once(get_stylesheet_directory() . '/post.class.inc');
require_once(get_stylesheet_directory() . '/organisation.class.inc');
require_once(get_stylesheet_directory() . '/city.class.inc');
require_once(get_stylesheet_directory() . '/writing.class.inc');


/// utils

function GetLocation($id)
{
	// 	echo '<h2>GetLocation: '.$id.'</h2>';
	global $wpdb;
	$locs = $wpdb->get_results("
	SELECT c.geonameid,c.name,c.country_code,a.name AS codename 
	FROM cities15 AS c LEFT JOIN admin1codes AS a 
	ON (a.admin = CONCAT(c.country_code,'.',c.admin1))
	WHERE c.geonameid = ".$id.";
	" ,OBJECT);
	
	if($locs != NULL)
	{
		// 		echo '<h2>Found location for: '.$id.'</h2>';
		return $locs[0];
	}
	// 	echo '<h2>Failed to find location for: '.$id.'</h2>';
	return NULL;
}

function GetCountryName($isocode)
{
	global $wpdb;
	$query = "
	SELECT country_name 
	FROM countries 
	WHERE ccode = '".$isocode."';
	";
	$country = $wpdb->get_results( $query ,OBJECT);
	if($country != NULL)
		return $country[0]->country_name;
	return "";
}

///

add_action('admin_init', 'SOE_locationInit');
add_action('admin_init', 'SOE_AdminInit');
add_action('init', 'SOE_customTypesInit');
add_action('init', 'SOE_JSInit');

// function sendAudioURL($html, $href, $title)
// {
// 	return $href;
// }
// 
// function sendImageURL($html, $id, $caption, $title, $align, $url, $size, $alt )
// {
// 	//error_log('sendImageURL:'.$html.'|'.$src);
// 	return $url;
// }
// 
// 
// function sendMediaURL($html, $url)
// {
// 	return $url;
// }
function SOE_AdminInit()
{
// 	add_filter( 'audio_send_to_editor_url', 'sendAudioURL', 1, 3 );
// 	add_filter( 'image_send_to_editor', 'sendImageURL', 1, 8 );
// 	
// 	
// 	global $wp_filter, $merged_filters, $wp_current_filter;
// 	print_r($wp_filter);
}

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
		$loc = GetLocation($locsetting);
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
				
	$soe_writings = new SOE_Eblog(array(
				'name' => 'Writing',
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
				
	$soe_cities = new SOE_City(array(
				'name' => 'City',
				'menu' => false,
				'support' => array('post_tag') ) );
	$soe_types = array( 
				$soe_posts,
				$soe_artists,
				$soe_events ,
				$soe_writings,
				$soe_organisations,
				$soe_cities
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
		wp_register_style('datepicker', get_stylesheet_directory_uri() . '/js/datepicker.css');
		wp_enqueue_style( 'datepicker');
		wp_enqueue_script('jquery-ui-autocomplete',  get_stylesheet_directory_uri(). '/js/jquery-ui-autocomplete.js' , array('jquery-ui-core'));
		wp_enqueue_script('jquery-ui-datepicker',  get_stylesheet_directory_uri(). '/js/jquery-ui-datepicker.js' , array('jquery-ui-core'));  
		wp_enqueue_script('datepicker',  get_stylesheet_directory_uri(). '/js/datepicker.js' , array('jquery-ui-datepicker'));
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');
		wp_enqueue_script('tiny_mce');
	}
	
}

function mediaPlayer($id)
{
	if($id > 0)
	{
		$audio = get_post($id, OBJECT);
		$at = wp_get_attachment_url($id);
		$mimetype = explode('/', get_post_mime_type($id));
		$audiotype = $mimetype[1];
		return '
		<span class="audio-block audio-'.$audiotype.'" id="audio-'.$audio->ID.'" title="'.$at.'">
		<span class="media-player"></span>
		<span id="jp_interface_'.$audio->ID.'">
		<img class="jp-play" src="'.get_bloginfo('template_directory').'/img/play.png" /> 
		<img class="jp-pause" src="'.get_bloginfo('template_directory').'/img/pause.png" />  
		<span class="audio_title">'.$audio->post_title.'</span> 
		</span>
		</span>';
			
	}	
}

function GetImage($id)
{
	$sized = image_downsize( $id, array(323,323));
	return '<img src="'.$sized[0].'" width="'.$sized[1].'" height="'.$sized[2].'"/>';
}

function GetTags($id)
{
	
}

function GetNextAndPrevious($id)
{
	global $wpdb;
	$ret = array('next'=> FALSE, 'previous' => FALSE);
	$cp = get_post($id, OBJECT);
	$query = "
	SELECT * 
	FROM ".$wpdb->posts." AS p
	WHERE (p.post_type = '".$cp->post_type."' AND p.post_date > '".$cp->post_date."');
	";
	
	$result = $wpdb->get_results($query, OBJECT);
	if($result)
	{
		$ret['next'] = $result[0];
	}
	$query = "
	SELECT * 
	FROM ".$wpdb->posts." AS p
	WHERE (p.post_type = '".$cp->post_type."' AND p.post_date < '".$cp->post_date."');
	";
	
	$result = $wpdb->get_results($query, OBJECT);
	if($result)
	{
		$ret['previous'] = array_pop($result);
	}
	
	return $ret;
	
}

function new_excerpt_length($length) {
	return 22;
}
add_filter('excerpt_length', 'new_excerpt_length');
function new_excerpt_more($more) {
	return '…';
}
add_filter('excerpt_more', 'new_excerpt_more');

?>