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

function sfhiv_document_metaboxes(){
//	wp_enqueue_script('sfhiv_event_js', get_bloginfo('stylesheet_directory') . '/models/assets/js/admin-event.js',array('jquery'));
	add_meta_box( 'document', 'Publication Date', 'sfhiv_document_pub_box', 'sfhiv_document', 'side', 'high' );
}

function sfhiv_document_get_pub_date($ID=false){
	if(!$ID){
		$ID = get_the_ID();
	}
	return get_post_meta($ID, 'sfhiv_document_pub_date',true);
}

function sfhiv_document_pub_box($post){
	$pub_date = sfhiv_document_get_pub_date($post->ID);
	$value = "";
	if($pub_date){
		$value = date('F Y',$pub_date);
	}
	echo '<label for="sfhiv_document_pub_date">Publication Date</label>';
	echo '<input type="text" id="sfhiv_document_pub_date" name="sfhiv_document_pub_date" value="'.$value.'" />';
}

add_action( 'save_post', 'sfhiv_document_pub_date_save' );
function sfhiv_document_pub_date_save($post_ID){
	delete_post_meta($post_ID,'sfhiv_document_pub_date');
	if(!isset($_POST['sfhiv_document_pub_date'])) return;
	$date = strtotime($_POST['sfhiv_document_pub_date']);
	add_post_meta($post_ID,'sfhiv_document_pub_date',$date);
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
	if($query->query_vars['post_parent']) return;
	if($query->is_single) return;
	if(!isset($query->query_vars['child_of']))	$query->query_vars['post_parent'] = 0;
}


add_action( 'pre_get_posts', 'sfhiv_document_query_sort', 10 );
function sfhiv_document_query_sort( $query ) {
    if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_document' ) return;
	
	$query->query_vars['meta_key'] = 'sfhiv_document_pub_date';
	$query->query_vars['orderby'] = 'meta_value,ID';
	$query->query_vars['order'] = 'DESC';
	if(!sfhiv_document_test_query($query->query_vars)){
		$query->query_vars['meta_key'] = '';
		$query->query_vars['orderby'] = 'title';
		$query->query_vars['order'] = 'ASC';
	}
}

function sfhiv_document_test_query($args){
	remove_action('pre_get_posts', 'sfhiv_document_query_sort', 10);
	$query = new WP_Query($args);
	add_action( 'pre_get_posts', 'sfhiv_document_query_sort', 10 );
	if($query->post_count > 0){
		return true;
	}
	return false;
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