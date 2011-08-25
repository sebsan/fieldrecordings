<?php
header("Content-type: text/javascript");


$artist_box_template = '
<div class="artist_box" id="artist_box_##ID##">
<input type="hidden" name="project_artists" value="##ID##"/>
<span>Remove</span> ##NAME## 
</div>
';

$qA = new WP_Query();
$artists = $qV->query('post_type=qo2_artist');

echo '
</div>
<script type="text/javascript">
// <![CDATA[
var qo2_artists = [
';
$sep = "";
$first = true;
foreach($artists as $artist)
{
	echo $sep.'
	{
		value:"'.$artist->ID.'",
		label:"'.$artist->post_title.'"
	}';
	if($first === true)
	{
		$sep = ",";
		$first = false;
	}
}
echo '];

jQuery( "#project_artists_search" ).autocomplete(
{
	minLength: 0,
						 source: qo2_artists,
						 select: function( event, ui ) {
							 var tpl = "'.$artist_box_template.'";
							 tpl.replace("##ID##", ui.item.value);
							 tpl.replace("##NAME##", ui.item.label);
							 jQuery("#project_artists_box").append(tpl);
							 
							 return false;
						 }
});
';

?>