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

$postType = get_post_type($post->ID);
$custom = get_post_custom($post->ID);


if($postType == 'soe_eblog')
{
	echo '<div id="content_outer"> <div id="content">';
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
	<div class="section_par">'.get_the_content().'</div>
	</div>
	';
	echo '</div></div>';
}
elseif($postType == 'soe_event')
{
	echo '<div id="content_outer"> <div id="content">';
	echo '</div></div>';
	
}
elseif($postType == 'soe_artist')
{
	echo '<div id="content_outer"> <div id="content">';
// 	print_r($post);
// 	print_r($custom);
	$image = "";
	$tags = "";
	$audio = "";
	
	echo '<div class="content_category">ARTISTS</div>
	<div class="title">'.get_the_title().'</div>
	<div class="location">'.$postloc->name.' — '.GetCountryName($postloc->country_code).'</div>
	<div class="picture">'.$image.'</div>
	<div class="section">
	<div class="section_title">Biography</div>
	<div class="section_par">
	'.$custom['artist_bio'][0].'
	</div>
	<div class="section_title">Use of fieldrecordings</div>
	<div class="section_par">
	'.$custom['artist_use'][0].'
	</div>
	<div class="general_url"><a href="http://'.$custom['artist_url'][0].'">'.$custom['artist_url'][0].'</a></div>
	<div class="tags_box">
	<div class="tags">
	'.$tags.'
	</div>
	</div>
	'.$audio.'
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
	$posts = $wpdb->get_results("
	SELECT * FROM wp_posts AS p 
	INNER JOIN wp_postmeta AS m 
	ON p.ID = m.post_id 
	WHERE (p.post_type != 'soe_city' AND m.meta_key = 'location' AND m.meta_value = '".$custom['location'][0]."') ;
	", OBJECT);
	$mx = 500;
	$my = 500;
	$x = 0;
	$y = 0;
	$r = 0;
	$t = 0;
	foreach($posts as $p)
	{
		if( $t == -400)
			$t = 0;
		else
			$t -= 20;
		$r += 60;
		
		$x = floor($r * cos($t));
		$y = floor($r * sin($t));
		
		$boxtype = 'BLOG';
		if($p->post_type == 'soe_event')
			$boxtype = 'EVENT';
		if($p->post_type == 'soe_artist')
			$boxtype = 'ARTIST';
		if($p->post_type == 'soe_organisation')
			$boxtype = 'ORGANISATION';
		if($p->post_type == 'soe_writing')
			$boxtype = 'WRITING';
		echo '
		<div class="closedBox_outer" style="position:absolute;left:'.($mx+$x).'px;top:'.($my+$y).'px">
			<div class="closedBox">
				<div class="closedBox_category">'.$boxtype.'</div>
				<div class="closedBox_title"><a href="'.get_permalink($p->ID).'">'.$p->post_title.'</a></div>
			</div>
		</div>';
	}
}
elseif($postType == 'soe_writing')
{
	echo '
	<div class="all">
	<div id="writing_outer">
	<div id="colonne-writings_1">
	<div class="writings_titre-in">
	<span >'.get_the_title().'</span>
	</div>
	<div class="writings_author">
	<span class="menu_writings">'.get_the_author().'</span> <span class ="writings_day">'.get_the_date().'</span>
	</div>
	<div id="writing_content">
	'.get_the_content().'
	</div>
	
	<div id="colonne-sommaire">
	<p class="writings_sommaire"><a href="#1">1. Exchanging artistic projects and ideas.</p>
	<p class="writings_sommaire"><a href="#2">2. Exchanging sounds across borders.<a></p>
	<p class="writings_sommaire"><a href="#2.1">2.1. Exploration.<a></p>
	<p class="writings_sommaire"><a href="#2.2">2.2. Creation and exchange of sounds.<a></p>
	</div> 
	
	
	</div> <!-- writing_outer  -->
	</div>
	';
}



get_footer();

?>