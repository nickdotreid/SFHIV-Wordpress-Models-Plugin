<?php

add_action('init','sfhiv_add_events_type');
function sfhiv_add_events_type(){
	register_post_type( 'sfhiv_event',
		array(
			'labels' => array(
				'name' => __( 'Meetings' ),
				'singular_name' => __( 'Meeting' ),
				'add_new' => __('Add New','meeting'),
				'add_new_item' => __('Add New Meeting'),
				'edit_item' => __('Edit Meeting'),
				'new_item' => __('New Meeting'),
				'all_items' => __('All Meetings'),
				'view_item' => __('View Meeting'),
				'search_items' => __('Search Meetings'),
				'not_found' => __('No meetings found'),
				'not_found_in_trash' => __('No meetings found in Trash'),
				'menu_name' => 'Meetings',
			),
		'public' => true,
		'has_archive' => true,
		'hierarchical' => true,
		'rewrite' => array(
			'slug' => 'events',
			'feeds' => false,
		),
		'capability_type' => 'page',
		'supports' => array('editor','excerpt'),
		'taxonomies' => array(
			'sfhiv_service_category',
			'sfhiv_group_category',
			'sfhiv_event_category',
			),
		'register_meta_box_cb' => 'sfhiv_add_events_meta_boxes',
		)
	);
}

add_filter( 'cmb_meta_boxes', 'sfhiv_event_add_time_duration_fields', 20 );
function sfhiv_event_add_time_duration_fields( $metaboxes ){
	$meta_boxes[] = array(
		'id'         => 'sfhiv_event_metabox',
		'title'      => 'Event Time and Duration',
		'pages'      => array( 'sfhiv_event', ),
		'context'    => 'side',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields' => array(
			array(
				'name' => 'Start Time',
				'id'   => 'sfhiv_event_start',
				'type' => 'text_datetime_timestamp',
			),
			array(
				'name' => 'End Time',
				'id'   => 'sfhiv_event_end',
				'type' => 'text_datetime_timestamp',
			),
		)
	);
	return $meta_boxes;
}

add_action('init','sfhiv_add_event_category');
function sfhiv_add_event_category(){
	$labels = array(
    'name' => _x( 'Event Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Event Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Event Categories' ),
    'all_items' => __( 'All Event Categories' ),
    'parent_item' => __( 'Parent Event Category' ),
    'parent_item_colon' => __( 'Parent Event Category:' ),
    'edit_item' => __( 'Edit Event Category' ),
    'update_item' => __( 'Update Event Category' ),
    'add_new_item' => __( 'Add New Event Category' ),
    'new_item_name' => __( 'New Group Event Name' ),
  ); 	

  register_taxonomy('sfhiv_event_category',array('sfhiv_event'),array(
    'hierarchical' => true,
    'labels' => $labels,
  ));
}

add_action('parse_query','sfhiv_event_query_set_vars');
function sfhiv_event_query_set_vars($query){
	if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_event' ) return;
	if(isset($query->query_vars['sfhiv_event_selection'])) return;
	$query->set("sfhiv_event_selection","future");
	if(isset($_GET['sfhiv_event_time'])){
		switch($_GET['sfhiv_event_time']){
			case "future":
				$query->set("sfhiv_event_selection","future");
				break;
			case "past":
				$query->set("sfhiv_event_selection","past");
			break;
			default:
				$query->set("sfhiv_event_selection","all");
		}
	}
}

add_action( 'pre_get_posts', 'sfhiv_event_order_query', 5 );
function sfhiv_event_order_query( $query ) {
	if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_event' ) return;
    $query->set( 'meta_key', 'sfhiv_event_start' );
	$query->set( 'orderby', 'meta_value_num' );
	switch($query->query_vars['sfhiv_event_selection']){
		case "future":
			$query->set( 'order', 'ASC' );
			break;
		default:
			$query->set( 'order', 'DESC' );
			break;
	}
}

add_action( 'pre_get_posts', 'sfhiv_event_query_update', 7 );
function sfhiv_event_query_update($query){
	if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_event' ) return;
	
	remove_action( 'pre_get_posts', 'sfhiv_event_query_update', 7 );
	remove_action('parse_query','sfhiv_event_query_set_vars');
	
	switch($query->query_vars['sfhiv_event_selection']){
		case "future":
			$query->set( 'meta_query', array(
				"relation" => "AND",
				array(
		        'key' => 'sfhiv_event_start',
		        'value' => time(),
		        'compare' => '>'
		    ) ));
			if(sfhiv_event_test_query($query)){
				$query->set("sfhiv_event_selection","future");
				break;
			}
		case "past":
			$query->set( 'meta_query', array(
				"relation" => "AND",
				array(
		        'key' => 'sfhiv_event_start',
		        'value' => time(),
		        'compare' => '<'
		    ) ));
			if(sfhiv_event_test_query($query)){
				$query->set("sfhiv_event_selection","past");
				break;
			}
			
		default:
			$query->set( 'meta_query', array() );
	}
	sfhiv_event_order_query( &$query );
	add_action( 'pre_get_posts', 'sfhiv_event_query_update', 7 );
	add_action('parse_query','sfhiv_event_query_set_vars');
}

function sfhiv_event_test_query($query){
	$test_query = new WP_Query($query->query_vars);
	if($test_query->post_count>0){
		return true;
	}
	return false;
}

?>