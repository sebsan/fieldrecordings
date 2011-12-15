<?php
/**

Sounds of Europe

%FILE%		single.php
%AUTHOR%	Pierre Marchand
%DATE%		2011-07-25

*/

the_post();
get_header();


global $wpdb;
global $postloc;
global $soe_types;

$postType = get_post_type($post->ID);
$custom = get_post_custom($post->ID);


if($postType == 'soe_eblog')
{
	
	$__prologVV = 'This blog will travel to a different European country
every month where a local organisation or artist will be responsible for
maintaining it. Each country´s particular context and practices with regards to
field recording will be explored and presented in a personal way.';
	
	echo '<div id="content_outer"> <div id="content"> <div id="eblog_prolog"><div><span>'.$__prologVV.'</span></div></div>';
	$date = get_the_date();
	$author = get_the_author();
	$authorLink = '';
	
	$query = "
	SELECT * 
	FROM ".$wpdb->postmeta." AS p
	WHERE (meta_key = 'organisation_user' AND meta_value = '".get_the_author_meta('ID')."');
	";
	$result = $wpdb->get_results($query, OBJECT);
	if($result !== NULL)
	{
		$orgaID = $result[0]->post_id;
		$authorLink = ' href="'.get_permalink($orgaID).'"';
	}
	
	echo '
	<div class="content_category">BLOG</div>
	<div class="title">'.get_the_title().'</div>
	<div class="blog_details"> 
	'.$date.' · <a'.$authorLink.'"> '.$author.'</a>
	</div>
	<div class="section">
	<div class="section_par">'.apply_filters('the_content',get_the_content()).'</div>
	</div>
	';
	$np = GetNextAndPrevious($post->ID);
	echo '<div id="post_nav_box">';
	if($np['previous'])
	{
		echo '<div id="previous_post"><a class="post_nav_link" href="'.get_permalink($np['previous']->ID).'">← '.apply_filters('the_title', $np['previous']->post_title).'</a></div> ';
	}
	
	if($np['next'])
	{
		echo '<div id="next_post"><a class="post_nav_link" href="'.get_permalink($np['next']->ID).'">'.apply_filters('the_title', $np['next']->post_title).' →</a></div> ';
	}
	echo '</div></div>';
	echo '</div></div>';
}
elseif($postType == 'soe_event')
{
	$date = new DateTime($custom['event_date_start'][0]);
	$orgas = '';
	$sep = '';
	foreach($custom['event_organization'] as $o)
	{
		$ap = get_post($o , OBJECT );
		$orgas .= $sep  . '<a href="'.get_permalink($ap->ID).'""> '.$ap->post_title.'</a>';
		$sep = ', ';
	}
// 	$ap = get_post($custom['event_organization'][0] , OBJECT );
	$l = GetLocation($custom['location'][0]);
// 	print_r($l);
	echo '
	<div id="content_outer"> 
	<div id="content">
		<div class="content_category">EVENT</div>
		<div class="title">'.get_the_title().'</div>
		<div class="blog_details"> 
		'.$date->format('d/m/Y').', '.$l->name.' - '.$orgas.' 
		</div>
		<div class="section">
		<div class="section_par">'.get_the_content().'</div>
		</div>
	</div>
	</div>';
	
}
elseif($postType == 'soe_artist')
{
	echo '<div id="content_outer"> <div id="content">';

	$image = "";
	if(isset($custom['artist_image'][0]))
	{
		$image = GetImage($custom['artist_image'][0]);
	}
	
	
	
	$tags = get_the_tag_list( '', ', ', '' );
	$audio = '';
	if(isset($custom['artist_sound'][0]))
		$audio = 'Listen to '. mediaPlayer($custom['artist_sound'][0]);
	
	echo '
	<div class="content_category">ARTISTS</div>
	<div class="title">'.get_the_title().'</div>
	<div class="location">'.$postloc->name.' — '.GetCountryName($postloc->country_code).'</div>
	<div class="picture">'.$image.'</div>
	<div class="section">
		<div class="section_title">Biography</div>
		<div class="section_par">
		'.$custom['artist_bio'][0].'
		</div>
		<div class="section_title">Description of work with field recording</div>
		<div class="section_par">
		'.$custom['artist_use'][0].'
		</div>
	
		<div class="general_url"><a href="'.$custom['artist_url'][0].'">'.$custom['artist_url'][0].'</a></div>
		<div class="tags_box">
			<div class="tags">
			'.$tags.'
			</div>
		</div>
		<div class="artist_track">'.$audio.'</div>
	</div>';
	echo '</div></div>';
}
elseif($postType == 'soe_organisation')
{
	echo '<div id="content_outer"> <div id="content">';
	$image = "";
	$tags = "";
	$audio = "";
	echo '<div class="content_category">ORGANISATION</div>
	<div class="title">'.get_the_title().'</div>
	<div class="location">'.$postloc->name.' — '.GetCountryName($postloc->country_code).'</div>
	<div class="picture">'.$image.'</div>
	<div class="section">
	<div class="section_title">Mission statement</div>
	<div class="section_par">
	'.$custom['organisation_mission'][0].'
	</div>
	
	<div class="general_url"><a href="http://'.$custom['organisation_url'][0].'">'.$custom['organisation_url'][0].'</a></div>
	<div class="tags_box">
	<div class="tags">
	'.$tags.'
	</div>
	</div>
	'.$audio.'
	</div>';
	echo '</div></div>';
}
elseif($postType == 'soe_city')
{
	$query = "
	SELECT * FROM ".$wpdb->posts." AS p 
	INNER JOIN ".$wpdb->postmeta." AS m 
	ON p.ID = m.post_id 
	WHERE (p.post_type != 'soe_city' AND m.meta_key = 'location' AND m.meta_value = '".$custom['location'][0]."' AND p.post_status = 'publish') 
	ORDER BY p.post_date DESC;
	";
// 	echo $query;
	$posts = $wpdb->get_results($query, OBJECT);
	$tps = array();
	$oposts = array();
	foreach($posts as $p)
	{
		if(!in_array($p->ID, $tps))
		{
			$tps[] = $p->ID;
			if(!isset($oposts[$p->post_type]))
				$oposts[$p->post_type] = array();
			$oposts[$p->post_type][] = $p;
		}
	}
	
	// find events connected to this city by the fact that orgas from this city are involved in events happening elsewhere
	if(isset($oposts['soe_organisation']))
	{
		foreach($oposts['soe_organisation'] as $p)
		{
			$query = "
			SELECT * FROM ".$wpdb->posts." AS p 
			INNER JOIN ".$wpdb->postmeta." AS m 
			ON p.ID = m.post_id 
			WHERE (p.post_type = 'soe_event' AND m.meta_key = 'event_organization' AND m.meta_value = '".$p->ID."' AND p.post_status = 'publish') 
			ORDER BY p.post_date DESC;
			";
// // 	echo $p->post_title.' => '.$query;
			$revents = $wpdb->get_results($query, OBJECT);
			if(isset($oposts['soe_event']))
			{
				if($revents)
				{
					foreach($revents as $r)
					{
						// check if we already have this event
						$hasEvent = FALSE;
						$insertIdx = -1;
						$rCust = get_post_custom($r->ID);
						$rd = strtotime($r->post_date);
						foreach($oposts['soe_event'] as $idx=>$event)
						{
							if($event->ID == $r->ID)
							{
								$hasEvent = TRUE;
								break;
							}
							$red = strtotime($event->post_date);
							if($rd > $red)
							{
								$insertIdx = $idx;
							}
						}
						//$r->post_title .' => '.($hasEvent ? 'true' : 'false');
						if(!$hasEvent)
						{
// 							echo $r->post_title .' => '.$insertIdx."\n";
// 							$rloc = GetLocation($rCust['location'][0]);
// 							$r->{remote_event} = $rloc->name ;
// 							$r->post_title .= ' (in '.$rloc->name.')';
// 							print_r($oposts['soe_event']);
							array_splice($oposts['soe_event'], $insertIdx, 0, array($r));
// 							print_r($oposts['soe_event']);
						}
					}
				}
			}
			else
			{
				$oposts['soe_event'] = array();
				if($revent)
				{
					foreach($revents as $r)
					{
						$oposts['soe_event'][] = $r;
					}
				}
			}
		}
	}
	
	$bCounter = 0;
	$bWidth = 202;
	$bYoffset = 120;
	$bXoffset = 60;
	$sOrder = array('soe_eblog','soe_artist','soe_organisation','soe_event');
	foreach($sOrder as $st)
	{
		if(isset($oposts[$st]))
		{
			$boxtype = 'BLOG';
			if($st == 'soe_event')
			{
				$boxtype = 'EVENTS';
			}
			if($st == 'soe_artist')
				$boxtype = 'ARTISTS';
			if($st == 'soe_organisation')
				$boxtype = 'ORGANISATIONS';
			if($st == 'soe_writing')
				continue;
// 				$boxtype = 'WRITINGS';
			echo '<div class="city_around_box" style="position:absolute;left:'.(($bCounter * $bWidth)+$bXoffset).'px;top:'.($bYoffset).'px">
			<div class="city_around_title"><span>'.$boxtype.'</span></div>
			';
			foreach($oposts[$st] as $p)
			{
				echo '
				<div class="closedBox_outer">
					<div class="closedBox">
						<a href="'.get_permalink($p->ID).'">'.apply_filters('the_title',$p->post_title).'</a>
					</div>
				</div>';
			}
			echo '</div>';
			$bCounter++;
		}
	}
// 	$nump = count($tps);
// 	$maxp = 0;
// 	$vc = 0;
// 	for($i = 0; $vc < $nump; $i++)
// 	{
// 		$vc += $i;
// 		$maxp = $i;
// 	}
// // 	echo 'MAXP = '. $maxp . ' ; NUMP = '. $nump;
// 	$x = 0;
// 	$y = 42;
// 	$cw = 201;
// 	$ch = 41;
// 	$ccx = 0;
// 	$ccy = 0;
// 	$ps = array();
// 	foreach($posts as $p)
// 	{
// 		if(!in_array($p->ID, $ps))
// 		{
// 			$ps[] = $p->ID;
// 			if($ccy == $maxp)
// 			{
// 				$maxp--;
// 				$ccy = 0;
// 				$ccx++;
// 			}
// 			
// 			
// 			$boxtype = 'BLOG';
// 			if($p->post_type == 'soe_event')
// 				$boxtype = 'EVENT';
// 			if($p->post_type == 'soe_artist')
// 				$boxtype = 'ARTIST';
// 			if($p->post_type == 'soe_organisation')
// 				$boxtype = 'ORGANISATION';
// 			if($p->post_type == 'soe_writing')
// 				$boxtype = 'WRITING';
// 			echo '
// 			<div class="closedBox_outer" style="position:absolute;left:'.(($cw * $ccx)+$x).'px;top:'.(($ch * $ccy)+$y).'px">
// 				<div class="closedBox">
// 					<div class="closedBox_category">'.$boxtype.'</div>
// 					<div class="closedBox_title"><a href="'.get_permalink($p->ID).'">'.$p->post_title.'</a></div>
// 				</div>
// 			</div>';
// 			
// 			$ccy++;
// 		}
// 	}
}
elseif($postType == 'soe_writing')
{
	$pdf ='';
	if(isset($custom['writing_pdf']))
	{
		$pdf_url = wp_get_attachment_url( $custom['writing_pdf'][0] );
		$pdf = ' <span id="writing_pdf">(<a href="'.$pdf_url.'">PDF</a>)<span>';
	}
	
	echo '
	<div class="all">
	<div id="writing_outer">
	<div id="colonne-writings_1">
	<div class="writings_titre-in">
	<span >'.get_the_title().'</span> '.$pdf.'
	</div>
	<div class="writings_author">
	<span class="menu_writings">'.get_the_author().'</span> <span class ="writings_day">'.get_the_date().'</span>
	</div>
	<div id="writing_content">
	'.get_the_content().'
	</div>
	

	</div> <!-- writing_outer  -->
	</div>
	';
}
elseif($postType == 'page')
{
	echo '
	<div id="content_outer"> 
		<div id="content">
			<div class="title">'.get_the_title().'</div>
			<div class="section">
				<div class="section_par">
				'.get_the_content().'
				</div>
			</div>
		</div>
	</div>';
}



get_footer();

?>
