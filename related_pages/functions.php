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