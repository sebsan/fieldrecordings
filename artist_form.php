<?php
/**

Sounds of Europe

%FILE%		user_form.php
%AUTHOR%	Pierre Marchand
%DATE%		2011-09-10

*/


if($_SESSION['REG'] != session_id())
	return;

function va($a)
{
	if(isset($_POST[$a]) && $_POST[$a] != "")
		return true;
	return false;
}
if(va('artistname') && va('location') && va('bio') && va('email') && va('use'))
{
	$artist = wp_insert_post(array(
		'post_type' => 'soe_artist',
		'post_title' => (mysql_real_escape_string($_POST['artistname'])),
		'post_status' => 'publish',));
	if($artist > 0)
	{
		add_post_meta($artist, 'artist_name', mysql_real_escape_string($_POST['artistname']));
		add_post_meta($artist, 'artist_bio', $_POST['bio']);
		add_post_meta($artist, 'artist_use', $_POST['use']);
		add_post_meta($artist, 'artist_url', $_POST['website']);
		add_post_meta($artist, 'artist_email', $_POST['email']);
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
				$uname = $upload_dir['path'].'/'. $medianame ;
				if( move_uploaded_file ( $mediafilename , $uname))
				{
					$attachment = array(
						'post_mime_type' => $_FILES['artistmedia']['type'],
						'post_title' => $medianame,
						'post_content' => '',
						'post_status' => 'publish'
						);
						$attach_id = wp_insert_attachment( $attachment, $uname, $artist);
						error_log('ATTACHED AUDIO: '. $attach_id);
						
					add_post_meta($artist, 'artist_sound', $attach_id);
				}
				else
					error_log('ERROR UNABLE TO MOVE: '. $mediafilename. ' ; '. $uname);
			}
			else
				error_log('ERROR FILETYPE: '.$wp_filetype[0]);
		}
		if(isset($_FILES['artistpicture']))
		{
			$mediafilename = $_FILES['artistpicture']['tmp_name'];
			$medianame = preg_replace('/\.[^.]+$/', '', $_FILES['artistpicture']['name']);
			$wp_filetype = explode('/', $_FILES['artistpicture']['type']);
			error_log('MEDIA: '.$mediafilename.'[]'.$wp_filetype[0]);
			if($wp_filetype[0] == 'image')
			{
				$upload_dir = wp_upload_dir();
				$uname = $upload_dir['path'].'/'. $medianame;
				if( move_uploaded_file ( $mediafilename , $uname))
				{
					$attachment = array(
						'post_mime_type' => $_FILES['artistpicture']['type'],
						'post_title' => $medianame,
						'post_content' => '',
						'post_status' => 'publish'
						);
						$attach_id = wp_insert_attachment( $attachment, $uname, $artist);
						error_log('ATTACHED IMAGE: '. $attach_id);
						require_once(ABSPATH . 'wp-admin/includes/image.php');
						$attach_data = wp_generate_attachment_metadata( $attach_id,  $uname);
						wp_update_attachment_metadata( $attach_id, $attach_data );
						add_post_meta($artist, 'artist_image', $attach_id);
				}
				else
					error_log('ERROR UNABLE TO MOVE: '. $mediafilename. ' ; '. $uname);
			}
			else
				error_log('ERROR FILETYPE: '.$wp_filetype[0]);
		}
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

@font-face {
  font-family:helvetica_serif;
  src:url(<?php echo '"'. get_bloginfo('stylesheet_directory') . '/OSP_helvetica_serif.ttf"' ?>);
}

@font-face {
  font-family:free_sans;
  src:url(<?php echo '"'. get_bloginfo('stylesheet_directory') . '/FreeSans.ttf"'?>);
}

@font-face {
  font-family:free_sansBold;
  src:url(<?php echo '"'. get_bloginfo('stylesheet_directory') . '/FreeSansBold.ttf"'?>);
}

@font-face {
  font-family:Fanwood;
  src:url(<?php echo '"'. get_bloginfo('stylesheet_directory') . '/Fanwood.otf"'?>);
}

*{
	border:none;
}

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
	color:grey;
}
.ui-autocomplete li a.ui-state-hover
{
	color:#000;
}


label 
{
	color:blue;
	font-family: helvetica_serif;
	background-color:white;
	font-size: 8pt;
	line-height: 12pt;
	text-align: center;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	border: 1px solid blue;
	margin-left: 30px;
	padding: 3px; 
}

input 
{
	color:black;
	font-family: free_sans;
	font-size: 10pt;
	width: 300px;
	line-height: 12pt;
	border:1px solid blue;
	margin-left: 30px;
	margin-bottom: 10px;
	padding: 0 0 0px 0;
}

input.submit
{
	color:white;
	width: 70px;
	background-color:blue;
	font-family: helvetica_serif;
	font-size: 8pt;
	line-height: 12pt;
	text-align: center;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	border: 1px solid blue;
	margin-left: 30px;
	margin-top: 20px;
	padding: 3px; 
}


input.submit:hover
{
	color:blue;
	width: 70px;
	font-family: helvetica_serif;
	background-color:white;
	font-size: 8pt;
	line-height: 12pt;
	text-align: center;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	border: 1px solid blue;
	margin-left: 30px;
	margin-top: 20px;
	padding: 3px; 
}

textarea 
{
	color:black;
	font-family: free_sans;
	font-size: 10pt;
	width: 300px;
	height: 200px;
	line-height: 12pt;
	border:1px solid blue;
	margin-left: 30px;
	margin-bottom: 10px;


}

#left{
position: fixed;
left: 200px;
top:40px;
}

#right{
position: fixed;
left: 550px;
top:40px;
}

#log{
position: fixed;
left: 900px;
top:40px;
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
<div id="left">
	<div>
	<label for="artistname">Name</label>
	</div>
	<div>
	<input type="text" name="artistname"/>
	</div>
	<div>
	<label for="website">Website</label>
	</div>
	<div>
	<input type="text" name="website"/>
	</div>
	<div>
	<label for="email">E-mail</label>
	</div>
	<div>
	<input type="text" name="email"/>
	</div>
	<div>
	<label for="location_search">Location</label>
	</div>
	<div>
	<input type="hidden" id="location" name="location"/> 
	</div>
	<div>
	<input type="text" id="location_search"/> 
	</div>
	<div>
	<label for="bio">Biography</label>
	</div>
	<div>
	<textarea name="bio"></textarea>
	</div>
</div>

<div id="right">
	<div>
	<label for="use">Use of field recordings</label>
	</div>
	<div>
	<textarea name="use"></textarea>
	</div>

	<div>
	<label for="artistmedia">Upload Track</label>
	</div>
	<div>
	<input type="file" name="artistmedia"/>
	</div>
	
	<div>
	<label for="artistpicture">Upload Image</label>
	</div>
	<div>
	<input type="file" name="artistpicture"/>
	</div>
	

	<div>
	<input class="submit" type="submit" value="Submit"/>
	</div>
</div>
<div id="log">
<img src="<?php echo get_bloginfo('stylesheet_directory') . '/sounds-of-eu_logo.png' ?>"/>
</div>

</form>

</body>
</html>