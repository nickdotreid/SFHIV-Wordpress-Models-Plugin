<?php


add_action('init','sfhiv_related_pages');
function sfhiv_related_pages(){
	p2p_register_connection_type( array(
		'name' => 'related_pages',
		'from' => array('page','post','sfhiv_event','sfhiv_group','sfhiv_service','sfhiv_faq','sfhiv_document'),
		'to' => array('page','post','sfhiv_event','sfhiv_group','sfhiv_service','sfhiv_faq','sfhiv_document'),
		'admin_box' => array(
			'show' => 'from',
			'context' => 'advanced',
		),
	));
}

?>