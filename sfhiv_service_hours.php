<?php

add_action('init','sfhiv_add_service_hours_type');
function sfhiv_add_service_hours_type(){
	register_post_type( 'sfhiv_service_hour',
		array(
			'labels' => array(
				'name' => __( 'Service Times' ),
				'singular_name' => __( 'Service Time' )
			),
		'public' => false,
		'hierarchical' => false,
		'taxonomies' => array('sfhiv_service_category','sfhiv_population_category'),
		'supports' => array('title','editor','excerpt'),
		'register_meta_box_cb' => 'sfhiv_add_service_hours_meta_boxes',
		)
	);
	p2p_register_connection_type( array(
		'name' => 'service_time',
		'from' => 'sfhiv_service',
		'to' => 'sfhiv_service_hour',
		'admin_box' => false,
		'admin_column' => 'to',
	) );
}

add_filter( 'the_posts', 'sfhiv_service_load_service_hours', 10, 2);
function sfhiv_service_load_service_hours($posts,$query){
	if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_service' ) return $posts;
	p2p_type( 'service_time' )->each_connected( $query, array('post_type'=>'sfhiv_service_hour'), 'times' );
	return $posts;
}

add_filter( 'the_posts', 'sfhiv_service_hours_load_related_locations', 10, 2);
function sfhiv_service_hours_load_related_locations($posts, $query){
	if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_service_hour' ) return $posts;
	p2p_type( 'related_location' )->each_connected( $query, array(), 'locations' );
	return $posts;
}

add_filter( 'the_posts', 'sfhiv_service_hours_load_days', 10, 2);
function sfhiv_service_hours_load_days($posts, $query){
	if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_service_hour' ) return $posts;
	foreach($posts as $post){
		$days = wp_get_object_terms( $post->ID, "sfhiv_day_of_week_taxonomy", array(
			"fields" => "slugs",
		));
		$post->days = $days;
	}
	return $posts;
}

function sfhiv_service_hour_get_service($service_hour){
	$services = new WP_Query( array(
		'post_type' => 'sfhiv_service',
		'connected_type' => 'service_time',
		'connected_items' => $service_hour->ID,
	));
	if($services->post_count < 1) return false;
	return $services->posts[0];
}

function sfhiv_service_hours_sort_by_location($times){
	$locations = array();
	foreach($times as $time){
		foreach($time->locations as $tiloc){
			$found = false;
			foreach($locations as $loc){
				if($tiloc->ID == $loc->ID){
					$loc->times[] = $time;
					$found = true;
				}
			}
			if(!$found){
				$tiloc->times = array($time);
				$locations[] = $tiloc;
			}
		}
		$time->locations = array();
	}
	return $locations;
}

function sfhiv_service_hours_sort_by_start_end($hours){
	$times = array();
	foreach($hours as $time){
		$found = false;
		foreach($times as $t){
			if($t['start'] == $time->start && $t['end'] == $time->end){
				$found = true;
				$t['times'][] = $time;
			}
		}
		if(!$found){
			$t = array(
				"start" => $time->start,
				"end" => $time->end,
				"times" => array($time),
			);
			$times[] = $t;
		}
	}
	return $times;
}

function sfhiv_service_hours_sort_by_day($hours){
	$days = array();
	foreach($hours as $time){
		foreach($time->days as $day){
			$found = false;
			foreach($days as $day_obj){
				if($day_obj['day'] == $day){
					$day_obj['times'][] = $time;
					$found = true;
				}
			}
			if(!$found){
				$d = array(
					"day" => $day,
					"times" => array($time),
				);
				$days[] = $d;
			}
		}
	}
	// sort each day by taxonomy day order
	// sort each days hours by start time
	return $days;
}

add_filter( 'the_posts', 'sfhiv_service_hours_load_time', 10, 2);
function sfhiv_service_hours_load_time($posts, $query){
	if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_service_hour' ) return $posts;
	foreach($posts as $post){
		$post->start = sfhiv_service_get_start_time($post);
		$post->end = sfhiv_service_get_end_time($post);
	}
	return $posts;
}

add_action( 'pre_get_posts', 'sfhiv_service_hour_sort_order', 5 );
function sfhiv_service_hour_sort_order( $query ) {
	if ( is_admin() || !isset($query->query_vars['post_type']) || $query->query_vars['post_type'] != 'sfhiv_service_hour' ) return;
	$query->query_vars['orderby'] = 'menu_order title date';
	$query->query_vars['order'] = 'ASC';
}

function sfhiv_add_service_hours_meta_boxes(){
	add_meta_box( 'service_hours_time', 'Service Time', 'sfhiv_services_hours_op_meta', 'sfhiv_service_hour' );
	sfhiv_location_add_choose_location_meta_box('sfhiv_service_hour');
}

function sfhiv_services_hours_op_meta($post){
	sfhiv_draw_services_hours_op_meta($post,'hours');
	sfhiv_draw_service_hours_time_meta($post,'hours');
}

function sfhiv_draw_services_hours_op_meta($days=array(),$form_name){	
	$day_terms = get_terms( "sfhiv_day_of_week_taxonomy", array(
		"hide_empty" => false,
		));	
	include 'templates/service_hours.php';
}

function sfhiv_draw_service_hours_time_meta($start,$end,$form_name){
	$start = date('g:i a',$start);	
	$end = date('g:i a',$end);
	include 'templates/service_hours_time.php';
}

function sfhiv_service_hours_string_to_time($string){
	return strtotime($string)-strtotime('12AM');
}

function sfhiv_add_services_meta_boxes(){
	wp_enqueue_script('sfhiv_service_hour_js', plugins_url('assets/js/admin-service_hour.js',__FILE__),array('jquery'));
	wp_enqueue_style('sfhiv_service_hour_css', plugins_url('assets/css/admin-service_hour.css',__FILE__));
	wp_enqueue_script('sfhiv_location_js', plugins_url('assets/js/admin-location.js',__FILE__),array('jquery'));
	wp_enqueue_style('sfhiv_location_css', plugins_url('assets/css/admin-location.css',__FILE__));
	add_meta_box( 'service_time', 'Time', 'sfhiv_service_time_box', 'sfhiv_service' );
}

function sfhiv_service_time_box($post){
	$service_hours = new WP_Query( array(
		'connected_type' => 'service_time',
		'connected_items' => $post->ID,
	));
	$collected_hours = array();
	foreach($service_hours->posts as $hour){
		$match = false;
		$start = sfhiv_service_get_start_time($hour);
		$end = sfhiv_service_get_end_time($hour);
		$location = sfhiv_service_get_location_id($hour);
		foreach($collected_hours as $index => $time){
			if($time['start'] == $start && $time['end'] == $end && $location == $time['location']){
				$match = true;
				$collected_hours[$index]['days'] = array_merge($collected_hours[$index]['days'],sfhiv_service_get_service_days($hour));
			}
		}
		if(!$match){
			array_push($collected_hours,array(
				'days' => sfhiv_service_get_service_days($hour),
				'start' => $start,
				'end' => $end,
				'location' => $location,
			));
		}
	}
	foreach($collected_hours as $time){
		sfhiv_service_draw_service_hour_form($time);
	}
	echo '<a href="#" id="new_sfhiv_service_hour" class="button" >New Time</a>';
}

function sfhiv_service_get_start_time($post){
	return get_post_meta($post->ID, 'sfhiv_service_start',true);
}

function sfhiv_service_get_end_time($post){
	return get_post_meta($post->ID, 'sfhiv_service_end',true);
}

function sfhiv_service_get_service_days($post){
	return wp_get_object_terms( $post->ID, "sfhiv_day_of_week_taxonomy", array(
		"fields" => "slugs",
	));
}

function sfhiv_service_hour_get_parent($post){
	$parents = new WP_Query( array(
		'connected_type' => 'service_time',
		'connected_items' => $post->ID,
	));
	if($parents->post_count > 0){
		return $parents->posts[0];
	}
	return false;
}

function sfhiv_service_get_location_id($post){
	$location = sfhiv_location_get_related_location($post->ID);
	if($location){
		return $location->ID;
	}
	return 0;
}

function sfhiv_service_draw_service_hour_form($data=array()){
	echo '<div class="service_hour">';
	sfhiv_draw_services_hours_op_meta($data['days'],'hours[position]');
	sfhiv_draw_service_hours_time_meta($data['start'], $data['end'],'hours[position]');
	sfhiv_location_location_list_draw(array($data['location']),array(
		'field_name' => 'hours[position][sfhiv_location]',
		));
	echo '</div>';
}

add_action('wp_ajax_sfhiv_service_hour_form', 'sfhiv_service_hour_ajax_hour_form');
function sfhiv_service_hour_ajax_hour_form() {
	sfhiv_service_draw_service_hour_form();
	die();
}

add_action( 'save_post', 'sfhiv_service_hour_time_save', 200 );
function sfhiv_service_hour_time_save($post_ID,$post){
	if(get_post_type($post_ID) != 'sfhiv_service') return;
	$service_hours = new WP_Query( array(
		'connected_type' => 'service_time',
		'connected_items' => $post_ID,
	));
	foreach($service_hours->posts as $hour){
		wp_delete_post( $hour->ID, true );
	}
	foreach($_POST['hours'] as $key=>$post_data){
		if(is_array($post_data['days'])){
			foreach($post_data['days'] as $day){
				sfhiv_create_or_update_service_hours(false,array_merge($post_data,array(
					"day_of_week" => $day,
				)),$post_ID);
			}
		}
	}
	sfhiv_service_hours_set_order();
}

function sfhiv_create_or_update_service_hours($post_ID=false,$post_data,$parent_ID=false){
	if(!$post_ID){
		if( isset($post_data['day_of_week'])
			&& (isset($post_data['start']) && $post_data['start']!='')
			&& (isset($post_data['end']) && $post_data['end']!='')){
			$post_ID = wp_insert_post(array(
				'post_type' => 'sfhiv_service_hour',
				'post_title' => "will",
				'post_content' => "get",
				'post_excerpt' => "overwritten",
			),true);
		}
	}
	if(!$post_ID) return false;
	if($parent_ID){
		$parents = new WP_Query( array(
			'connected_type' => 'service_time',
			'connected_items' => $post_ID,
		));
		$found = false;
		foreach($parents as $parent){
			if($parent->ID == $parent_ID) $found = true;
		}
		if(!$found){
			p2p_create_connection( 'service_time', array(
				'from' => $parent_ID,
				'to' => $post_ID,
			));
		}
		$tax_terms = array('sfhiv_service_category','sfhiv_population_category');
		foreach($tax_terms as $tax){
			$parent_terms = wp_get_object_terms($parent_ID,$tax,array('fields'=>'slugs'));
			wp_set_object_terms($post_ID,$parent_terms,$tax);
		}
		$parent = get_post($parent_ID);
		$parent_status = get_post_status($parent_ID);
		wp_update_post(array(
			'ID' => $post_ID,
			'post_status' => $parent_status,
			'post_title' => $parent->post_title,
			'post_content' => $parent->post_content,
			'post_excerpt' => $parent->post_excerpt,
		));
		$parent_custom = get_post_custom($parent_ID);
		foreach($parent_custom as $custom_key => $custom_value){
			delete_post_meta($post_ID,$custom_key);
			foreach($custom_value as $meta_value){
				add_post_meta($post_ID,$custom_key,$meta_value);
			}
		}
	}
	// update time
	sfhiv_service_time_of_day_save_post_update($post_ID);
	if(isset($post_data['start'])){
		$seconds = sfhiv_service_hours_string_to_time($post_data['start']);
		update_post_meta($post_ID, 'sfhiv_service_start', $seconds);
	}
	if(isset($post_data['end'])){
		$seconds = sfhiv_service_hours_string_to_time($post_data['end']);
		update_post_meta($post_ID, 'sfhiv_service_end', $seconds);
	}
	if(isset($post_data['sfhiv_location'])){
		sfhiv_location_relation_save($post_ID,$post_data['sfhiv_location']);
	}
	if(isset($post_data['day_of_week'])){
		 wp_set_object_terms( $post_ID, $post_data['day_of_week'], 'sfhiv_day_of_week_taxonomy', false );
		if($parent_ID){
			wp_set_object_terms( $parent_ID, $post_data['day_of_week'], 'sfhiv_day_of_week_taxonomy', true );
		}
	}
}

function sfhiv_service_hours_set_order(){
	$query = new WP_Query(array(
		'post_type' => 'sfhiv_service_hour',
		'nopaging' => true,
	));
	usort($query->posts,function($a,$b){
		$day_terms = get_terms( "sfhiv_day_of_week_taxonomy", array(
			"hide_empty" => false,
			));
		foreach($day_terms as $term){
			if(has_term($term,'sfhiv_day_of_week_taxonomy',$a) && has_term($term,'sfhiv_day_of_week_taxonomy',$b)){
				if(sfhiv_service_get_start_time($a) > sfhiv_service_get_start_time($b)){
					return 1;
				}else{
					return -1;
				}
			}
			if(has_term($term,'sfhiv_day_of_week_taxonomy',$a)){
				return -1;
			}
			if(has_term($term,'sfhiv_day_of_week_taxonomy',$b)){
				return 1;
			}
		}
		return 0;
	});
	foreach($query->posts as $index => $post){
		wp_update_post(array(
			'ID' => $post->ID,
			'menu_order' => $index,
		));
	}
}

?>