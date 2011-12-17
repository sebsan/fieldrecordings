<?php

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

function SOE_SowSection()
{
	echo '<p>Sound of the week (Freesound sound ID)</p>';
}

function SOE_SowCallback()
{
	global $wpdb;
	
	$sowsetting = get_option('soe_sow', false);
	$sstr = '
	<input type="text" name="soe_sow" value="'.$sowsetting.'"/>
	';
	echo $sstr;
	
	/*
	
	$query = "
	SELECT * 
	FROM ".$wpdb->posts." AS p
	WHERE (p.post_type = 'attachment');
	";
	$result = $wpdb->get_results($query, OBJECT);
	
	$sstr = '<select name="soe_sow">';
	foreach($result as $r)
	{
		$mime = array_shift(explode('/', $r->post_mime_type));
		if($mime == 'audio')
		{
			if($r->ID == $sowsetting)
				$sstr .= '<option value="'.$r->ID.'" selected="selected">'.$r->post_title.'</option>';
			else
				$sstr .= '<option value="'.$r->ID.'">'.$r->post_title.'</option>';
		}
	}
	$sstr .= '<select>';
	
	echo $sstr;*/
}


function SOE_NewsSection()
{
	echo '<p>Highlight News</p>';
}

function SOE_NewsCallback()
{
	global $wpdb;
	
	$newssetting = get_option('soe_news', false);
	
	$query = "
	SELECT * 
	FROM ".$wpdb->posts." AS p
	WHERE (p.post_type = 'soe_postit' AND p.post_status = 'publish');
	";
	$result = $wpdb->get_results($query, OBJECT);
	$sstr = '<select name="soe_news">';
	if(0 == $newssetting)
		$sstr .= '<option value="0"  selected="selected">No News</option>';
	else
		$sstr .= '<option value="0" >No News</option>';
	foreach($result as $r)
	{
		if($r->ID == $newssetting)
			$sstr .= '<option value="'.$r->ID.'" selected="selected">['.$r->post_title.'] '.substr($r->post_content, 0 , 64).'</option>';
			else
				$sstr .= '<option value="'.$r->ID.'">['.$r->post_title.'] '.substr($r->post_content, 0, 64).'</option>';
	}
	$sstr .= '<select>';
	
	echo $sstr;
}
function SOE_NewsTitleSection()
{
// 	echo '<p>Highlight Event Title</p>';
}

function SOE_NewsTitleCallback()
{
// 	global $wpdb;
// 	
// 	$newstitle = get_option('soe_news_title', 'Latest News');
// 	$sstr = '
// 	<input type="text" name="soe_news_title" value="'.$newstitle.'"/>
// 	';
// 	echo $sstr;
}


add_action('admin_menu', 'SOE_OptionsMenu');

function SOE_OptionsMenu()
{
	add_submenu_page('options-general.php', 'Sounds of Europe Settings', 'SoE Settings', 'administrator', __FILE__, 'soe_settings_page');
	add_action('admin_init', 'SOE_OptionsInit');
}
function SOE_OptionsInit()
{
// 	add_settings_section('soe_location_section', 'Current Location', 'SOE_locationSection', 'soe_settings_page');
// 	add_settings_field('soe_location', 'Location', 'SOE_locationCallback', 'soe_settings_page', 'soe_location_section');
	register_setting('soe_settings_page','soe_location');
	
// 	add_settings_section('soe_sow_section', 'Sound of the week', 'SOE_SowSection', 'soe_settings_page');
// 	add_settings_field('soe_sow', 'SOW', 'SOE_SowCallback', 'soe_settings_page', 'soe_sow_section');
	register_setting('soe_settings_page','soe_sow');
	
// 	add_settings_section('soe_news_section', 'News', 'SOE_NewsSection', 'soe_settings_page');
// 	add_settings_field('soe_news', 'News', 'SOE_NewsCallback', 'soe_settings_page', 'soe_news_section');
	register_setting('soe_settings_page','soe_news');
	register_setting('soe_settings_page','soe_news_title');
}

function soe_settings_page()
{
	echo '<div class="wrap">
	<h2>Sounds of Europe settings</h2>
	<form method="post" action="options.php"> ';
	settings_fields( 'soe_settings_page' );
// 	do_settings( 'soe_settings_page' );
	
	SOE_locationSection();
	SOE_locationCallback();
	SOE_NewsTitleSection();
	SOE_NewsTitleCallback();
	SOE_NewsSection();
	SOE_NewsCallback();
	SOE_SowSection();
	SOE_SowCallback();
	
	echo '<p class="submit">
	<input type="submit" class="button-primary" value="Save Changes" />
	</p>
	</form>
	</div>';
	
}

?>
