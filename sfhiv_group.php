<?php

add_action( 'init', 'sfhiv_create_group_type' );
function sfhiv_create_group_type() {
	register_post_type( 'sfhiv_group',
		array(
			'labels' => array(
				'name' => __( 'Groups' ),
				'singular_name' => __( 'Group' )
			),
		'public' => true,
		'show_ui' => true,
		'has_archive' => false,
		'hierarchical' => true,
		'exclude_from_search' => true,
		'rewrite' => array(
			'slug' => 'groups',
			'feeds' => false,
		),
		'capability_type' => 'page',
		'supports' => array('title','editor','thumbnail','excerpt','page-attributes'),
		'can_export' => true,
		'register_meta_box_cb' => 'sfhiv_add_groups_meta_boxes',
		)
	);
}

add_action( 'pre_get_posts', 'sfhiv_group_sort_order', 5 );
function sfhiv_group_sort_order( $query ) {
	if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_group' ) return;
	$query->query_vars['orderby'] = 'menu_order title date';
	$query->query_vars['order'] = 'ASC';
}

function sfhiv_add_groups_meta_boxes(){
	sfhiv_location_add_choose_location_meta_box('sfhiv_group');
}

add_action( 'init', 'sfhiv_create_group_categories' );
function sfhiv_create_group_categories() {
 $labels = array(
    'name' => _x( 'Group Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Group Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Group Categories' ),
    'all_items' => __( 'All Group Categories' ),
    'parent_item' => __( 'Parent Group Category' ),
    'parent_item_colon' => __( 'Parent Group Category:' ),
    'edit_item' => __( 'Edit Group Category' ),
    'update_item' => __( 'Update Group Category' ),
    'add_new_item' => __( 'Add New Group Category' ),
    'new_item_name' => __( 'New Group Category Name' ),
  ); 	

  register_taxonomy('sfhiv_group_category',array(
	'sfhiv_group',
	'sfhiv_event'
	),array(
    'hierarchical' => true,
    'labels' => $labels,
  ));
}

add_action( 'wp_loaded', 'sfhiv_group_connection_types' );
function sfhiv_group_connection_types() {
	// Make sure the Posts 2 Posts plugin is active.
	if ( !function_exists( 'p2p_register_connection_type' ) )
		return;
		
	p2p_register_connection_type( array(
		'name' => 'group_events',
		'from' => 'sfhiv_group',
		'to' => 'sfhiv_event',
		'title' => array( 'from' => __( 'Events in Group', 'sfhiv' ), 'to' => __( 'Group in Events', 'sfhiv' ) ),
		'admin_box' => array(
				'show' => 'any',
				'context' => 'advanced'
		),
	));

	p2p_register_connection_type( array(
		'name' => 'group_services',
		'from' => 'sfhiv_group',
		'to' => 'sfhiv_service',
	));

	p2p_register_connection_type( array(
		'name' => 'group_members',
		'from' => 'sfhiv_group',
		'to' => 'user',
		'title' => array( 'from' => __( 'Members in Group', 'sfhiv' ), 'to' => __( 'Groups for Member', 'sfhiv' ) ),
		'fields' => array(
				'title' => 'Title',
				'weight' => 'Weight',
				'group' => 'Grouping',
				'show_contact_info' => array(
					'title' => 'Contactable',
					'type' => 'checkbox',
				),
			),
		'admin_box' => array(
				'show' => 'any',
				'context' => 'advanced'
		),
	));
}

include_once('sfhiv_group_member.php');
include_once('sfhiv_group_event.php');

?>