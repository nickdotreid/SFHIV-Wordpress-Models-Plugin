<?php

add_action('init','sfhiv_add_faq_type');
function sfhiv_add_faq_type(){
	register_post_type( 'sfhiv_faq',
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
		'supports' => array('title','editor','page-attributes'),
		'taxonomies' => array('sfhiv_service_category','sfhiv_population_category'),
		)
	);
}

add_action( 'pre_get_posts', 'sfhiv_faq_order_query', 5 );
function sfhiv_faq_order_query( $query ) {
	if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_faq' ) return;
	$query->set( 'orderby', 'menu_order' );
	$query->set( 'order', 'ASC' );
}

?>