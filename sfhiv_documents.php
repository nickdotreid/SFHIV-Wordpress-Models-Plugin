<?php

add_action('init','sfhiv_add_documents_type');
function sfhiv_add_documents_type(){
	register_post_type( 'sfhiv_document',
		array(
			'labels' => array(
				'name' => __( 'Documents' ),
				'singular_name' => __( 'Document' ),
				'add_new' => __('Add New','document'),
				'add_new_item' => __('Add New Document'),
				'edit_item' => __('Edit Document'),
				'new_item' => __('New Document'),
				'all_items' => __('All Documents'),
				'view_item' => __('View Document'),
				'search_items' => __('Search Documents'),
				'not_found' => __('No documents found'),
				'not_found_in_trash' => __('No documents found in Trash'),
				'menu_name' => 'Documents',
			),
		'public' => true,
		'has_archive' => true,
		'hierarchical' => true,
		'rewrite' => array(
			'slug' => 'documents',
			'feeds' => false,
		),
		'capability_type' => 'page',
		'supports' => array('title','author','editor','excerpt','thumbnail','page-attributes'),
		'taxonomies' => array(
			'sfhiv_service_category',
			'sfhiv_population_category',
			'sfhiv_document_category',
			),
		'register_meta_box_cb' => 'sfhiv_document_metaboxes',
		)
	);
}

add_action('init','sfhiv_add_document_category');
function sfhiv_add_document_category(){
	$labels = array(
    'name' => _x( 'Document Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Document Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Document Categories' ),
    'all_items' => __( 'All Document Categories' ),
    'parent_item' => __( 'Parent Document Category' ),
    'parent_item_colon' => __( 'Parent Document Category:' ),
    'edit_item' => __( 'Edit Document Category' ),
    'update_item' => __( 'Update Document Category' ),
    'add_new_item' => __( 'Add New Document Category' ),
    'new_item_name' => __( 'New Group Document Name' ),
  ); 	

  register_taxonomy('sfhiv_document_category',array('sfhiv_document'),array(
    'hierarchical' => true,
    'labels' => $labels,
  ));
}

add_action('init','sfhiv_add_role_category');
function sfhiv_add_role_category(){
	$labels = array(
    'name' => _x( 'Role Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Role Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Role Categories' ),
    'all_items' => __( 'All Role Categories' ),
    'parent_item' => __( 'Parent Role Category' ),
    'parent_item_colon' => __( 'Parent Role Category:' ),
    'edit_item' => __( 'Edit Role Category' ),
    'update_item' => __( 'Update Role Category' ),
    'add_new_item' => __( 'Add New Role Category' ),
    'new_item_name' => __( 'New Group Role Name' ),
  ); 	

  register_taxonomy('sfhiv_role_category',array('sfhiv_document','sfhiv_role'),array(
    'hierarchical' => true,
    'labels' => $labels,
  ));
}

add_action( 'pre_get_posts', 'sfhiv_document_query_top_level_only', 5 );
function sfhiv_document_query_top_level_only( $query ) {
    if ( is_admin() || !isset($query->query_vars['post_type']) || $query->query_vars['post_type'] != 'sfhiv_document' ) return;
	if($query->query_vars['post_parent']) return;
	if($query->is_single) return;
	if(!isset($query->query_vars['child_of']))	$query->query_vars['post_parent'] = 0;
}


add_action( 'pre_get_posts', 'sfhiv_document_query_sort_by_date', 10 );
function sfhiv_document_query_sort_by_date($query){
	if ( is_admin() || !isset($query->query_vars['post_type']) || $query->query_vars['post_type'] != 'sfhiv_document' ) return;
	
	$query->query_vars['orderby'] = 'date,ID';
	$query->query_vars['order'] = 'DESC';
}

remove_action( 'future_sfhiv_document', '_future_post_hook', 5, 2 );
add_action( 'future_sfhiv_document', 'sfhiv_document_future_post_hook', 5, 2);
function sfhiv_document_future_post_hook( $deprecated = '', $post){
	wp_publish_post( $post->ID );
}

function sfhiv_document_get_studies($ID=false){
	if(!$ID){
		$ID = get_the_ID();
	}
	$studies = new WP_Query( array(
		'post_type' => 'sfhiv_study',
		'connected_type' => 'sfhiv_study_document',
		'connected_items' => $ID,
	));
	return $studies;
}

?>