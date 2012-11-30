<?php

add_action( 'init', 'sfhiv_create_group_type' );
function sfhiv_create_group_type() {
	register_post_type( 'sfhiv_group',
		array(
			'labels' => array(
				'name' => __( 'Groups' ),
				'singular_name' => __( 'Group' ),
				'add_new' => __('Add New','group'),
				'add_new_item' => __('Add New Group'),
				'edit_item' => __('Edit Group'),
				'new_item' => __('New Group'),
				'all_items' => __('All Groups'),
				'view_item' => __('View Group'),
				'search_items' => __('Search Groups'),
				'not_found' => __('No groups found'),
				'not_found_in_trash' => __('No groups found in Trash'),
				'menu_name' => 'Groups',
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
	$query->query_vars['nopaging'] = true;
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

include_once('sfhiv_group_member.php');
include_once('sfhiv_group_event.php');

?>