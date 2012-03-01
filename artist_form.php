<?php
/**

Sounds of Europe

%FILE%		user_form.php
%AUTHOR%	Pierre Marchand
%DATE%		2011-09-10

*/


session_start();
$failedToCreate = false;
$regform = 'regform';

if(isset($_SESSION['REG']) && $_SESSION['REG'] != session_id())
	return;

function va($a)
{
	if(isset($_POST[$a]) && $_POST[$a] != "")
		return true;
	return false;
}

function getPostValue($a)
{
	if(isset($_POST[$a]) && $_POST[$a] != "")
		return $_POST[$a];
	return "";
}

function getMaxUploadSize()
{
	$u_bytes = ini_get( 'upload_max_filesize' ) ;
	$p_bytes = ini_get( 'post_max_size' ) ;
	return min($u_bytes, $p_bytes);
}

if(isset($_POST['regform']))
{
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
				$medianame = slugify( $_FILES['artistmedia']['name']);
				$mediatitle = preg_replace('/\.[^.]+$/', '', $_FILES['artistmedia']['name']);
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
							'post_title' => $mediatitle,
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
				$medianame = slugify( $_FILES['artistpicture']['name']);
				$mediatitle = preg_replace('/\.[^.]+$/', '', $_FILES['artistpicture']['name']);
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
							'post_title' => $mediatitle,
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
		else
			$failedToCreate = true;
	}
	else
		$failedToCreate = true;
}
	
if(isset($_POST['regform_update']))
{
	if(va('artist_id') && va('artistname') && va('location') && va('bio') && va('email') && va('use'))
	{
		$artist = $_POST['artist_id'];
			wp_update_post(array(
			'ID' => $artist,
			'post_title' => (mysql_real_escape_string($_POST['artistname'])),
			'post_status' => 'publish',));
			if($artist > 0)
			{
				update_post_meta($artist, 'artist_name', mysql_real_escape_string($_POST['artistname']));
				update_post_meta($artist, 'artist_bio', $_POST['bio']);
				update_post_meta($artist, 'artist_use', $_POST['use']);
				update_post_meta($artist, 'artist_url', $_POST['website']);
				update_post_meta($artist, 'artist_email', $_POST['email']);
				update_post_meta($artist, 'location', $_POST['location']);
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
					$medianame = slugify( $_FILES['artistmedia']['name']);
					$mediatitle = preg_replace('/\.[^.]+$/', '', $_FILES['artistmedia']['name']);
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
								'post_title' => $mediatitle,
								'post_content' => '',
								'post_status' => 'publish'
							);
							$attach_id = wp_insert_attachment( $attachment, $uname, $artist);
							error_log('ATTACHED AUDIO: '. $attach_id);
							
							update_post_meta($artist, 'artist_sound', $attach_id);
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
					$medianame = slugify( $_FILES['artistpicture']['name']);
					$mediatitle = preg_replace('/\.[^.]+$/', '', $_FILES['artistpicture']['name']);
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
								'post_title' => $mediatitle,
								'post_content' => '',
								'post_status' => 'publish'
							);
							$attach_id = wp_insert_attachment( $attachment, $uname, $artist);
							error_log('ATTACHED IMAGE: '. $attach_id);
							require_once(ABSPATH . 'wp-admin/includes/image.php');
							$attach_data = wp_generate_attachment_metadata( $attach_id,  $uname);
							wp_update_attachment_metadata( $attach_id, $attach_data );
							update_post_meta($artist, 'artist_image', $attach_id);
						}
						else
							error_log('ERROR UNABLE TO MOVE: '. $mediafilename. ' ; '. $uname);
					}
					else
						error_log('ERROR FILETYPE: '.$wp_filetype[0]);
				}
				header('Location: '. get_permalink($artist));
			}
			else
				$failedToCreate = true;
	}
	else
		$failedToCreate = true;
}

if(isset($_GET['a_edit']))
{
	global $wpdb;
	
	$email = mysql_real_escape_string($_GET['a_edit']);
	
	$query = "
	SELECT * FROM ".$wpdb->posts." AS p 
	INNER JOIN ".$wpdb->postmeta." AS m 
	ON p.ID = m.post_id 
	WHERE (p.post_type = 'soe_artist' AND m.meta_key = 'artist_email' AND m.meta_value = '".$email."');
	";
	// 	echo $query;
	$artist = $wpdb->get_results($query, OBJECT);
	
	if($artist)
	{
		mt_srand();
		$_SESSION['E_TOKEN'] = mt_rand(1111,9999);
		$a = $artist[0];
		$mret = mail($email, 'Sounds of Europe profile token', "Follow this url \n<".get_permalink($post->ID)."?a_id=".$a->ID."&a_tok=".$_SESSION['E_TOKEN'].">");
// 		error_log(get_permalink($post->ID)."?a_id=".$a->ID."&a_tok=".$_SESSION['E_TOKEN']);
		
		if($mret === true)
			echo '<h3>You should receive an e-mail shortly giving you access to your profile for editing.</h3>';
		else
			echo '<h3>Something went wrong when tried to send an e-mail to: '.$email.'</h3>';
		//."<a href=\"".get_permalink($post->ID)."?a_id=".$a->ID."&a_tok=".$_SESSION['E_TOKEN']."\">THE LINK</a>";
		return;
	}
}
$isEditing = false;
if(isset($_GET['a_id']) && isset($_GET['a_tok']))
{
	if(isset($_SESSION['E_TOKEN']) && $_GET['a_tok'] == $_SESSION['E_TOKEN'])
	{
		$ep = get_post($_GET['a_id']);
		$epc = get_post_custom($ep->ID);
		$_POST['artistname'] = $ep->post_title;
		$_POST['bio'] = $epc['artist_bio'][0];
		$_POST['use'] = $epc['artist_use'][0];
		$_POST['website'] = $epc['artist_url'][0];
		$_POST['email'] = $epc['artist_email'][0];
		$_POST['location'] = $epc['location'][0];
		$_POST['location_name'] = GetLocation($epc['location'][0])->name;
		
		$isEditing = true;
		$regform = 'regform_update';
	}
}
	

/// DISPLAY FORM
/// V V V

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


#reedit{
	position:absolute;
	bottom:0;
	left:24px;
	padding:12px;
	border-left: 1px solid #18959A;
	border-right: 1px solid #18959A;
	border-top: 1px solid #18959A;
	color:#fff;
	font-family:helvetica_serif;
	font-size:9pt;
	background-color:#18959A;
}


label 
{
	color:#18959A;
	font-family: helvetica_serif;
	background-color:white;
	font-size: 8pt;
	line-height: 12pt;
	text-align: center;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	border: 1px solid #18959A;
	margin-left: 30px;
	padding: 3px; 
}

input[type~="file"], input[type~="text"]
{
	color:black;
	font-family: free_sans;
	font-size: 10pt;
	width: 300px;
	line-height: 12pt;
	border:1px solid #18959A;
	margin-left: 30px;
	margin-bottom: 10px;
	padding: 0 0 0px 0;
}

input.submit
{
	color:white;
	width: 70px;
	background-color:#18959A;
	font-family: helvetica_serif;
	font-size: 8pt;
	line-height: 12pt;
	text-align: center;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	border: 1px solid #18959A;
	margin-left: 30px;
	margin-top: 20px;
	padding: 3px; 
}


input.submit:hover
{
	color:#18959A;
	width: 70px;
	font-family: helvetica_serif;
	background-color:white;
	font-size: 8pt;
	line-height: 12pt;
	text-align: center;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	border: 1px solid #18959A;
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
	border:1px solid #18959A;
	margin-left: 30px;
	margin-bottom: 10px;


}

#form_explain{
	position: absolute;
	left: 40px;
	top:40px;
	color:#18959A;
	font-family:helvetica_serif;
	font-size:9pt;
	line-height:13pt;
	width:260px;
}

#form_explain a{
	color:#18959A;
}

#form_explain a:hover{
	color:#FC264A;
}


strong{
	color:#FC264A;
	font-size:100%;
}

#left{
	position: absolute;
	left: 300px;
	top:40px;
}

#right{
	position: absolute;
	left: 650px;
	top:40px;
}

#log{
	position: absolute;
	left: 1000px;
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
				jQuery( "#location_name" ).val(ui.item.label);
				jQuery( "#location" ).val(ui.item.value);
				return false;
			},
		});
});
// ]]>
</script>

</head>
<body>
<?php 
// echo '<!--'; 
// print_r($_POST);
// foreach($_POST as $k=>$v)
// {
// 	echo $k .' = ' . $v ."\n";
// }
// echo 'a='.getPostValue('artistname');
// echo '-->'; 
?>
<?php 
if($failedToCreate)
	echo '<h4 style="position:absolute;top:0;left:0;color:#666">Failed to submit your informations, please try again.</h2>';
?>

<form name="theForm" method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="<?php echo $regform; ?>" value="1"/>
<?php if($isEditing){ echo '<input type="hidden" name="artist_id" value="'.$_GET['a_id'].'"/>'; } ?>
<div id="form_explain">
<p>
<strong>Sounds of Europe</strong> is a project that acknowledges and follows the increase of <strong>field recording</strong> activity in music, art and sciences in recent years.
One of the objectives of the project is to create <strong>a platform for sound oriented organizations and sound artists working with field recordings.</strong>
In order to stimulate exchanges, please join!
</p>
<p>
If you don't want to receive the 'Sounds of Europe' newsletter, please thick the box<input type="checkbox" value="mailinglist"/>  

Your email address will in any case not be used for commercial purposes.
</p>
<p>
You can always contact us if questions or problems arise > <a href="mailto:info@soundsofeurope.eu">info@soundsofeurope.eu</a>
</p>
<p style="margin-top:10em">
* = mandatory
</p>
</div>

<div id="left">
	<div>
	<label for="artistname">Name *</label>
	</div>
	<div>
	<input type="text" name="artistname" value="<?php echo getPostValue('artistname'); ?>"/>
	</div>
	<div>
	<label for="website">Website</label>
	</div>
	<div>
	<input type="text" name="website" value="<?php echo getPostValue('website'); ?>"/>
	</div>
	<div>
	<label for="email">E-mail *</label>
	</div>
	<div>
	<input type="text" name="email" value="<?php echo getPostValue('email'); ?>"/>
	</div>
	<div>
	<label for="location_search">City *</label>
	</div>
	<div>
	<input type="hidden" id="location" name="location" value="<?php echo getPostValue('location'); ?>"/> 
	<input type="hidden" id="location_name" name="location_name" value="<?php echo getPostValue('location_name'); ?>"/> 
	</div>
	<div>
	<input type="text" id="location_search" value="<?php echo getPostValue('location_name'); ?>"/> 
	</div>
	<div>
	<label for="bio">Biography *</label>
	</div>
	<div>
	<textarea name="bio"><?php echo getPostValue('bio'); ?></textarea>
	</div>
</div>

<div id="right">
	<div>
	<label for="use">Description of work with field recording *</label>
	</div>
	<div>
	<textarea name="use"><?php echo getPostValue('use'); ?></textarea>
	</div>

	<div>
	<label for="artistmedia">Upload Track (type: mp3, ogg; Max Size: <?php echo getMaxUploadSize(); ?>)</label>
	</div>
	<div>
	<input type="file" name="artistmedia" value="<?php echo getPostValue('artistmedia'); ?>"/>
	</div>
	
	<div>
	<label for="artistpicture">Upload Image (Type: jpeg, png; Max Size: <?php echo getMaxUploadSize(); ?>)</label>
	</div>
	<div>
	<input type="file" name="artistpicture" value="<?php echo getPostValue('artistpicture'); ?>"/>
	</div>
	

	<div>
	<input class="submit" type="submit" value="Submit"/>
	</div>
</div>
<div id="log">
<img src="<?php echo get_bloginfo('stylesheet_directory') . '/sounds-of-eu_logo.png' ?>"/>
</div>

</form>

<form name="reedit" id="reedit" action="<?php echo get_permalink($post->ID); ?>" method="get">
<span>If you wish to edit your profile, enter your e-mail address here and submit:</span> <input type="text" name="a_edit"/><input type="submit" class="submit"/>
</form>

</body>
</html>
