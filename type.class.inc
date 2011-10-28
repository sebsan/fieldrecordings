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
		'show_ui' => $this->menu,
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
		error_log( 'wp_insert_post => '. $post->post_type);
		if ($post->post_type == $this->tname) 
		{
			foreach ($this->meta_fields as $key => $desc) 
			{
				
				$value = @$_POST[$key];
				error_log( $key .' => '. $value);
				if (empty($value)) 
				{
					delete_post_meta($post_id, $key);
					continue;
				}
				if (!is_array($value)) 
				{
					if (!update_post_meta($post_id, $key, $value)) 
					{
						add_post_meta($post_id, $key, $value, true);
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
	
	public function ensureCity($genonameid, $name)
	{
		global $wpdb;
		$wplocs = $wpdb->get_results("
		SELECT * FROM wp_posts AS p 
		INNER JOIN wp_postmeta AS m 
		ON p.ID = m.post_id 
		WHERE (p.post_type = 'soe_city' AND m.meta_key = 'location' AND m.meta_value = '".$genonameid."') ;
		", OBJECT);
		if(count($wplocs) == 0)
		{
			$wplocdata = array(
				'post_title' => $name,
				'post_status' => 'publish', 
				'post_type' => 'soe_city',
			);
			$wplocid =  wp_insert_post($wplocdata);
			if($wplocid > 0)
			{
				add_post_meta($wplocid, 'location', $genonameid, true);
			}
		}
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
				// Create WP cities if it does not exist yet
				$this->ensureCity($custom['location'][0], $loc->name);
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
	
	
	public function getMedia($label,$desc,$type,$form_name)
	{
		$custom = get_post_custom($post->ID);
// 		print_r($custom);
		global $wpdb;
		$wpmedias = $wpdb->get_results("
		SELECT * FROM wp_posts AS p 
		WHERE p.post_type = 'attachment';
		", OBJECT);
		
		$selectStyle= 'cursor:pointer;border:1px solid grey';
		$selectedMediaName = 'select_media_'.$form_name;
		$selectedMediaStyleName = 'selected_media_'.$form_name;
		$selectedMediaStyle = 'class="'.$selectedMediaStyleName.'"';
		$selected = null;
		$mediaForm = '
		<style>
		.'.$selectedMediaStyleName.'{
			border:2px dashed black;
		}
		</style>
		<h3>'.$label.'</h3>'.$desc.'<div height="400px" width="70%" style="overflow:auto">';
		foreach($wpmedias as $m)
		{
			$mm = wp_get_attachment_metadata($m->ID);
			// 			$mc = get_post_custom($m->ID);
			$mimeArray = explode('/', $m->post_mime_type);
			$mime = $mimeArray[0];
			$mime2 = $mimeArray[1];
			// 			echo 'MIME:'.$mime.';TYPE:'.$type;
			if($mime == $type)
			{
// 				echo $custom[$form_name][0] .' == ' . $m->ID;
				if($custom[$form_name][0] == $m->ID)
					$selected =  $m->ID;
				if('image' == $type)
				{
					$sized = image_downsize( $m->ID, 'thumbnail' );
					
					$mediaForm .= '
					<div '.($custom[$form_name][0] == $m->ID ? $selectedMediaStyle : "").'>
					<img src="'.$sized[0].'" width="'.$sized[1].'" height="'.$sized[2].'"/> '.$m->post_title.'
					<span class="'.$selectedMediaName.'" id="selectmedia-'.$m->ID.'" style="'.$selectStyle.'">Select</span>
					</div>';
				}
				elseif('audio' == $type)
				{
					$mediaForm .= '<div '.($custom[$form_name][0] == $m->ID ? $selectedMediaStyle : "").'>
					'.$m->post_title.'
					<span class="'.$selectedMediaName.'" id="selectmedia-'.$m->ID.'" style="'.$selectStyle.'">Select</span>
					</div>';
				}
			}
			elseif($mime2 == $type) // PDFs
			{
				$mediaForm .= '<div '.($custom[$form_name][0] == $m->ID ? $selectedMediaStyle : "").'>
				'.$m->post_title.'
				<span class="'.$selectedMediaName.'" id="selectmedia-'.$m->ID.'" style="'.$selectStyle.'">Select</span>
				</div>';
			}
		}
		echo $mediaForm.'
		<input type="hidden" id="'.$form_name.'" name="'.$form_name.'" value="'.$selected.'"/>
		</div>';
				
				
				echo "<script type=\"text/javascript\">
				jQuery(document).ready(function() 
					{
						jQuery('.".$selectedMediaName."').click(function() 
						{
							jQuery('.".$selectedMediaStyleName."').removeClass('".$selectedMediaStyleName."');
							var that = jQuery(this);
							that.parent().addClass('".$selectedMediaStyleName."');
							var s = that.attr('id').split('-').pop();
							jQuery('#".$form_name."').val(s);
							// jQuery('#publish').click();
							return false;
						});
					});
				
				</script>
				";
		
	}
	
}


?>