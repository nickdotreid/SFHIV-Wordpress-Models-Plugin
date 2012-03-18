<?php

add_action('init','sfhiv_add_trainings_type');
function sfhiv_add_trainings_type(){
	register_post_type( 'sfhiv_training',
		array(
			'labels' => array(
				'name' => __( 'Trainings' ),
				'singular_name' => __( 'Training' )
			),
		'public' => true,
		'has_archive' => true,
		'hierarchical' => true,
		'rewrite' => array(
			'slug' => 'trainings',
			'feeds' => false,
		),
		'capability_type' => 'page',
		'supports' => array('title','author','editor','excerpt','thumbnail','page-attributes'),
		'taxonomies' => array('service_category'),
		)
	);
}

?>