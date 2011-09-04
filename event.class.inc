<?php
// Sounds of Europe 
// Event

class SOE_Event extends SOE_Type
{
	
	public function __construct($args)
	{
		parent::__construct($args);
		$this->addMetaFields(array(
			'event_date_start' => 'Date of the event (or its beginning)',
			'event_date_end' => 'Date of the end of the event, if it runs on severals days',
			'event_time_start' => 'Time, if any.',
			'event_organization' => 'Organization'));
		// Initialize the methods
		add_action( 'admin_init', array(&$this, 'admin_init') );
		
		register_taxonomy_for_object_type('category', $this->tname);
	
	}
	

	public function admin_css()
	{
		wp_enqueue_style( 'datepicker', get_stylesheet_directory_uri(). '/css/jquery/datepicker.css');
	}
	
	public function admin_init() 
	{
		
		add_meta_box( "event-option-box", "Event details" , array( &$this, "meta_options" ), $this->tname, 'normal', 'core');
		add_meta_box( 'mb_event_location', 'Location', array( &$this, "location" ), $this->tname, 'normal', 'high');
	}
	
	
	public function meta_options($post, $metabox) 
	{
		global $wpdb;
		$custom = get_post_custom($post->ID);
		
		// date and time
		echo '
		<style>
		@import url("'. get_stylesheet_directory_uri(). '/css/jquery/datepicker.css");
		</style>
		<div style="float:left;width:50%">
			<div class="inside">
			<h4>Start</h4>
			<input class="datepicker" type="text" name="event_date_start"  tabindex="1" value="'.$custom['event_date_start'][0].'" id="event_date_start"/>
			<input type="text" name="event_time_start" id="event_time_start" value="'.$custom['event_time_start'][0].'"/>
			<p>'.$this->meta_fields['event_date_start'].'</p>
			</div>
			<div class="inside">
			<h4>End</h4>
			<input class="datepicker" type="text" name="event_date_end" style="width:98%;" tabindex="1" value="'.$custom['event_date_end'][0].'" id="event_date_end"/>
			<p>'.$this->meta_fields['event_date_end'].'</p>
			</div>
		</div>
		';
		
		echo '<div style="clear:left"></div>';
	}
	


}



?>