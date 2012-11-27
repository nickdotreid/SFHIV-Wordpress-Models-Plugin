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

add_filter( 'cmb_meta_boxes', 'sfhiv_event_add_unique_page_metabox', 21 );
function sfhiv_event_add_unique_page_metabox( $meta_boxes ){
	$meta_boxes[] = array(
		'id'         => 'sfhiv_event_unique_page',
		'title'      => 'Unique Page',
		'pages'      => array( 'sfhiv_event', ),
		'context'    => 'side',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields' => array(
			array(
				'name' => 'Unique page',
				'desc' => 'Create unique page for meeting',
				'id'   => 'sfhiv_event_unique_page_checkbox',
				'type' => 'checkbox',
			),
		)
	);
	return $meta_boxes;
}

add_action( 'the_posts', 'sfhiv_event_get_groups', 7, 2 );
function sfhiv_event_get_groups($posts,$query){
	if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_event' ) return $posts;
	p2p_type( 'group_events' )->each_connected( $query, array('post_type'=>'sfhiv_group'), 'groups' );
	return $query->posts;
}

add_filter('post_type_link','sfhiv_event_link_filter',2,2);
function sfhiv_event_link_filter($link,$post){
	if(is_admin() || get_post_type($post->ID) != 'sfhiv_event') return $link;
	$unique = get_post_meta($post->ID,'sfhiv_event_unique_page_checkbox',true);
	if(!$unique){
		$groups = new WP_Query( array(
			'connected_type' => 'group_events',
			'connected_items' => $post->ID,
			'nopaging' => true,
			'post_type' => 'sfhiv_group',
		));
		if(count($groups->posts) > 0){
			$group = $groups->posts[0];
			$link = get_permalink($group->ID);
			return $link."#post-".$post->ID;
		};
	}
	return $link;
}

add_filter( 'cmb_meta_boxes', 'sfhiv_event_add_time_duration_fields', 20 );
function sfhiv_event_add_time_duration_fields( $meta_boxes ){
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
				'name' => 'Duration',
				'desc' => 'Enter the number of hours the event lasts. Enter amounts smaller than an hour with a decimal.',
				'id'   => 'sfhiv_event_duration',
				'type' => 'text_small',
			),
			array(
				'name'    => 'Date Status',
				'desc'    => 'How confirmed is this date',
				'id'      => 'sfhiv_event_date_status',
				'type'    => 'radio',
				'options' => array(
					array( 'name' => 'Confirmed', 'value' => 'confirmed', ),
					array( 'name' => 'Day Announced', 'value' => 'day', ),
					array( 'name' => 'Month Announced', 'value' => 'month', ),
					array( 'name' => 'To Be Announced', 'value' => 'tba', ),
				),
			),
		)
	);
	return $meta_boxes;
}
function sfhiv_event_get_start_time($post_ID){
	return get_post_meta($post_ID,'sfhiv_event_start',true);
}

function sfhiv_event_get_end_time($post_ID){
	$duration = get_post_meta($post_ID,'sfhiv_event_duration',true);
	if($duration){
		$seconds = $duration * 60 * 60;
		$time = get_post_meta($post_ID,'sfhiv_event_start',true);
		return $time + $seconds;
	}
	# This is depreciated for existing data
	return get_post_meta($post_ID,'sfhiv_event_end',true);
}

function sfhiv_event_get_duration($post_ID){
	$duration = get_post_meta($post_ID,'sfhiv_event_duration',true);
	if($duration){
		return $duration;
	}
	$start = get_post_meta($post_ID,'sfhiv_event_start',true);
	$end = get_post_meta($post_ID,'sfhiv_event_end',true);
	return $end - $start;
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

function sfhiv_event_query_is_upcoming($query){
	if($query->query_vars['tax_query']){
		foreach($query->query_vars['tax_query'] as $num => $que){
			if($num != 'relation'){
				if(sfhiv_event_tax_query_is_upcoming($que)){
					return true;
				}				
			}
		}
	}
	return false;
}

function sfhiv_event_tax_query_remove_upcoming($tax_query){
	foreach($tax_query as $que){
		if(!sfhiv_event_tax_query_is_upcoming($que)){
			$new_tax_query[] = $que;
		}
	}
	$new_tax_query = array( "relation" => $tax_query['relation'] );
	return $new_tax_query;
}

function sfhiv_event_tax_query_is_upcoming($que){
	if($que['taxonomy'] == 'sfhiv_event_category'){
		if($que['field'] == 'slug' && $que['terms'] == 'upcoming'){
			return true;
		}
	}
	return false;
}

add_action('parse_query','sfhiv_event_query_set_vars');
function sfhiv_event_query_set_vars($query){
	if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_event' ) return;
	if(isset($query->query_vars['sfhiv_event_selection'])) return;
	if(sfhiv_event_query_is_upcoming($query)){
		$query->set("sfhiv_event_selection","future");
		$query->set('tax_query',sfhiv_event_tax_query_remove_upcoming($query->query_vars['tax_query']));
	}
}

add_action( 'pre_get_posts', 'sfhiv_event_order_query', 5 );
function sfhiv_event_order_query( $query ) {
	if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_event' ) return;
    $query->set( 'meta_key', 'sfhiv_event_start' );
	$query->set( 'orderby', 'meta_value_num' );
	$query->set( 'order', 'ASC' );
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
			break;			
		default:
			$query->set( 'meta_query', array() );
	}
	sfhiv_event_order_query( &$query );
	add_action( 'pre_get_posts', 'sfhiv_event_query_update', 7 );
	add_action('parse_query','sfhiv_event_query_set_vars');
}

?>