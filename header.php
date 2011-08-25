<?php
/**

Sounds of Europe

%FILE%		header.php
%AUTHOR%	Pierre Marchand
%DATE%		2011-07-25

 */


$template_dir = get_stylesheet_directory_uri();
$SOE_styles = array(
	"style",
	"menu_top",
	"map",
	"menu_map",
	"menu_nomap",
	"content");

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
}
?>
</style>

<script type="text/javascript">
var rootUrl = "<?php echo get_bloginfo('url') . '/'; ?>";
var templateUrl = "<?php echo $template_dir . '/'; ?>";
var jplayerswf = " <?php echo get_bloginfo('template_directory') . '/js/jQuery.jPlayer.2.0.0/' ;?>";
var theCity = "<?php echo "bruxelles" ?>";
</script>

</head> 

<body>

<!-- MAP -->
<div id="carte"></div>
<!-- MAP -->

<!-- MENU -->
<?php get_template_part('menu'); ?>
<!-- END OF MENU -->