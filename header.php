<?php
/**

Sounds of Europe

%FILE%		header.php
%AUTHOR%	Pierre Marchand
%DATE%		2011-07-25

 */


global $tnames;
global $postloc;
global $blogloc;
$postloc = NULL;
$blogloc = NULL;

if(is_single($post) && in_array($post->post_type, $tnames))
{
	$custom = get_post_custom($post->ID);
	if(isset($custom['location'][0]))
	{
		$postloc = GetLocation($custom['location'][0]);
	}
}
else
{
	$blogloc = GetLocation( get_option('soe_location') );
	
}


$template_dir = get_stylesheet_directory_uri();
$SOE_styles = array(
	"style",
	"menu_top",
	"map",
	"menu_map",
	"menu_nomap",
	"content",
	"writings");

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" lang="en"> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php
	bloginfo( 'name' );
	if( is_home() || is_front_page() )
		echo ' *';
?></title>
<?php wp_head(); ?>

<style media="screen" type="text/css">
<?php
foreach($SOE_styles as $style)
{
	echo '@import url("'.$template_dir.'/'.$style.'.css");';
	echo "\n";
}


?>
</style>

<script type="text/javascript">
var rootUrl = "<?php echo get_bloginfo('url') . '/'; ?>";
var templateUrl = "<?php echo $template_dir . '/'; ?>";
var jplayerswf = " <?php echo get_bloginfo('template_directory') . '/js/jQuery.jPlayer.2.0.0/' ;?>";
var theCity = "<?php  echo $postloc !== NULL ? $postloc->geonameid : "bruxelles" ?>";
<?php
// city IDs in use
global $wpdb;
$locs = $wpdb->get_results("
SELECT * 
FROM ". $wpdb->postmeta ." AS p 
INNER JOIN cities15 AS c 
ON (p.meta_key = 'location' AND p.meta_value = c.geonameid);" , OBJECT);
$wpcities = $wpdb->get_results("
SELECT * FROM wp_posts AS p 
INNER JOIN wp_postmeta AS m 
ON p.ID = m.post_id 
WHERE (p.post_type = 'soe_city' AND m.meta_key = 'location') ;
", OBJECT);
$citiesurl = array();
foreach($wpcities as $c)
{
	$citiesurl[$c->meta_value] = get_permalink($c->ID);
}
// 			print_r($locs);
$locids = array();
if($locs != NULL)
{
	echo 'var locations = new Array();';
	foreach($locs as $loc)
	{
		if(in_array($loc->meta_value, $locids) === FALSE)
		{
// 			echo "\n";
			echo '{var cObj = new Object();';
			echo 'cObj.id = '.$loc->meta_value.';';
			echo 'cObj.url = "'.$citiesurl[$loc->meta_value].'";';
			echo 'cObj.name = "'.$loc->name.'";';
			echo 'cObj.lat = -1 * '.$loc->latitude.';';
			echo 'cObj.lon = '.$loc->longitude.';';
			echo 'cObj.country = "'.$loc->country_code.'";';
			echo 'locations.push(cObj);}';
			$locids[] = $loc->meta_value;
		}
	}
}
?>
</script>

</head> 

<body>

<!-- MAP -->
<div id="texture" class="map" style="z-index:0;"></div>
<div id="carte" class="map" style="z-index:1;"></div>
<div id="labels" class="map" style="z-index:2;"></div>
<!-- MAP -->

<!-- MENU -->
<?php get_template_part('menu'); ?>
<!-- END OF MENU -->