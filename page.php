<?php

add_action('init','sfhiv_related_pages');
function sfhiv_related_pages(){
	p2p_register_connection_type( array(
		'name' => 'related_pages',
		'from' => array('page','post','sfhiv_event','link'),
		'to' => 'page',
		'cardinality' => 'one-to-many',
		'title' => array( 'from' => 'Linked To', 'to' => 'Linked From' ),
	));
}

?>