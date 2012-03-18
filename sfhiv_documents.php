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
		'hierarchical' => false,
		'rewrite' => array(
			'slug' => 'documents',
			'feeds' => false,
		),
		'capability_type' => 'post',
		'supports' => array('title','editor','excerpt'),
		'taxonomies' => array(
			'service_category',
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

?>