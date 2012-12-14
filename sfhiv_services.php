<?php

add_action('init','sfhiv_add_services_type');
function sfhiv_add_services_type(){
	register_post_type( 'sfhiv_service',
		array(
			'labels' => array(
				'name' => __( 'Services' ),
				'singular_name' => __( 'Service' ),
				'add_new' => __('Add New','service'),
				'add_new_item' => __('Add New Service'),
				'edit_item' => __('Edit Service'),
				'new_item' => __('New Service'),
				'all_items' => __('All Services'),
				'view_item' => __('View Service'),
				'search_items' => __('Search Services'),
				'not_found' => __('No services found'),
				'not_found_in_trash' => __('No services found in Trash'),
				'menu_name' => 'Services',
			),
		'public' => true,
		'exclude_from_search' => true,
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
}

add_action('init','sfhiv_add_service_provider_type');
function sfhiv_add_service_provider_type(){
	register_post_type( 'sfhiv_provider',
		array(
			'labels' => array(
				'name' => __( 'Service Providers' ),
				'singular_name' => __( 'Service Provider' ),
				'add_new' => __('Add New','Service Provider'),
				'add_new_item' => __('Add New Service Provider'),
				'edit_item' => __('Edit Service Provider'),
				'new_item' => __('New Service Provider'),
				'all_items' => __('All Service Providers'),
				'view_item' => __('View Service Provider'),
				'search_items' => __('Search Service Providers'),
				'not_found' => __('No service providers found'),
				'not_found_in_trash' => __('No service providers found in Trash'),
				'menu_name' => 'Service Providers',
			),
			'supports' => array('title','thumbnail'),
			'public' => false,
			'show_ui' => true,
			'show_in_menu' => 'edit.php?post_type=sfhiv_service',
			'menu_position' => 100,
			'taxonomies' => array(),
		)
	);
}

add_action( 'wp_loaded', 'sfhiv_service_to_provider_connection' );
function sfhiv_service_to_provider_connection() {
	p2p_register_connection_type( array(
		'name' => 'provider_services',
		'from' => 'sfhiv_provider',
		'to' => 'sfhiv_service',
		'title' => array( 'from' => __( 'Services from Provider', 'sfhiv' ), 'to' => __( 'Provider for Service', 'sfhiv' ) ),
		'admin_column' => 'to',
	));
}

add_filter( 'the_posts', 'sfhiv_service_load_providers', 10, 2);
function sfhiv_service_load_providers($posts,$query){
	if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_service' ) return $posts;
	p2p_type( 'provider_services' )->each_connected( $query, array(), 'providers' );
	return $posts;
}

add_filter( 'the_posts', 'sfhiv_service_provider_load_services', 10, 2);
function sfhiv_service_provider_load_services($posts,$query){
	if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_provider' ) return $posts;
	p2p_type( 'provider_services' )->each_connected( $query, array(), 'services' );
	return $posts;
}

include_once("sfhiv_service_hours.php");
include_once("sfhiv_services_time_taxonomy.php");
include_once("sfhiv_services_day_taxonomy.php");

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

  register_taxonomy('sfhiv_service_category',array('sfhiv_service','sfhiv_service_hour'),array(
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
	'sfhiv_service','sfhiv_service_hour'
	),
	array(
    'hierarchical' => true,
    'labels' => $labels,
  ));
}

add_action( 'pre_get_posts', 'sfhiv_service_order_query', 5 );
function sfhiv_service_order_query( $query ) {
	if ( is_admin() || !isset($query->query_vars['post_type']) || $query->query_vars['post_type'] != 'sfhiv_service' ) return;
	$query->set( 'orderby', 'title' );
	$query->set( 'order', 'ASC' );
}

?>