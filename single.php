<?php
/**

Sounds of Europe

%FILE%		single.php
%AUTHOR%	Pierre Marchand
%DATE%		2011-07-25

*/
get_header();


global $wpdb;
global $postloc;
the_post();
$postType = get_post_type($post->ID);
$custom = get_post_custom($post->ID);

echo '<div id="content_outer"> <div id="content">';

if($postType == 'soe_eblog')
{
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
}
elseif($postType == 'soe_event')
{
	
}
elseif($postType == 'soe_artist')
{
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
}

echo '</div></div>';


get_footer();

?>