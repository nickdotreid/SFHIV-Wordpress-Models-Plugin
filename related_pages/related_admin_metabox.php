<?php

add_action( 'add_meta_boxes', 'sfhiv_related_pages_setup_metabox' );
function sfhiv_related_pages_setup_metabox(){
	global $sfhiv_related_pages_types;
	
	wp_enqueue_style('sfhiv_related_pages_css', get_bloginfo('stylesheet_directory') . '/models/assets/css/admin-related_pages.css');
	//	load google maps
	wp_enqueue_script('sfhiv_related_pages_js', get_bloginfo('stylesheet_directory') . '/models/assets/js/admin-related_pages.js',array('jquery'));
	foreach($sfhiv_related_pages_types as $type){
		add_meta_box( 'related_pages', 'Related Pages', 'sfhiv_related_pages_metabox', $type);	
	}
}

function sfhiv_related_pages_metabox($post){
	$related_items = sfhiv_get_related_pages($post->ID);
	include_once('templates/related_pages_form.php');
}

add_action( 'save_post', 'sfhiv_related_pages_post_save' );
function sfhiv_related_pages_post_save($post_ID){
	global $sfhiv_related_pages_types;
	if(!in_array(get_post_type($post_ID),$sfhiv_related_pages_types)) return;
	
	$existing_connections = sfhiv_get_related_pages($post_ID);
	foreach($existing_connections->posts as $page){
		if(!in_array($page->ID,$_POST['sfhiv_related'])){
			p2p_delete_connection($page->p2p_id);
		}
	}
	
	foreach($_POST['sfhiv_related'] as $index => $related_ID){
		$connection_id = false;
		$connections = p2p_get_connections('related_pages',array(
			"from" => $post_ID,
			"to" => $related_ID,
		));
		if(count($connections)>0){
			foreach($connections as $connection){
				p2p_update_meta($connection->p2p_id,'order',$index);
			}
		}else{
			$connection_id = p2p_type( 'related_pages' )->connect( $post_ID, $related_ID);
			if($connection_id) p2p_update_meta($connection_id,'order',$index);
		}
	}
}

add_action('wp_ajax_sfhiv_related_pages_search', 'sfhiv_related_pages_search_ajax');
function sfhiv_related_pages_search_ajax() {
	global $sfhiv_related_pages_types;
	if(isset($_POST['term'])){
		$args = array(
			'post_type' => $sfhiv_related_pages_types,
			'nopaging' => true,
		);
		$args['s'] = $_POST['term'];
		$query = new WP_Query($args);
		foreach($query->posts as $item){
			include('templates/related_page_item.php');
		}
	}
	die(); // this is required to return a proper result
}

?>