<?php

add_action('init','sfhiv_add_reports_type');
function sfhiv_add_reports_type(){
	register_post_type( 'sfhiv_report',
		array(
			'labels' => array(
				'name' => __( 'Reports' ),
				'singular_name' => __( 'Report' )
			),
		'public' => true,
		'has_archive' => true,
		'hierarchical' => true,
		'rewrite' => array(
			'slug' => 'reports',
			'feeds' => true,
		),
		'capability_type' => 'page',
		'supports' => array('title','author','editor','excerpt','thumbnail','page-attributes'),
		)
	);
}


?>