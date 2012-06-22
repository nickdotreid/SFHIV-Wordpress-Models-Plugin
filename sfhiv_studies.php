<?php

add_action('init','sfhiv_add_studies_type');
function sfhiv_add_studies_type(){
	register_post_type( 'sfhiv_study',
		array(
			'labels' => array(
				'name' => __( 'Studies' ),
				'singular_name' => __( 'Study' ),
				'add_new' => __('Add New','study'),
				'add_new_item' => __('Add New Study'),
				'edit_item' => __('Edit Study'),
				'new_item' => __('New Study'),
				'all_items' => __('All Studies'),
				'view_item' => __('View Study'),
				'search_items' => __('Search Studies'),
				'not_found' => __('No studies found'),
				'not_found_in_trash' => __('No studies found in Trash'),
				'menu_name' => 'Studies',
			),
		'public' => true,
		'has_archive' => true,
		'hierarchical' => true,
		'rewrite' => array(
			'slug' => 'studies',
			'feeds' => true,
		),
		'capability_type' => 'page',
		'supports' => array('title','editor','excerpt','thumbnail','page-attributes'),
		)
	);
	
	p2p_register_connection_type( array(
		'name' => 'sfhiv_study_document',
		'from' => 'sfhiv_study',
		'to' => 'sfhiv_document',
		'admin_column' => 'to',
	) );
	
	p2p_register_connection_type( array(
		'name' => 'sfhiv_group_study',
		'from' => 'sfhiv_group',
		'to' => 'sfhiv_study',
		'admin_column' => 'to',
	) );
}

add_action( 'init', 'sfhiv_create_study_categories' );
function sfhiv_create_study_categories() {
 $labels = array(
    'name' => _x( 'Study Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Study Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Study Categories' ),
    'all_items' => __( 'All Study Categories' ),
    'parent_item' => __( 'Parent Study Category' ),
    'parent_item_colon' => __( 'Parent Study Category:' ),
    'edit_item' => __( 'Edit Study Category' ),
    'update_item' => __( 'Update Study Category' ),
    'add_new_item' => __( 'Add New Study Category' ),
    'new_item_name' => __( 'New Group Study Name' ),
  ); 	

  register_taxonomy('sfhiv_study_category',array('sfhiv_study'),array(
    'hierarchical' => true,
    'labels' => $labels,
  ));
}

add_action( 'pre_get_posts', 'sfhiv_study_query_top_level_only', 5 );
function sfhiv_study_query_top_level_only( $query ) {
	if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_study' ) return;
	if($query->query_vars['post_parent']) return;
	if($query->is_single) return;
	if(!isset($query->query_vars['child_of']))	$query->query_vars['post_parent'] = 0;
}

add_action( 'pre_get_posts', 'sfhiv_study_orderby_menu_order', 5 );
function sfhiv_study_orderby_menu_order( $query ) {
	if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_study' ) return;
	$query->set('orderby','menu_order');
	$query->set('order','ASC');
}

?>