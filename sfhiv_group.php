<?php

add_action( 'init', 'sfhiv_create_group_type' );
function sfhiv_create_group_type() {
	register_post_type( 'sfhiv_group',
		array(
			'labels' => array(
				'name' => __( 'Groups' ),
				'singular_name' => __( 'Group' )
			),
		'public' => true,
		'has_archive' => true,
		'rewrite' => array(
			'slug' => 'groups',
			'feeds' => false,
		),
		'supports' => array('title','editor','thumbnail','excerpt','page-attributes'),
		'can_export' => true,
		)
	);
	add_post_type_support( 'group', 'excerpt' );
}

?>