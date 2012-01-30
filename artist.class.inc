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
			'artist_email' => 'E-mail adress of the artist.',
			'artist_bio' => 'Biography',
			'artist_use' => 'Use of field recording',
			'artist_url' => 'Website URL',
			'artist_image' => 'Image',
			'artist_sound' => 'Sound'));
		
		if($this->isSelfAdmin())
		{ 
			add_action( 'admin_init', array(&$this, 'admin_init') );
		}
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
		
		error_log("admin_init (artist)");
		
// 		wp_enqueue_script(array('jquery', 'editor', 'thickbox', 'media-upload'));
// 		wp_enqueue_style('thickbox');
// 		wp_tiny_mce( false , array( "editor_selector" => "artist_bio"));
// 		wp_tiny_mce( false , array( "editor_selector" => "artist_use"));
		
		add_meta_box( 'mb_artist_name', 'Name', array( &$this, "meta_name" ), $this->tname, 'normal', 'high');
		add_meta_box( 'mb_artist_location', 'Location', array( &$this, "location" ), $this->tname, 'normal', 'high');
		add_meta_box( 'mb_artist_url', 'Website & E-mail', array( &$this, "meta_url" ), $this->tname, 'normal', 'high');
		add_meta_box( 'mb_artist_bio', 'Information', array( &$this, "meta_content" ), $this->tname, 'normal', 'high');
		add_meta_box( 'mb_artist_media', 'Media', array( &$this, "meta_media" ), $this->tname, 'normal', 'high');
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
		
		<input type="text" name="artist_email" style="width:98%;" value="'.$custom['artist_email'][0].'" id="artist_email">
		<p>'.$this->meta_fields['artist_email'].'</p>
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
				<textarea style="width:100%;height:200px;" class="artist_bio" id="artist_bio" name="artist_bio" >'.$custom['artist_bio'][0].'</textarea>
			</div>
			<h4>'.$this->meta_fields['artist_use'].'</h4>
			<div id="artist_use_box">
				<textarea style="width:100%;height:200px;" class="artist_use" id="artist_use" name="artist_use" >'.$custom['artist_use'][0].'</textarea>
			</div>
		</div>
		
		
		';
		
	}
	
	public function meta_media($post, $metabox) 
	{
		$this->getMedia('Image', 'Select an image for this artist', 'image' ,'artist_image');
		$this->getMedia('Sound', 'Select a sound track for this artist', 'audio' ,'artist_sound');
	}
	
}



?>