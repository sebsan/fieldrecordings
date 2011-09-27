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
$sounds = $wpdb->get_results("
SELECT * 
FROM ". $wpdb->posts ." AS p 
WHERE (p.post_type = 'attachment' AND p.post_mime_type LIKE 'audio%' )
ORDER BY p.post_date DESC
LIMIT 0,1;
", OBJECT);

if($sounds)
{
	$s = $sounds[0];
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
	<div id="sow_player_outer">
		<div id="sow_player">
			<div class="audio-block audio-'.$audiotype.'" id="audio-'.$s->ID.'" title="'.$at.'">
			<span class="media-player"></span>
				<div id="jp_interface_'.$s->ID.'" class="player_symbols">
					<img class="jp-play" src="'.get_bloginfo('template_directory').'/img/play.png" /> 
					<img class="jp-pause" src="'.get_bloginfo('template_directory').'/img/pause.png" /> 
					<span>Sound of the week</span>
					<div class="sow_details">
						<div>
						<span class="sow_track_title">'.$s->post_title.'</span>
						<span class="jp-current-time"></span> |
						<span class="jp-duration"></span>
						</div> 
						<div>
						'.$par.'
						</div>
					</div>
				</div>
			</div>
		</div> <!-- sow_player -->
	</div><!-- sow_player_outer -->';
}
/// NEWS

$events = $wpdb->get_results("
SELECT * 
FROM ". $wpdb->posts ." AS p 
WHERE (p.post_type = 'soe_event') ;
", OBJECT);
	// print_r($events);
	$news = array('post' => null, 'date' => 0 , 'custom' => null);
	$today = strtotime(current_time('mysql'));
	// print_r($today);
	// echo 'T = '.$today;
	foreach($events as $e)
	{
		$c = get_post_custom($e->ID);
		// 	print_r($c);
		$sd = strtotime($c['event_date_start'][0]);
		$ed = strtotime($c['event_date_end'][0]);
		if($sd >= $today || $ed >= $today)
		{
			if($d < $news['date'] || $news['date'] == 0)
			{
				$news['post'] = $e;
				$news['date'] = $d;
				$news['custom'] = $c;
			}
		}
		
	}
	// print_r($news);
	if($news['post'])
	{
		$ncust = get_post_custom($news['post']->ID);
		$nloc = GetLocation($ncust['location'][0]);
		$newsStr =  '<div id="newsContent">
		<span class="title"><a href="'.get_permalink($news['post']->ID).'">'.get_the_title($news['post']->ID).'</a></span> /
		<span class="date">'.$news['custom']['event_date_start'][0].'</span> /
		<span class="place">'.$nloc->name.'</span>
		</div> <!-- newsContent -->';
	}

?>

<div id="menu_index" class="menu_closed"></div> <!--menu_index-->

<?php echo $soundStr; ?>
<?php echo $newsStr; ?>
<div id="menu_item">
	<span id="menu_item_eblog" class="site_menu_item">Blog</span>
	<span id="menu_item_artist" class="site_menu_item">Artists</span>
	<span id="menu_item_event" class="site_menu_item">Events</span>
	<span id="menu_item_organisation" class="site_menu_item">Institutions</span>
	<span id="menu_item_writing" class="site_menu_item">Writings</span>
	<a href="<?php echo get_permalink($about->ID); ?>" id="menu_item_about" class="extra_menu_item">About</a>
	

</div>