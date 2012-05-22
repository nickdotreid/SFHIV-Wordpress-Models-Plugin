<?php

add_action( 'init', 'sfhiv_create_year_tag' );
function sfhiv_create_year_tag() {
 $labels = array(
    'name' => _x( 'Years', 'taxonomy general name' ),
    'singular_name' => _x( 'Year', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Years' ),
    'all_items' => __( 'All Years' ),
    'edit_item' => __( 'Edit Year' ),
    'update_item' => __( 'Update Year' ),
    'add_new_item' => __( 'Add New Year' ),
    'new_item_name' => __( 'New Year Name' ),
  ); 	

  register_taxonomy('sfhiv_year',array(
	'sfhiv_group',
	'sfhiv_document',
	'sfhiv_event'
	),array(
    'hierarchical' => true,
    'labels' => $labels
  ));
}

?>