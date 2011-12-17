<?php
// Sounds of Europe 
// Writing

class SOE_Writing extends SOE_Type
{
	
	public function __construct($args)
	{
	parent::__construct($args);
		$this->addMetaFields(array( 'writing_pdf' => 'Attached PDF.' ));
		
		$post_type = false;
		if(isset($_GET['post']))
			$post_type = get_post_type($_GET['post']);
		if ($post_type !== false && $post_type == $this->tname) 
			add_action( 'admin_init', array(&$this, 'admin_init') );
	}
	
	public function admin_init() 
	{
		add_meta_box( 'mb_writing_pdf', 'Media', array( &$this, "meta_media" ), $this->tname, 'normal', 'high');
	}
	
	public function meta_media($post, $metabox)
	{
		$custom = get_post_custom($post->ID);
		$this->getMedia('Attached PDF', 'Select a PDF to attach to this writing', 'pdf' ,'writing_pdf');
	}
	
}



?>