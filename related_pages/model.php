<?php

$sfhiv_related_pages_types = array('page','post','sfhiv_event','sfhiv_group','sfhiv_service','sfhiv_faq','sfhiv_document');

add_action('init','sfhiv_related_pages');
function sfhiv_related_pages(){
	global $sfhiv_related_pages_types;
	p2p_register_connection_type( array(
		'name' => 'related_pages',
		'from' => $sfhiv_related_pages_types,
		'to' => $sfhiv_related_pages_types,
		'admin_box' => false,
	));
}

?>