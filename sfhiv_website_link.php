<?php

add_filter( 'cmb_meta_boxes', 'sfhiv_website_link_box', 10 );
function sfhiv_website_link_box( $meta_boxes ){
	$sfhiv_website_linked_post_types = array(
		'sfhiv_document',
		'sfhiv_study',
		'sfhiv_group',
		'sfhiv_service',
		'sfhiv_service_hour',
		'sfhiv_service_provider',
		'sfhiv_event',
		'post',
		'page',
		);
	$meta_boxes[] = array(
		'id'         => 'sfhiv_website_link',
		'title'      => 'External Link',
		'pages'      => $sfhiv_website_linked_post_types,
		'context'    => 'side',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields' => array(
			array(
				'name' => 'External Link',
				'desc' => 'External link for this content item',
				'id'   => 'sfhiv_website_link',
				'type' => 'text',
			),
			array(
				'name' => 'Forward',
				'desc' => 'Automatically Forward All Traffic',
				'id'   => 'sfhiv_website_link_forward',
				'type' => 'checkbox',
			),
		)
	);
	return $meta_boxes;
}


function sfhiv_website_link_get_link($post_ID=false){
	if(!$post_ID) $post_ID = get_the_ID();
	$link = get_post_meta($post_ID,'sfhiv_website_link',true);
	if(!$link) return false;
	return (object) array(
		"link" => $link,
		"forward" => get_post_meta($post_ID,'sfhiv_website_link_forward',true),
		"name" => get_post_meta($post_ID,'sfhiv_website_link_name',true),
	);
}

add_filter('post_link','sfhiv_website_link_filter',2,2);
add_filter('post_type_link','sfhiv_website_link_filter',2,2);
add_filter('page_link','sfhiv_website_link_filter',2,2);
function sfhiv_website_link_filter($link,$post_id){
	if(is_object($post_id)){
		$post_id = $post_id->ID;
	}
	$forward = get_post_meta($post_id,'sfhiv_website_link_forward',true);
	if($forward){
		return get_post_meta($post_id,'sfhiv_website_link',true);
	}
	return $link;
}

?>