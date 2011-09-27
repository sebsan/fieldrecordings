<?php
/** 
 Sounds of Europe
 
 %FILE%		menu.php
 %AUTHOR%	Pierre Marchand
 %DATE%		2011-07-25
 
 */
$about = get_page_by_title('About');
?>

<div id="menu_index" class="menu_closed"></div> <!--menu_index-->

<div id="menu_item">
	<span id="menu_item_eblog" class="site_menu_item">Blog</span>
	<span id="menu_item_artist" class="site_menu_item">Artists</span>
	<span id="menu_item_event" class="site_menu_item">Events</span>
	<span id="menu_item_organisation" class="site_menu_item">Institutions</span>
	<span id="menu_item_writing" class="site_menu_item">Writings</span>
	<a href="<?php echo get_permalink($about->ID); ?>" id="menu_item_about" class="extra_menu_item">About</a>

</div>