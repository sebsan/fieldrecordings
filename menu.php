<?php
/** 
 Sounds of Europe
 
 %FILE%		menu.php
 %AUTHOR%	Pierre Marchand
 %DATE%		2011-07-25
 
 */

$about = get_page_by_title('About');

$soundStr = '';
$newsStr = '';

global $wpdb;
/// SOUND OF THE WEEK

$sounds = get_option('soe_sow', false);
if($sounds)
{
	$s = get_post($sounds);
	$at = wp_get_attachment_url($s->ID);
	$mimetype = explode('/', get_post_mime_type($s->ID));
	$audiotype = $mimetype[1];
	$par = "";
	if($s->post_parent)
	{
		$pp = get_post($s->post_parent);
		$par = '<a class="sow_by" href="'.get_permalink($s->post_parent).'">By '.$pp->post_title.'</a>';
	}
	
	$soundStr = '
		<div id="sow_player">
			<div class="audio-block audio-'.$audiotype.'" id="audio-'.$s->ID.'" title="'.$at.'">
			<span class="media-player" id="sow_media_player"></span>
				<div id="jp_interface_'.$s->ID.'" class="player_symbols">
				<span id="sow_label">Sound of the week</span>
					
					
					<div>
					<img class="jp-play" src="'.get_bloginfo('template_directory').'/img/play-red.png" /> 
					<img class="jp-pause" src="'.get_bloginfo('template_directory').'/img/pause-red.png" /> 
					<span class="sow_track_title">'.$s->post_title.'</span>
					
					</div> 
					
				</div>
			</div>
		</div> <!-- sow_player -->';
}
/// NEWS


	$news = get_option('soe_news', false);
	if($news)
	{
		$thenew = get_post($news);
		$ncust = get_post_custom($thenew->ID);
		$nloc = GetLocation($ncust['location'][0]);
		$newsStr =  '<div id="newsContent">
		<div id="latest_news"><span>Latest News</span></div>
		<span class="title"><a href="'.get_permalink($thenew->ID).'">'.get_the_title($thenew->ID).'</a></span> /
		<span class="date">'.$ncust['event_date_start'][0].'</span> /
		<span class="place">'.$nloc->name.'</span>
		</div> <!-- newsContent -->';
	}

?>
<?php echo $soundStr; ?>
<?php echo $newsStr; ?>

<div id="bando">
	<div id="bando_logo">
		<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/soe-logo-menu.png"/>
		<a href="http://ec.europa.eu/culture/our-programmes-and-actions/doc411_en.htm"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/eu-logo.png"/></a>
	</div>

</div>

<div id="menu_index" class="menu_closed"></div> <!--menu_index-->


<div id="menu_item">
	<span id="menu_item_eblog" class="site_menu_item">Blog</span>
	<span id="menu_item_artist" class="site_menu_item">Artists</span>
	<span id="menu_item_event" class="site_menu_item">Events</span>
	<span id="menu_item_organisation" class="site_menu_item">Organisations</span>
	<span id="menu_item_writing" class="site_menu_item">Writings</span>
	<a href="<?php echo get_permalink($about->ID); ?>" id="menu_item_about" class="extra_menu_item">About</a>
	

</div>