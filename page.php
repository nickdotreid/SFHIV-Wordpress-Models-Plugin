<?php

add_action('init','sfhiv_related_pages');
function sfhiv_related_pages(){
	p2p_register_connection_type( array(
		'name' => 'related_pages',
		'from' => array('page','post','sfhiv_event','sfhiv_group','sfhiv_service','sfhiv_faq'),
		'to' => array('page','post','sfhiv_event','sfhiv_group','sfhiv_service','sfhiv_faq'),
		'cardinality' => 'one-to-many',
		'title' => array( 'from' => 'From Page', 'to' => 'To Page' ),
	));
}

function sfhiv_has_related_pages($ID=false){
	if(!$ID){
		$ID = get_the_ID();
	}
	$services = new WP_Query( array(
		'connected_type' => 'related_pages',
		'connected_items' => get_the_ID(),
	));
	if($services->have_posts()){
		return true;
	}
	return false;
}

function sfhiv_get_related_pages($id=false){
	if(!$ID){
		$ID = get_the_ID();
	}
	$services = new WP_Query( array(
		'connected_type' => 'related_pages',
		'connected_items' => get_the_ID(),
		'nopaging' => true,
	));
	return $services;
}
?>