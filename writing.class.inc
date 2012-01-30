<?php
// Sounds of Europe 
// Writing

class SOE_Writing extends SOE_Type
{
	
	public function __construct($args)
	{
	parent::__construct($args);
		$this->addMetaFields(array( 'writing_pdf' => 'Attached PDF.' , 'writing_author' => 'Author'));
		
		if($this->isSelfAdmin())
		{ 
			add_action( 'admin_init', array(&$this, 'admin_init') );
		}
	}
	
	public function admin_init() 
	{
		add_meta_box( 'mb_writing_author', 'Author', array( &$this, "author" ), $this->tname, 'normal', 'high');
		add_meta_box( 'mb_writing_pdf', 'Media', array( &$this, "meta_media" ), $this->tname, 'normal', 'high');
	}
	
	public function author($post, $metabox)
	{
		$custom = get_post_custom($post->ID);
		echo '
		<div class="inside">
		<input type="text" name="writing_author" style="width:98%;" value="'.$custom['writing_author'][0].'" id="writing_author">
		<!-- <p>'.$this->meta_fields['writing_author'].'</p> --> </div>';
	}
	
	public function meta_media($post, $metabox)
	{
		$custom = get_post_custom($post->ID);
		$this->getMedia('Attached PDF', 'Select a PDF to attach to this writing', 'pdf' ,'writing_pdf');
	}
	
}



?>