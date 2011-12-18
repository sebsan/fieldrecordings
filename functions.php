<?php

require_once(get_stylesheet_directory() . '/type.class.inc');
require_once(get_stylesheet_directory() . '/event.class.inc');
require_once(get_stylesheet_directory() . '/artist.class.inc');
require_once(get_stylesheet_directory() . '/post.class.inc');
require_once(get_stylesheet_directory() . '/organisation.class.inc');
require_once(get_stylesheet_directory() . '/city.class.inc');
require_once(get_stylesheet_directory() . '/writing.class.inc');
require_once(get_stylesheet_directory() . '/postit.class.inc');


require_once(get_stylesheet_directory() . '/options.php');

/// utils
function fDate($d)
{
	return date("d/m/Y", strtotime($d));
}

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

function HTTP_GET($uri , $getdata = array(), $port = 80, $cookie = array(),  $custom_headers = array(), $timeout = 1000, $req_hdr = false,  $res_hdr = false)
{
	$ret = ''; 
	$puri = parse_url($uri);
	$ip = $puri['host'];
	$cookie_str = ''; 
	$getdata_str = count($getdata) ? '?' : ''; 
	
	foreach ($getdata as $k => $v) 
		$getdata_str .= urlencode($k) .'='. urlencode($v) . '&'; 
	
	foreach ($cookie as $k => $v) 
		$cookie_str .= urlencode($k) .'='. urlencode($v) .'; '; 
	
	$crlf = "\r\n"; 
	$req = 'GET '. $uri . $getdata_str .' HTTP/1.1' . $crlf; 
	$req .= 'Host: '. $ip . $crlf; 
	$req .= 'User-Agent: Mozilla/5.0 Firefox/3.6.12' . $crlf; 
	$req .= 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' . $crlf; 
	$req .= 'Accept-Language: en-us,en;q=0.5' . $crlf; 
	$req .= 'Accept-Encoding: deflate' . $crlf; 
	$req .= 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7' . $crlf; 
	
	foreach ($custom_headers as $k => $v) 
		$req .= $k .': '. $v . $crlf; 
	
	if (!empty($cookie_str)) 
		$req .= 'Cookie: '. substr($cookie_str, 0, -2) . $crlf; 
	
	$req .= $crlf; 
	
	if ($req_hdr) 
		$ret .= $req; 
	
	if (($fp = @fsockopen($ip, $port, $errno, $errstr)) == false) 
		return "Error $errno: $errstr\n"; 
	
	stream_set_timeout($fp, 0, $timeout * 1000); 
	
	fputs($fp, $req); 
	while ($line = fgets($fp)) $ret .= $line; 
	fclose($fp); 
	
	if (!$res_hdr) 
		$ret = substr($ret, strpos($ret, "\r\n\r\n") + 4); 
	
	return $ret; 
}

///

add_action('admin_init', 'SOE_AdminInit');
add_action('init', 'SOE_customTypesInit');
add_action('init', 'SOE_JSInit');

function SOE_AdminInit()
{
	
}

function SOE_customTypesInit() 
{
	global $soe_types;
	global $tnames;
	
	$soe_posts = new SOE_Eblog(array(
				'name' => 'Eblog',
				'menu' => true,
				'support' => array('title', 'editor', 'author', 'excerpt') ) );
				
	$soe_writings = new SOE_Writing(array(
				'name' => 'Writing',
				'menu' => true,
				'support' => array('title') ) );
		
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
				
	$soe_postits = new SOE_Postit(array(
				'name' => 'Postit',
				'menu' => true,
				'support' => array('title', 'editor') ) );
	$soe_types = array( 
				$soe_posts,
				$soe_artists,
				$soe_events ,
				$soe_writings,
				$soe_organisations,
				$soe_cities,
				$soe_postits
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
		wp_enqueue_script('jquery-ui-autocomplete',  get_stylesheet_directory_uri(). '/js/jquery-ui-autocomplete.js' , array('jquery-ui-core'));
		wp_enqueue_script('jquery-ui-datepicker',  get_stylesheet_directory_uri(). '/js/jquery-ui-datepicker.js' , array('jquery-ui-core'));  
		wp_enqueue_script('datepicker',  get_stylesheet_directory_uri(). '/js/datepicker.js' , array('jquery-ui-datepicker')); 
		wp_enqueue_script('asmselect',  get_stylesheet_directory_uri(). '/js/jquery.asmselect.js' , array('jquery-ui-core'));
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');
// 		wp_enqueue_script('tiny_mce');
		
		wp_register_style('soe_admin', get_stylesheet_directory_uri() . '/admin.css');
		wp_enqueue_style( 'soe_admin');
	}
	
}

function mediaPlayer($id)
{
	if($id > 0)
	{
		$uploads = wp_upload_dir();
		$audio = get_post($id, OBJECT);
		$atURL =  explode('/', wp_get_attachment_url($id));
		$atName = array_pop($atURL);
		$atURL[] = rawurlencode($atName);
		$at = implode('/',$atURL);
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
	$sized = image_downsize( $id, array(393,999));
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
	WHERE (p.post_type = '".$cp->post_type."' AND p.post_date > '".$cp->post_date."' AND p.post_status = 'publish');
	";
	
	$result = $wpdb->get_results($query, OBJECT);
	if($result)
	{
		$ret['next'] = $result[0];
	}
	$query = "
	SELECT * 
	FROM ".$wpdb->posts." AS p
	WHERE (p.post_type = '".$cp->post_type."' AND p.post_date < '".$cp->post_date."' AND p.post_status = 'publish');
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

function slugify($text)
{
	// if its a filename, we want to preserve the dot
	$ta = explode('.', $text);
	// replace non letter or digits by -
	$text = preg_replace('~[^\\pL\d]+~u', '-', $ta);
	$text = implode('.', $ta);
	// trim
	$text = trim($text, '-');
	
	// transliterate
	if (function_exists('iconv'))
	{
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
	}
	
	// lowercase
	$text = strtolower($text);
	
	// remove unwanted characters
	$text = preg_replace('~[^-^\.\w]+~', '', $text);
	
	if (empty($text))
	{
		return 'n-a';
	}
	
	return $text;
}



/**
 *	Audio player kind of plugin 
 */

function getMediaFromTitle($t)
{
	global $wpdb;
	$mq = "
	SELECT * 
	FROM ".$wpdb->posts." AS p
	WHERE (p.post_type = 'attachment' AND p.post_title LIKE '". $t[1] ."');
	";
	$ats = $wpdb->get_results($mq, OBJECT);
// 	var_dump($ats);
	if($ats)
	{
		return mediaPlayer($ats[0]->ID); 
	}
	return '';
}

function insertMediaPlayer($html)
{
	$ret = preg_replace_callback('/\[audio\s*(.*)\]/', getMediaFromTitle, $html);
	return $ret;
}

add_filter('the_content', 'insertMediaPlayer');











?>