<?php
/**

Sounds of Europe

%FILE%		user_form.php
%AUTHOR%	Pierre Marchand
%DATE%		2011-09-10

*/
// $sessionReg = md5('bhjus678shdj gdsh6dds 89d7s styi sudt ' . $_SERVER['REMOTE_ADDR'];


if($_SESSION['REG'] != session_id())
	return;


if(isset($_POST['artistname']) || isset($_POST['location']) || isset($_POST['bio']))
{
	$artist = wp_insert_post(array(
		'post_type' => 'soe_artist',
		'post_title' => (mysql_real_escape_string($_POST['artistname'])),
		'post_status' => 'publish',));
	if($artist > 0)
	{
		add_post_meta($artist, 'artist_bio', $_POST['bio']);
		add_post_meta($artist, 'artist_use', $_POST['use']);
		add_post_meta($artist, 'artist_url', $_POST['website']);
		add_post_meta($artist, 'location', $_POST['location']);
		$geonameid = $_POST['location'];
		{
			global $wpdb;
			$locs = $wpdb->get_results("
			SELECT c.geonameid,c.name,c.country_code,a.name AS codename FROM cities15 AS c LEFT JOIN admin1codes AS a ON (a.admin = CONCAT(c.country_code,'.',c.admin1))
			WHERE c.geonameid = ".$geonameid.";" , OBJECT);
			if($locs != NULL)
			{
				$wplocs = $wpdb->get_results("
				SELECT * FROM wp_posts AS p 
				INNER JOIN wp_postmeta AS m 
				ON p.ID = m.post_id 
				WHERE (p.post_type = 'soe_city' AND m.meta_key = 'location' AND m.meta_value = '".$genonameid."') ;
				", OBJECT);
				if(count($wplocs) == 0)
				{
					$wplocdata = array(
						'post_title' => $locs[0]->name,
						'post_status' => 'publish', 
						'post_type' => 'soe_city',
					);
					$wplocid =  wp_insert_post($wplocdata);
					if($wplocid > 0)
					{
						add_post_meta($wplocid, 'location', $genonameid, true);
					}
				}
			}
		}
// 		print_r($_FILES);
		if(isset($_FILES['artistmedia']) )
		{
			$mediafilename = $_FILES['artistmedia']['tmp_name'];
			$medianame = preg_replace('/\.[^.]+$/', '', $_FILES['artistmedia']['name']);
			$wp_filetype = explode('/', $_FILES['artistmedia']['type']);
			error_log('MEDIA: '.$mediafilename.'[]'.$wp_filetype[0]);
			if($wp_filetype[0] == 'audio')
			{
				$upload_dir = wp_upload_dir();
				if( move_uploaded_file ( $mediafilename , $upload_dir['path'].'/'. $medianame))
				{
					$attachment = array(
						'post_mime_type' => $_FILES['artistmedia']['type'],
						'post_title' => $medianame,
						'post_content' => '',
						'post_status' => 'publish'
						);
						$attach_id = wp_insert_attachment( $attachment, $mediafilename, $artist);
						error_log('ATTACHED: '. $attach_id);
						
					add_post_meta($artist, 'artist_sound', $attach_id);
				}
				else
					error_log('ERROR UNABLE TO MOVE: '. $mediafilename. ' ; '. $upload_dir['path']);
			}
			else
				error_log('ERROR FILETYPE: '.$wp_filetype[0]);
		}
		else
			print_r($_POST);
		header('Location: '. get_permalink($artist));
	}
}
	
	

/// DISPLAY FORM
/// V V V

session_start();
$_SESSION['REG'] = session_id();

wp_deregister_script('raphael');
wp_deregister_script('soe');
wp_enqueue_script('jquery-ui-autocomplete',  get_stylesheet_directory_uri(). '/js/jquery-ui-autocomplete.js' , array('jquery-ui-core'));

$locSource = get_bloginfo('stylesheet_directory').'/cities.php';



?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" lang="en"> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>User Registration Form</title>
<?php 
wp_head(); 
?>

<style media="screen" type="text/css">
.ui-autocomplete
{
	list-style:none;
/* 	border:1px solid #777; */
	background-color:white;
	
}
.ui-autocomplete li
{
	display:block;
}
.ui-autocomplete li a
{
	color:#777;
}
.ui-autocomplete li a.ui-state-hover
{
	color:#000;
}
</style>

<script type="text/javascript">
// <![CDATA[

jQuery(document).ready(function()
{
	jQuery( "#location_search" ).autocomplete(
		{
			minLength: 0,
			source: "<?php echo $locSource ?>",
			focus: function( event, ui ) 
			{
				jQuery( "#location_search" ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) 
			{
				jQuery( "#location_search" ).val(ui.item.label);
				jQuery( "#location" ).val(ui.item.value);
				return false;
			},
		});
});
// ]]>
</script>

</head>
<body>

<form name="regform" method="post" action="" enctype="multipart/form-data">
<div>
<label for="artistname">Name</label>
<input type="text" name="artistname"/>
</div>
<div>
<label for="website">Website</label>
<input type="text" name="website"/>
</div>


<div>
<label for="location_search">Location</label>
<input type="hidden" id="location" name="location"/> 
<input type="text" id="location_search"/> 
</div>




<div>
<label for="bio">Biography</label>
<textarea name="bio"></textarea>
</div>
<div>
<label for="use">Use of field recordings</label>
<textarea name="use"></textarea>
</div>

<div>
<label for="artistmedia">Upload track</label>
<input type="file" name="artistmedia"/>
</div>

<div>
<input type="submit" value="Submit"/>
</div>

</form>

</body>
</html>