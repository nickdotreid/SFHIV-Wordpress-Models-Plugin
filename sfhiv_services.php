<?php

add_action('init','sfhiv_add_services_type');
function sfhiv_add_services_type(){
	register_post_type( 'sfhiv_service',
		array(
			'labels' => array(
				'name' => __( 'Services' ),
				'singular_name' => __( 'Service' )
			),
		'public' => true,
		'has_archive' => true,
		'rewrite' => array(
			'slug' => 'services',
			'feeds' => false,
		),
		'hierarchical' => true,
		'taxonomies' => array(),
		'register_meta_box_cb' => 'sfhiv_add_services_meta_boxes',
		)
	);
	add_post_type_support( 'sfhiv_service', 'excerpt' );
}

add_action('init','sfhiv_add_service_category');
function sfhiv_add_service_category(){
	$labels = array(
    'name' => _x( 'Service Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Service Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Service Categories' ),
    'all_items' => __( 'All Service Categories' ),
    'parent_item' => __( 'Parent Service Category' ),
    'parent_item_colon' => __( 'Parent Service Category:' ),
    'edit_item' => __( 'Edit Service Category' ),
    'update_item' => __( 'Update Service Category' ),
    'add_new_item' => __( 'Add New Service Category' ),
    'new_item_name' => __( 'New Group Service Name' ),
  ); 	

  register_taxonomy('sfhiv_service_category',array('sfhiv_service'),array(
    'hierarchical' => true,
    'labels' => $labels,
  ));
}

add_action('init','sfhiv_add_population_tag');
function sfhiv_add_population_tag(){
	$labels = array(
    'name' => _x( 'Population Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Population Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Population Categories' ),
    'all_items' => __( 'All Population Categories' ),
    'parent_item' => __( 'Parent Population Category' ),
    'parent_item_colon' => __( 'Parent Population Category:' ),
    'edit_item' => __( 'Edit Population Category' ),
    'update_item' => __( 'Update Population Category' ),
    'add_new_item' => __( 'Add New Population Category' ),
    'new_item_name' => __( 'New Group Population Name' ),
  ); 	

  register_taxonomy('sfhiv_population_category',array(
	'sfhiv_service'
	),
	array(
    'hierarchical' => true,
    'labels' => $labels,
  ));
}

function sfhiv_add_services_meta_boxes(){
	add_meta_box( 'service_time', 'Time', 'sfhiv_service_time_box', 'sfhiv_service' );
}

function sfhiv_service_time_box($post){
	$service_hours = new WP_Query( array(
		'connected_type' => 'service_time',
		'connected_items' => $post->ID,
	));
	foreach($service_hours->posts as $hour){
		sfhiv_draw_services_hours_op_meta($hour,'hours['.$hour->ID.']');
		echo sfhiv_delete_service_hour_link($hour);
	}
	?><h4>Add new time and location</h4><?
	sfhiv_draw_services_hours_op_meta($post,'hours[new]');
}

function sfhiv_delete_service_hour_link($post,$link = 'Delete This', $before = '', $after = '', $title="Move this item to the Trash") {
    if ( $post->post_type == 'page' ) {
        if ( !current_user_can( 'edit_page' ) )
            return;
    } else {
        if ( !current_user_can( 'edit_post' ) )
            return;
    }
	$href = "/wp-admin/post.php?action=trash&post=" . $post->ID;
    $delLink = wp_nonce_url( site_url() . $href, 'trash-' . $post->post_type . '_' . $post->ID);
    $link = '<a href="' . $delLink . '" onclick="javascript:if(!confirm(\'Are you sure you want to move this item to trash?\')) return false;" title="'.$title.'" />'.$link."</a>";
    return $before . $link . $after;
}

add_action( 'save_post', 'sfhiv_service_hour_time_save' );
function sfhiv_service_hour_time_save($post_ID,$post){
	if(get_post_type($post_ID) != 'sfhiv_service') return;
	foreach($_POST['hours'] as $key=>$post_data){
		if($key == 'new'){
			sfhiv_create_or_update_service_hours(false,$post_data,$post_ID);
		}else{
			sfhiv_create_or_update_service_hours($key,$post_data,$post_ID);
		}
	}
}

function sfhiv_create_or_update_service_hours($post_ID=false,$post_data,$parent_ID=false){
	if(!$post_ID){
		if((isset($post_data['day_of_week']) && is_array($post_data['day_of_week']) && count($post_data['day_of_week'])>0 )
			&& (isset($post_data['start']) && $post_data['start']!='')
			&& (isset($post_data['end']) && $post_data['end']!='')){
			$post_ID = wp_insert_post(array(
				'post_type' => 'sfhiv_service_hour',
			));
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
		$parent_status = get_post_status($parent_ID);
		wp_update_post(array(
			'ID' => $post_ID,
			'post_status' => $parent_status,
		));
	}
	// check and save post meta
	if(isset($post_data['day_of_week'])){
		delete_post_meta($post_ID,'sfhiv_service_days');
		foreach($post_data['day_of_week'] as $day){
			add_post_meta($post_ID, 'sfhiv_service_days', $day);	
		}
	}
	if(isset($post_data['start'])){
		$seconds = sfhiv_service_hours_string_to_time($post_data['start']);
		update_post_meta($post_ID, 'sfhiv_service_start', $seconds);
	}
	if(isset($post_data['end'])){
		$seconds = sfhiv_service_hours_string_to_time($post_data['end']);
		update_post_meta($post_ID, 'sfhiv_service_end', $seconds);
	}
}


add_action('init','sfhiv_add_service_hours_type');
function sfhiv_add_service_hours_type(){
	register_post_type( 'sfhiv_service_hour',
		array(
			'labels' => array(
				'name' => __( 'Service Times' ),
				'singular_name' => __( 'Service Time' )
			),
		'public' => true,
		'has_archive' => false,
		'show_in_menu' => 'edit.php?post_type=sfhiv_service',
		'rewrite' => array(
			'slug' => 'services',
			'feeds' => false,
		),
		'hierarchical' => false,
		'taxonomies' => array('sfhiv_service_category','sfhiv_population_category'),
		'supports' => array('title'),
		'register_meta_box_cb' => 'sfhiv_add_service_hours_meta_boxes',
		)
	);
	p2p_register_connection_type( array(
		'name' => 'service_time',
		'from' => 'sfhiv_service',
		'to' => 'sfhiv_service_hour',
	) );
}

function sfhiv_add_service_hours_meta_boxes(){
	add_meta_box( 'service_hours_time', 'Service Time', 'sfhiv_services_hours_op_meta', 'sfhiv_service_hour' );
}

function sfhiv_services_hours_op_meta($post){
	sfhiv_draw_services_hours_op_meta($post,'hours');
}

function sfhiv_draw_services_hours_op_meta($post,$form_name){
	$days_in_week = array(
		'Monday',
		'Tuesday',
		'Wednesday',
		'Thursday',
		'Friday',
		'Saturday',
		'Sunday',
	);
	$hours = array();
	$num = 0;
	while($num < 12){
		$num ++;
		array_push($hours,$num);
	}
	$minutes = array();
	$num = 0;
	while($num < 60){
		array_push($minutes,$num);
		$num += 15;
	}
	$ampm = array('AM','PM');
	
	$days = get_post_meta($post->ID, 'sfhiv_service_days');
	
	$start = get_post_meta($post->ID, 'sfhiv_service_start',true);
	$start = date('g:i a',$start);
	
	$end = get_post_meta($post->ID, 'sfhiv_service_end',true);
	$end = date('g:i a',$end);
	
	include 'templates/service_hours.php';
}

add_action( 'save_post', 'sfhiv_service_hours_save' );
function sfhiv_service_hours_save($post_ID,$post){
	sfhiv_create_or_update_service_hours($post_ID,$_POST['hours']);
}

function sfhiv_service_hours_string_to_time($string){
	return strtotime($string)-strtotime('12AM');
}

function sfhiv_service_hour_order_query( $query ) {
    if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_service_hour' ) return;
    $query->set( 'orderby', 'meta_value_num' );
    $query->set( 'meta_key', 'sfhiv_service_start' );
    $query->set( 'order', 'ASC' );
}
add_action( 'pre_get_posts', 'sfhiv_service_hour_order_query' );

?>