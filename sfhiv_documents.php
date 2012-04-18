<?php

add_action('init','sfhiv_add_documents_type');
function sfhiv_add_documents_type(){
	register_post_type( 'sfhiv_document',
		array(
			'labels' => array(
				'name' => __( 'Documents' ),
				'singular_name' => __( 'Document' )
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
    if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_document' ) return;
	if(!isset($query->query_vars['child_of']))	$query->query_vars['post_parent'] = 0;
}

?>