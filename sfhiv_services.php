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
	if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_service' ) return;
	$query->set( 'orderby', 'title' );
	$query->set( 'order', 'ASC' );
}

?>