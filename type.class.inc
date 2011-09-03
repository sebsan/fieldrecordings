<?php 

// Sounds of Europe
// Custom Type Template

class SOE_Type
{
	public $meta_fields = array(
		'location' => 'Location'
		);
	
	public $locations;
	
	function __construct($args)
	{
		$name = $args['name'];
		$names = isset($args['names']) ? $args['names'] : $name . 's';
		$this->lcname = strtolower($name);
		$this->menu = $args['menu'];
		$support = $args['support'];
		$this->tname = 'soe_'.$this->lcname;
		
		$labels = array(
		'name' => __( $names ),
		'singular_name' => __( $name ),
		'add_new' => __( 'Add New', $name ),
		'add_new_item' => __( 'Add New '. $name ),
		'edit_item' => __( 'Edit '.$name ),
		'new_item' => __( 'New '.$name ),
		'view_item' => __( 'View '.$name ),
		'search_items' => __( 'Search '.$names ),
		'not_found' =>  __( 'No '.$name.' found in search' ),
		'not_found_in_trash' => __( 'No '.$name.' found in Trash' ),
		);
		
		$siteArgs = array(
		'labels' => $labels,
		'public' => true,
		'show_ui' => true,
		'_builtin' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'rewrite' => array('slug' => $this->lcname),
		'query_var' => $this->tname,
		'supports' => $support,
		'menu_position' => 5,
		'taxonomies' => array('post_tag'),
		'has_archive' => true,
		);
		
		register_post_type( $this->tname , $siteArgs );
		
		add_action( 'wp_insert_post', array(&$this, 'wp_insert_post'), 10, 2 );
	}
	
	public function wp_insert_post($post_id, $post = null) 
	{
		if ($post->post_type == $this->tname) 
		{
			foreach ($this->meta_fields as $key => $desc) 
			{
				
				$value = @$_POST[$key];
				if (empty($value)) 
				{
					delete_post_meta($post_id, $key);
					continue;
				}
				if (!is_array($value)) 
				{
					if (!update_post_meta($post_id, $key, $value)) 
					{
						add_post_meta($post_id, $key, $value);
					}
				} 
				else 
				{
					delete_post_meta($post_id, $key);
					foreach ($value as $entry) add_post_meta($post_id, $key, $entry);
				}
			}
		}
	}
	
	public function addMetaFields($mf)
	{
		$this->meta_fields = array_unique(array_merge($this->meta_fields ,$mf));
	}
	
	public function WP_type()
	{
		return $this->tname;
	}
	
	public function location($post, $metabox) 
	{
		$custom = get_post_custom($post->ID);
		$loc = NULL;
		if(isset($custom['location'][0]))
		{
			global $wpdb;
			$locs = $wpdb->get_results("
			SELECT c.geonameid,c.name,c.country_code,a.name AS codename FROM cities15 AS c LEFT JOIN admin1codes AS a ON (a.admin = CONCAT(c.country_code,'.',c.admin1))
			WHERE c.geonameid = ".$custom['location'][0].";" , OBJECT);
// 			print_r($locs);
			if($locs != NULL)
			{
				$loc = $locs[0];
			}
		}
		$locSource = get_bloginfo('stylesheet_directory').'/cities.php';
		if($loc === NULL)
		{
			echo '
			<div>
			<input type="hidden" id="location" name="location"/> 
			<input type="text" id="location_search"/> 
			</div>';
		}
		else
		{
			echo '
			<div>
			<input type="hidden" id="location" name="location" value="'.$loc->geonameid.'" /> 
			<input type="text" id="location_search" value="'.$loc->name.', '.$loc->codename.' ('.$loc->country_code.')" />
			</div>';
		}
		
		echo '
		<script type="text/javascript">
		// <![CDATA[
		
		jQuery(document).ready(function()
		{
			
	
	jQuery( "#location_search" ).autocomplete(
		{
			minLength: 0,
			source: "'.$locSource.'",
			focus: function( event, ui ) 
			{
				jQuery( "#location_search" ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) 
			{
				jQuery( "#location_search" ).val(ui.item.label);
				jQuery( "#location" ).val(ui.item.value);
				return false;
			},
		});
	});
	// ]]>
	</script>
	';
	
	
	}
	
}


?>