<?php

require_once('model.php');

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
	global $sfhiv_related_pages_types;
	if(!$ID){
		$ID = get_the_ID();
	}
	$services = p2p_type( 'related_pages' )->get_connected( $ID, array(
		'post_type' => $sfhiv_related_pages_types,
		'nopaging' => true,
	) );
	return $services;
}

?>