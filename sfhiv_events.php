<?php

add_action('init','sfhiv_add_events_type');
function sfhiv_add_events_type(){
	register_post_type( 'sfhiv_event',
		array(
			'labels' => array(
				'name' => __( 'Events' ),
				'singular_name' => __( 'Event' )
			),
		'public' => true,
		'has_archive' => true,
		'hierarchical' => false,
		'rewrite' => array(
			'slug' => 'events',
			'feeds' => false,
		),
		'capability_type' => 'page',
		'supports' => array('title','editor','excerpt'),
		'taxonomies' => array(
			'sfhiv_service_category',
			'sfhiv_group_category',
			'sfhiv_event_category',
			),
		'register_meta_box_cb' => 'sfhiv_add_events_meta_boxes',
		)
	);
}

function sfhiv_add_events_meta_boxes(){
	add_meta_box( 'event_time', 'When', 'sfhiv_event_time_box', 'sfhiv_event' );
}

function sfhiv_event_time_box($post){
	$start_time = get_post_meta($post->ID, 'sfhiv_event_start',true);
	$end_time = get_post_meta($post->ID, 'sfhiv_event_end',true);
	?>
	<p><label>Start Day:<input type="text" name="sfhiv_event_start_day" value="<?=date('Y-m-d',$start_time);?>" /></label></p>
	<p><label>Start Time:<input type="text" name="sfhiv_event_start_time" value="<?=date('g:i a',$start_time);?>" /></label></p>
	<p><label>End Day:<input type="text" name="sfhiv_event_end_day" value="<?=date('Y-m-d',$end_time);?>" /></label></p>
	<p><label>End Time:<input type="text" name="sfhiv_event_end_time" value="<?=date('g:i a',$end_time);?>" /></label></p>
	<?
}

add_action( 'save_post', 'sfhiv_event_time_save' );
function sfhiv_event_time_save($post_ID,$post){
	if(get_post_type($post_ID) != 'sfhiv_event') return;
	if(isset($_POST['sfhiv_event_start_day']) || $_POST['sfhiv_event_start_day']!="" || isset($_POST['sfhiv_event_start_time']) || $_POST['sfhiv_event_start_time']!=""){
		$start_time = strtotime($_POST['sfhiv_event_start_day'].' '.$_POST['sfhiv_event_start_time']);
		update_post_meta($post_ID, 'sfhiv_event_start', $start_time);
	}
	if(isset($_POST['sfhiv_event_end_day']) || $_POST['sfhiv_event_end_day']!="" || isset($_POST['sfhiv_event_end_time']) || $_POST['sfhiv_event_end_time']!=""){
		$end_time = strtotime($_POST['sfhiv_event_end_day'].' '.$_POST['sfhiv_event_end_time']);
		update_post_meta($post_ID, 'sfhiv_event_end', $end_time);
	}
}

add_action('init','sfhiv_add_event_category');
function sfhiv_add_event_category(){
	$labels = array(
    'name' => _x( 'Event Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Event Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Event Categories' ),
    'all_items' => __( 'All Event Categories' ),
    'parent_item' => __( 'Parent Event Category' ),
    'parent_item_colon' => __( 'Parent Event Category:' ),
    'edit_item' => __( 'Edit Event Category' ),
    'update_item' => __( 'Update Event Category' ),
    'add_new_item' => __( 'Add New Event Category' ),
    'new_item_name' => __( 'New Group Event Name' ),
  ); 	

  register_taxonomy('sfhiv_event_category',array('sfhiv_event'),array(
    'hierarchical' => true,
    'labels' => $labels,
  ));
}

?>