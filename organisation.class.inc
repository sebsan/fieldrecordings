<?php
// Sounds of Europe
// Organisation

class SOE_Organisation extends SOE_Type
{
	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->addMetaFields(array(
			'organisation_name' => 'Name of the organisation.',
			'organisation_mission' => 'Mission statement',
			'organisation_url' => 'Website URL',
			'organisation_image' => 'Image',
			'organisation_sound' => 'Sound',
			'organisation_user' => 'Wordpress user conected to this organisation'));
		
		add_action( 'admin_init', array(&$this, 'admin_init') );
		add_filter('name_save_pre', array(&$this, 'prepare_to_save'));
		add_filter('title_save_pre', array(&$this, 'prepare_to_save'));
	}
	
	public function prepare_to_save($arg_name) 
	{
		if ($_POST['post_type'] == 'soe_organisation') 
		{
			return $_POST['organisation_name'];
		}
		else
		{
			return $arg_name;
		}
	}
	
	
	public function admin_init() 
	{
		add_meta_box( 'mb_organisation_name', 'Name', array( &$this, "meta_name" ), $this->tname, 'normal', 'high');
		add_meta_box( 'mb_organisation_user', 'User', array( &$this, "meta_user" ), $this->tname, 'normal', 'high');
		add_meta_box( 'mb_organisation_mission', 'Information', array( &$this, "meta_content" ), $this->tname, 'normal', 'high');
		add_meta_box( 'mb_organisation_url', 'Website', array( &$this, "meta_url" ), $this->tname, 'normal', 'high');
		add_meta_box( 'mb_organisation_media', 'Media', array( &$this, "meta_media" ), $this->tname, 'normal', 'high');
		add_meta_box( 'mb_organisation_location', 'Location', array( &$this, "location" ), $this->tname, 'normal', 'high');
	}
	
	public function meta_name($post, $metabox) 
	{
		$custom = get_post_custom($post->ID);
		echo '
		<div class="inside">
		<input type="text" name="organisation_name" style="width:98%;" value="'.$custom['organisation_name'][0].'" id="organisation_name">
		<p>'.$this->meta_fields['organisation_name'].'</p>
		</div>
	';
	}
	
	public function meta_user($post, $metabox)
	{
		$custom = get_post_custom($post->ID);
		$users = get_users();
		
		$options = '';
		foreach($users as $user)
		{
			if($user->ID == $custom['organisation_user'][0])
				$options .= '<option value="'.$user->ID.'" selected="selected">'.$user->display_name.'</option>';
			else
				$options .= '<option value="'.$user->ID.'">'.$user->display_name.'</option>';
		}
		echo '
		<div class="inside">
		<select name="organisation_user">'.$options.'</select>
		<p>'.$this->meta_fields['organisation_user'].'</p>
		</div>
		';
	}
	
	public function meta_url($post, $metabox) 
	{
		$custom = get_post_custom($post->ID);
		echo '
		<div class="inside">
		<input type="text" name="organisation_url" style="width:98%;" value="'.$custom['organisation_url'][0].'" id="organisation_url">
		<p>'.$this->meta_fields['organisation_url'].'</p>
		</div>
		';
	}
	
	public function meta_content($post, $metabox)
	{
		$custom = get_post_custom($post->ID);
		echo '
		<div class="inside">
			<h4>'.$this->meta_fields['organisation_mission'].'</h4>
			<div id="organisation_mission_box">
				<textarea class="organisation_mission" id="organisation_mission" name="organisation_mission" >'.$custom['organisation_mission'][0].'</textarea>
			</div>
		</div>
		
		<script type="text/javascript">
		jQuery(document).ready(function() 
		{
			if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) 
			{
				tinyMCE.execCommand("mceAddControl", false, "organisation_mission");
			}
			else
			{
				var tf = typeof( tinyMCE );
				alert("No TinyMCE around /" + tf +"/");
			}
	});
	</script>
	';
		// Dumb call to have a tinyMCE object
		wp_tiny_mce( false , array( "editor_selector" => "a_nice_textarea" ) );
		
	}
	
	public function meta_media($post, $metabox) 
	{
		$custom = get_post_custom($post->ID);
		echo '
		';
		$this->getMedia('Upload Image', 'Select an image for this organisation', 'image');
	}
	
}



?>