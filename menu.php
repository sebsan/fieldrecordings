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
$hasNews = false;

global $wpdb;
/// SOUND OF THE WEEK

$sound = get_option('soe_sow', false);
if($sound)
{
// 	$s = get_post($sounds);
// 	$at = wp_get_attachment_url($s->ID);
// 	$mimetype = explode('/', get_post_mime_type($s->ID));
// 	$audiotype = $mimetype[1];
// 	$par = "";
// 	if($s->post_parent)
// 	{
// 		$pp = get_post($s->post_parent);
// 		$par = '<a class="sow_by" href="'.get_permalink($s->post_parent).'">By '.$pp->post_title.'</a>';
// 	}

	$fsSoundJSON = SOE_HTTP_GET('http://www.freesound.org/api/sounds/'.$sound.'/', array('api_key' => '27040bd2abb94a2fb141a19b963c9e93'));
// 	print_r('http://www.freesound.org/api/sounds/'.$sound.'/');
	
	
	$fsSoundJSON = strstr($fsSoundJSON, '{');
	$fsSoundJSON = substr($fsSoundJSON, 0,  strrpos($fsSoundJSON, '}') + 1);
	
// 	print_r($fsSoundJSON);
	$fs = json_decode($fsSoundJSON);
// 	print_r($fs);
	$audiotype = 'mp3';
	
	if(isset($fs->url))
	{
		$fsdesc = $fs->description;
		if(strlen($fsdesc) > 68)
			$fsdesc = substr($fs->description, 0, 68). '…';
		$soundStr = '
			<div id="sow_player">
			<div class="audio-block audio-'.$audiotype.'" id="audio-'.$sound.'" title="'.$fs->{'preview-hq-mp3'}.'">
				<span class="media-player" id="sow_media_player"></span>
				<div id="jp_interface_'.$sound.'" class="player_symbols">
					<span id="sow_label">Sound of the week</span>
						
						
						<div>
						<img class="jp-play" src="'.get_bloginfo('template_directory').'/img/play-red.png" alt="play" /> 
						<img class="jp-pause" src="'.get_bloginfo('template_directory').'/img/pause-red.png" alt="pause" /> 
						<a class="sow_track_title" target="_blank" href="'.$fs->url.'">'.$fsdesc.'</a>
						
						</div> 
						
					</div>
				</div>
			</div> <!-- sow_player -->';
	}
	else
	{
		$soundStr = ' <div id="sow_player"> </div>';
	}
}

/// NEWS
global $isEntryPoint;
$news = get_option('soe_news', 0);
$newsOpen = $isEntryPoint;
if($news > 0)
{
	$hasNews = true;
	$thenew = get_post($news);
	$newsStr =  '
	<div id="newsContent">
		<div id="latest_news">
			<span id="latest_news_title">'.get_the_title($thenew->ID).'</span>
			<div id="latest_news_content">'.apply_filters('the_content',$thenew->post_content).'</div>
		</div> 
	</div> <!-- newsContent -->';
		
}


?>



<div id="bando">
	<div id="bando_logo1">
		<a href="<?php echo get_bloginfo('wpurl'); ?>"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/soe-logo-menu2.png" alt="soe-logo" /></a>
	</div>
	<div id="bando_logo2">
		<a href="http://ec.europa.eu/culture/our-programmes-and-actions/doc411_en.htm"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/eu-logo.png" alt="eu-logo" /></a>
	</div>
	
	<?php echo $soundStr; ?>
	<div id="social_box">
            <div class="social_item"><a title="on facebook" href="http://XXXX">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/soe-facebook.png" alt="facebook" /></a></div>
            <div class="social_item"><a title="rss feed" href="">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/soe-rss.png" alt="feed" /></a></div>
            <div class="social_item"><a title="follow us on twitter" href="http://XXXX">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/soe-twitter.png" alt="twitter" /></a></div>
            <div class="social_item"><a title="contact us by e-mail" href="mailto:info@soundsofeurope.eu">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/soe-mail.png" alt="e-mail" /></a></div>
	</div>
</div>

<div id="menu_index" class="menu_closed"></div> <!--menu_index-->
<?php echo $newsStr; ?>


<div id="menu_item">
	<span id="menu_item_eblog" class="site_menu_item">Blog</span>
	<span id="menu_item_artist" class="site_menu_item">Artists</span>
	<span id="menu_item_organisation" class="site_menu_item">Organisations</span>
	<span id="menu_item_event" class="site_menu_item">Events</span>
	<span id="menu_item_writing" class="site_menu_item">Writings</span>
	<a href="<?php echo get_permalink($about->ID); ?>" id="menu_item_about" class="extra_menu_item">About</a>
	<?php if($hasNews){echo '<span id="menu_item_news" class="extra_menu_item '.($newsOpen == true ? 'news-active' : '').'">News</span>';} ?>

</div>
