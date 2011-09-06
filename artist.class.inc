<?php
// Sounds of Europe
// Artist

class SOE_Artist extends SOE_Type
{
	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->addMetaFields(array(
			'artist_name' => 'Name of the artist.',
			'artist_bio' => 'Biography',
			'artist_use' => 'Use of field recording',
			'artist_url' => 'Website URL',
			'artist_image' => 'Image',
			'artist_sound' => 'Sound'));
		
		add_action( 'admin_init', array(&$this, 'admin_init') );
		add_filter('name_save_pre', array(&$this, 'prepare_to_save'));
		add_filter('title_save_pre', array(&$this, 'prepare_to_save'));
	}
	
	public function prepare_to_save($arg_name) 
	{
		if ($_POST['post_type'] == 'soe_artist') 
		{
			return $_POST['artist_name'];
		}
		else
		{
			return $arg_name;
		}
	}
	
	
	public function admin_init() 
	{
		add_meta_box( 'mb_artist_name', 'Name', array( &$this, "meta_name" ), $this->tname, 'normal', 'high');
		add_meta_box( 'mb_artist_bio', 'Information', array( &$this, "meta_content" ), $this->tname, 'normal', 'high');
		add_meta_box( 'mb_artist_url', 'Website', array( &$this, "meta_url" ), $this->tname, 'normal', 'high');
		add_meta_box( 'mb_artist_media', 'Media', array( &$this, "meta_media" ), $this->tname, 'normal', 'high');
		add_meta_box( 'mb_artist_location', 'Location', array( &$this, "location" ), $this->tname, 'normal', 'high');
	}
	
	public function meta_name($post, $metabox) 
	{
		$custom = get_post_custom($post->ID);
		echo '
		<div class="inside">
		<input type="text" name="artist_name" style="width:98%;" value="'.$custom['artist_name'][0].'" id="artist_name">
		<p>'.$this->meta_fields['artist_name'].'</p>
		</div>
	';
	}
	
	public function meta_url($post, $metabox) 
	{
		$custom = get_post_custom($post->ID);
		echo '
		<div class="inside">
		<input type="text" name="artist_url" style="width:98%;" value="'.$custom['artist_url'][0].'" id="artist_url">
		<p>'.$this->meta_fields['artist_url'].'</p>
		</div>
		';
	}
	
	public function meta_content($post, $metabox)
	{
		$custom = get_post_custom($post->ID);
		echo '
		<div class="inside">
			<h4>'.$this->meta_fields['artist_bio'].'</h4>
			<div id="artist_bio_box">
				<textarea class="artist_bio" id="artist_bio" name="artist_bio" >'.$custom['artist_bio'][0].'</textarea>
			</div>
			<h4>'.$this->meta_fields['artist_use'].'</h4>
			<div id="artist_use_box">
				<textarea class="artist_use" id="artist_use" name="artist_use" >'.$custom['artist_use'][0].'</textarea>
			</div>
		</div>
		
		<script type="text/javascript">
		jQuery(document).ready(function() 
		{
			if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) 
			{
				tinyMCE.execCommand("mceAddControl", false, "artist_bio");
				tinyMCE.execCommand("mceAddControl", false, "artist_use");
			}
			else
			{
				var tf = typeof( tinyMCE );
				var tfe = typeof( tinyMCE.execCommand );
				alert("No TinyMCE around /" + tf + "/" + tfe +"/");
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
	}
	
}



?>