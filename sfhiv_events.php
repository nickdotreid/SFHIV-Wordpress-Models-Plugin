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
	wp_enqueue_script('sfhiv_event_js', get_bloginfo('stylesheet_directory') . '/models/assets/js/admin-event.js',array('jquery'));
	add_meta_box( 'event_time', 'When', 'sfhiv_event_time_box', 'sfhiv_event', 'side', 'high' );
	sfhiv_location_add_choose_location_meta_box('sfhiv_event');
}

function sfhiv_event_time_box($post){
	$start_time = get_post_meta($post->ID, 'sfhiv_event_start',true);
	$end_time = get_post_meta($post->ID, 'sfhiv_event_end',true);
	?>
	<p><label>Start Day:<input id="sfhiv_event_start_date" class="sfhiv-date" type="text" name="sfhiv_event[start][date]" value="<?=date('Y-m-d',$start_time);?>" /></label></p>
	<p><label>Start Time:<input id="sfhiv_event_start_time" class="sfhiv-time" type="text" name="sfhiv_event[start][time]" value="<?=date('g:i a',$start_time);?>" /></label></p>
	<p><label>End Day:<input id="sfhiv_event_end_date" class="sfhiv-date" type="text" name="sfhiv_event_end_day[end][date]" value="<?=date('Y-m-d',$end_time);?>" /></label></p>
	<p><label>End Time:<input id="sfhiv_event_end_time" class="sfhiv-time" type="text" name="sfhiv_event_end_time[end][time]" value="<?=date('g:i a',$end_time);?>" /></label></p>
	<?
}

add_action( 'save_post', 'sfhiv_event_time_save' );
function sfhiv_event_time_save($post_ID){
	if(get_post_type($post_ID) != 'sfhiv_event') return;
	$start_time = sfhiv_event_save_time($post_ID,'sfhiv_event_start',$_POST['sfhiv_event']['start']);
	$end_time = sfhiv_event_save_time($post_ID,'sfhiv_event_end',$_POST['sfhiv_event']['end']);
	
	if($start_time && $end_time && $end_time<$start_time){
		update_post_meta($post_ID, 'sfhiv_event_end', $start_time);
	}
	if($start_time && !$end_time){
		update_post_meta($post_ID, 'sfhiv_event_end', $start_time);
	}
}

function sfhiv_event_save_time($post_ID,$key,$postdata){
	if(!isset($postdata['date']) || $postdata['date']=="" || !isset($postdata['time']) || $postdata['time']=="") return false;
	$time = strtotime($postdata['date'].' '.$postdata['time']);
	if(!$time)	return false;
	update_post_meta($post_ID, $key, $time);
	return $time;
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

function sfhiv_event_order_query( $query ) {
    if ( is_admin() || $query->query_vars['post_type'] != 'sfhiv_event' ) return;
	if(isset($_GET['sfhiv_event_time'])){
		$meta_query = array();
		if($_GET['sfhiv_event_time']=='upcoming'){
			$meta_query[] = array(
	            'key' => 'sfhiv_event_start',
	            'value' => time(),
	            'compare' => '>'
	        );
		}
		if($_GET['sfhiv_event_time']=='past'){
			$meta_query[] = array(
	            'key' => 'sfhiv_event_start',
	            'value' => time(),
	            'compare' => '<'
	        );
		}
	    $query->set( 'meta_query', $meta_query );
	}
    $query->set( 'orderby', 'meta_value_num' );
    $query->set( 'meta_key', 'sfhiv_event_start' );
    $query->set( 'order', 'ASC' );
}
add_action( 'pre_get_posts', 'sfhiv_event_order_query' );

?>