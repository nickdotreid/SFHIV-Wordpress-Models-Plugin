<?php

add_action('init','sfhiv_add_reports_type');
function sfhiv_add_reports_type(){
	register_post_type( 'sfhiv_report',
		array(
			'labels' => array(
				'name' => __( 'Reports' ),
				'singular_name' => __( 'Report' )
			),
		'public' => true,
		'has_archive' => true,
		'hierarchical' => true,
		'rewrite' => array(
			'slug' => 'reports',
			'feeds' => true,
		),
		'capability_type' => 'page',
		'supports' => array('title','author','editor','excerpt','thumbnail','page-attributes'),
		)
	);
}

add_action( 'init', 'sfhiv_create_report_categories' );
function sfhiv_create_report_categories() {
 $labels = array(
    'name' => _x( 'Report Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Report Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Report Categories' ),
    'all_items' => __( 'All Report Categories' ),
    'parent_item' => __( 'Parent Report Category' ),
    'parent_item_colon' => __( 'Parent Report Category:' ),
    'edit_item' => __( 'Edit Report Category' ),
    'update_item' => __( 'Update Report Category' ),
    'add_new_item' => __( 'Add New Report Category' ),
    'new_item_name' => __( 'New Group Report Name' ),
  ); 	

  register_taxonomy('sfhiv_eport_category',array('sfhiv_report'),array(
    'hierarchical' => true,
    'labels' => $labels,
  ));


?>