<?php
// Sounds of Europe 
// Eblog

class SOE_Eblog extends SOE_Type
{
	
	public function __construct($args)
	{
		parent::__construct($args);
		
		add_action( 'wp_insert_post', array(&$this, 'setLocation'), 10, 2 );
		
		if($this->isSelfAdmin())
		{ 
			add_action( 'admin_init', array(&$this, 'admin_init') );
		}
	}
	
	public function setLocation($post_id, $post = null)
	{
		if ($post->post_type == $this->tname) 
		{
			$soeloc = get_option('soe_location');
			$this->ensureCity($soeloc, GetLocation($soeloc)->name);
			add_post_meta($post_id, 'location', $soeloc, true);
		}
	}
	
	public function admin_init() 
	{
		add_meta_box( 'mb_artist_location', 'Location', array( &$this, "location" ), $this->tname, 'normal', 'high');
	}
}



?>