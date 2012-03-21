<?php

add_action('init','sfhiv_add_services_type');
function sfhiv_add_services_type(){
	register_post_type( 'sfhiv_service',
		array(
			'labels' => array(
				'name' => __( 'Services' ),
				'singular_name' => __( 'Service' )
			),
		'public' => true,
		'has_archive' => true,
		'rewrite' => array(
			'slug' => 'services',
			'feeds' => false,
		),
		'hierarchical' => true,
		'taxonomies' => array('catagories'),
		'register_meta_box_cb' => 'sfhiv_add_services_meta_boxes',
		)
	);
	add_post_type_support( 'sfhiv_service', 'excerpt' );
	/*
	p2p_register_connection_type( array(
		'name' => 'service_location',
		'from' => 'sfhiv_service',
		'to' => 'location',
	) );
	*/
}

add_action('init','sfhiv_add_service_category');
function sfhiv_add_service_category(){
	$labels = array(
    'name' => _x( 'Service Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Service Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Service Categories' ),
    'all_items' => __( 'All Service Categories' ),
    'parent_item' => __( 'Parent Service Category' ),
    'parent_item_colon' => __( 'Parent Service Category:' ),
    'edit_item' => __( 'Edit Service Category' ),
    'update_item' => __( 'Update Service Category' ),
    'add_new_item' => __( 'Add New Service Category' ),
    'new_item_name' => __( 'New Group Service Name' ),
  ); 	

  register_taxonomy('service_category',array('sfhiv_service'),array(
    'hierarchical' => true,
    'labels' => $labels,
  ));
}

add_action('init','sfhiv_add_population_tag');
function sfhiv_add_population_tag(){
	$labels = array(
    'name' => _x( 'Population Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Population Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Population Categories' ),
    'all_items' => __( 'All Population Categories' ),
    'parent_item' => __( 'Parent Population Category' ),
    'parent_item_colon' => __( 'Parent Population Category:' ),
    'edit_item' => __( 'Edit Population Category' ),
    'update_item' => __( 'Update Population Category' ),
    'add_new_item' => __( 'Add New Population Category' ),
    'new_item_name' => __( 'New Group Population Name' ),
  ); 	

  register_taxonomy('sfhiv_population_category',array(
	'sfhiv_service'
	),
	array(
    'hierarchical' => true,
    'labels' => $labels,
  ));
}

function sfhiv_add_services_meta_boxes(){
	
}

function sfhiv_services_hours_op_meta($post){
	$days_in_week = array(
		'Monday',
		'Tuesday',
		'Wednesday',
		'Thursday',
		'Friday',
		'Saturday',
		'Sunday',
	);
	$hours = array();
	$num = 0;
	while($num < 12){
		$num ++;
		array_push($hours,$num);
	}
	$minutes = array();
	$num = 0;
	while($num < 60){
		array_push($minutes,$num);
		$num += 15;
	}
	$ampm = array('AM','PM');
	
	$days = get_post_meta($post->ID, 'sfhiv_service_days',true);
	if($days){
		$days = explode(",",$days);	
	}
	$start = get_post_meta($post->ID, 'sfhiv_service_start',true);
	$start = date('g:i a',$start);
	$end = get_post_meta($post->ID, 'sfhiv_service_end',true);
	$end = date('g:i a',$end);
	$appointment = get_post_meta($post->ID, 'sfhiv_service_appointment');
	
	?>
	<fieldset>
		<legend>Hours of Operation</legend>
		<fieldset>
			<legend>Days of Week</legend>
			<?	foreach($days_in_week as $day):	?>
			<label class="checkbox"><input type="checkbox" name="hours[day_of_week][]" value="<?=$day;?>" <?	if(in_array($day,$days)){ echo 'checked="checked"'; }	?>  /><?=$day;?></label>
			<?	endforeach;	?>
		</fieldset>
		<fieldset>
			<legend>Time</legend>
			<label for="hours_start">Start Time</label>
			<input id="hours_start" type="text" name="hours[start]" value="<?=$start;?>" />
			<label for="hours_end">End Time</label>
			<input id="hours_end" type="text" name="hours[end]" value="<?=$end;?>" />
		</fieldset>
		<fieldset>
			<legend>Appointment</legend>
			<label class="radio"><input type="radio" name="hours[appointment]" value="" />No Appointment</label>
			<label class="radio"><input type="radio" name="hours[appointment]" value="recommended" />Appointment Recommended</label>
			<label class="radio"><input type="radio" name="hours[appointment]" value="required" />Appointment Required</label>
		</fieldset>
	</fieldset>
	<?
}

add_action( 'save_post', 'sfhiv_service_hours_save' );
function sfhiv_service_hours_save($post_ID,$post){

	if(isset($_POST['hours']['day_of_week'])){
		$days = implode(",",$_POST['hours']['day_of_week']);
		update_post_meta($post_ID, 'sfhiv_service_days', $days);
	}
	if(isset($_POST['hours']['start'])){
		$seconds = strtotime($_POST['hours']['start']);
		update_post_meta($post_ID, 'sfhiv_service_start', $seconds);
	}
	if(isset($_POST['hours']['end'])){
		$seconds = strtotime($_POST['hours']['end']);
		update_post_meta($post_ID, 'sfhiv_service_end', $seconds);
	}
	if(isset($_POST['hours']['appointment'])){
		update_post_meta($post_ID, 'sfhiv_service_appointment', $_POST['hours']['appointment']);
	}
}


?>