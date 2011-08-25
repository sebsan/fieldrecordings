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
		
		
// 		$venues = $wpdb->get_results("
// 		SELECT * 
// 		FROM ". $wpdb->posts ." 
// 		WHERE post_type = 'soe_venue' AND post_status = 'publish';" , OBJECT);
// 		
// 		echo '
// 		<div style="margin-left:50%">
// 		<h4>Select venue</h4>
// 		<select name="venue" id="venue">
// 		';
// 		foreach($venues as $venue)
// 		{
// 			$selected = $venue->ID == $custom['venue'][0] ? "selected" : "";
// 			echo '<option value="'.$venue->ID.'"'.$selected.'>'.$venue->post_title.'</option>
// 			';
// 		}
// 		echo '
// 		</select>
// 		</div>
// 		';
// 		
// 		
// 		$projects = $wpdb->get_results("
// 		SELECT * 
// 		FROM ". $wpdb->posts ." 
// 		WHERE post_type = 'soe_project' AND post_status = 'publish';" , OBJECT);
// 
// 		echo '
// 		<div style="margin-left:50%">
// 		<h4>Select project</h4>
// 		<select name="project" id="project">
// 		<option value="0">No project</option>
// 		';
// 		foreach($projects as $project)
// 		{
// 			$terms = wp_get_object_terms($project->ID, 'post_tag');
// 			if(/*($terms === WP_Error) 
// 			|| (count($terms) == 0)
// 			||*/ ((count($terms) > 0) && ($terms[0]->name == 'project')) )
// 			{
// 				$selected = $project->ID == $custom['project'][0] ? "selected" : "";
// 				echo '<option value="'.$project->ID.'"'.$selected.'>'.$project->post_name.'</option>
// 				';
// 			}
// 		}
// 		echo '
// 		</select>
// 		</div>
// 		';
		
		echo '<div style="clear:left"></div>';
	}
	


}



?>