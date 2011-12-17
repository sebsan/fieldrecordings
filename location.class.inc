<?php
// Sounds of Europe
// Location

class SOE_Location extends SOE_Type
{
	public function __construct($args)
	{
		parent::__construct($args);
		
		$this->addMetaFields(array(
			'location_city' => 'City',
			'location_country' => 'Country code',
			'location_lat' => 'Latitude',
			'location_lon' => 'longitude'));
		
		$post_type = false;
		if(isset($_GET['post']))
			$post_type = get_post_type($_GET['post']);
		if ($post_type !== false && $post_type == $this->tname)
			add_action('admin_init', array(&$this, 'admin_init') );
		add_filter('name_save_pre', array(&$this, 'prepare_to_save'));
		add_filter('title_save_pre', array(&$this, 'prepare_to_save'));
	}
	
	public function prepare_to_save($arg_name) 
	{
		if ($_POST['post_type'] == 'soe_location') 
		{
			return $_POST['location_city'];
		}
		else
		{
			return $arg_name;
		}
	}
	
	
	public function admin_init() 
	{
		add_meta_box( 'mb_location_city', 'Name', array( &$this, "meta_name" ), $this->tname, 'normal', 'high');
	}
	
	public function meta_name($post, $metabox) 
	{
		$custom = get_post_custom($post->ID);
		echo '
		<div class="inside">
		<input type="text" name="location_city" style="width:98%;" value="'.$custom['location_city'][0].'" id="location_city">
		<p>'.$this->meta_fields['location_city'].'</p>
		</div>
	';
	}
	
}



?>