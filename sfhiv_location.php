<?php

add_action('init','sfhiv_add_location_type');
function sfhiv_add_location_type(){
	register_post_type( 'sfhiv_location',
		array(
			'labels' => array(
				'name' => __( 'Locations' ),
				'singular_name' => __( 'Location' )
			),
		'public' => true,
		'has_archive' => false,
		'hierarchical' => false,
		'capability_type' => 'page',
		'supports' => array('title','excerpt'),
		'taxonomies' => array(),
		'register_meta_box_cb' => 'sfhiv_add_locations_meta_boxes',
		)
	);
}

function sfhiv_add_locations_meta_boxes(){
	//wp_enqueue_script('sfhiv_event_js', get_bloginfo('stylesheet_directory') . '/models/assets/js/admin-event.js',array('jquery'));
	add_meta_box( 'location', 'Where', 'sfhiv_location_metabox', 'sfhiv_location');
}

function sfhiv_location_metabox($post){
	$args = false;
	if(!$args){
		$args = array(
			'prefix' => 'sfhiv_location',
		);
	}
	
	$latitude = get_post_meta($post->ID, 'sfhiv_latitude',true);
	$longitude = get_post_meta($post->ID, 'sfhiv_longitude',true);
	$address = get_post_meta($post->ID, 'sfhiv_address',true);
	$zip_code = get_post_meta($post->ID, 'sfhiv_zip_code',true);
	$hint = get_post_meta($post->ID, 'sfhiv_location_hint',true);
	$city = get_post_meta($post->ID, 'sfhiv_city',true);
	if(!$city){
		$city = 'San Francisco';
	}
	$state = get_post_meta($post->ID, 'sfhiv_state',true);
	if(!$state){
		$state = 'California';
	}
	$country = get_post_meta($post->ID, 'sfhiv_country',true);
	if(!$country){
		$country = 'United States';
	}
	?>
	<input type="hidden" name="<?=$args['prefix'];?>[latitude]" value="<?=$latitude;?>" />
	<input type="hidden" name="<?=$args['prefix'];?>[longitude]" value="<?=$longitude;?>" />
	<p><label>Address:<input type="text" name="<?=$args['prefix'];?>[address]" value="<?=$address;?>" /></label></p>
	<p><label>Postal Code:<input type="text" name="<?=$args['prefix'];?>[zip_code]" value="<?=$zip_code;?>" /></label></p>
	<p><label>City:<input type="text" name="<?=$args['prefix'];?>[city]" value="<?=$city;?>" /></label></p>
	<p><label>State:<input type="text" name="<?=$args['prefix'];?>[state]" value="<?=$state;?>" /></label></p>
	<p><label>Country:<input type="text" name="<?=$args['prefix'];?>[country]" value="<?=$country;?>" /></label></p>
	<p><label>Hint:<input type="text" name="<?=$args['prefix'];?>[location_hint]" value="<?=$hint;?>" /></label></p>
	<?
}

add_action( 'save_post', 'sfhiv_location_location_save' );
function sfhiv_location_location_save($post_ID){
	if(get_post_type($post_ID) != 'sfhiv_location') return;
	if(isset($_POST['sfhiv_location']) && is_array($_POST['sfhiv_location'])){
		sfhiv_location_save($post_ID,$_POST['sfhiv_location']);
	}
}
function sfhiv_location_save($post_ID,$postdata = array()){
	foreach($postdata as $key => $value){
		if($value != ""){
			update_post_meta($post_ID,'sfhiv_'.$key,$value);			
		}
	}
}


?>