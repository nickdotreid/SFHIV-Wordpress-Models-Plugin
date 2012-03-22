<?php

if(!defined('sfhiv_faq')) define('sfhiv_faq','sfhiv_faq',true);

add_action('init','sfhiv_add_faq_type');
function sfhiv_add_faq_type(){
	register_post_type( sfhiv_faq,
		array(
			'labels' => array(
				'name' => __( 'Frequently Asked Questions' ),
				'singular_name' => __( 'Frequently Asked Question' )
			),
		'public' => true,
		'has_archive' => true,
		'hierarchical' => false,
		'rewrite' => array(
			'slug' => 'faq',
			'feeds' => false,
		),
		'capability_type' => 'post',
		'supports' => array('title','editor','excerpt','page-attributes'),
		'taxonomies' => array('sfhiv_service_category','sfhiv_population_category'),
		)
	);
}

?>