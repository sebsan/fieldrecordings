<?php
// Sounds of Europe 
// Eblog

class SOE_Eblog extends SOE_Type
{
	
	public function __construct($args)
	{
		parent::__construct($args);
		
		add_action( 'wp_insert_post', array(&$this, 'setLocation'), 10, 2 );
	}
	
	public function setLocation($post_id, $post = null)
	{
		if ($post->post_type == $this->tname) 
		{
			add_post_meta($post_id, 'location', get_option('soe_location'), true);
		}
	}
}



?>