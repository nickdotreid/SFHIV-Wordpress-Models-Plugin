<?php

add_action('init','sfhiv_add_location_type');
function sfhiv_add_location_type(){
	register_post_type( 'sfhiv_location',
		array(
			'labels' => array(
				'name' => __( 'Locations' ),
				'singular_name' => __( 'Location' ),
				'add_new' => __('Add New','location'),
				'add_new_item' => __('Add New Location'),
				'edit_item' => __('Edit Location'),
				'new_item' => __('New Location'),
				'all_items' => __('All Locations'),
				'view_item' => __('View Location'),
				'search_items' => __('Search Locations'),
				'not_found' => __('No locations found'),
				'not_found_in_trash' => __('No locations found in Trash'),
				'menu_name' => 'Locations',
			),
		'public' => true,
		'exclude_from_search' => true,
		'has_archive' => false,
		'hierarchical' => false,
		'capability_type' => 'page',
		'supports' => array('title','excerpt'),
		'taxonomies' => array(),
		'register_meta_box_cb' => 'sfhiv_add_locations_meta_boxes',
		)
	);
	
	p2p_register_connection_type( array(
		'name' => 'related_location',
		'from' => array('sfhiv_event','sfhiv_service_hour'),
		'to' => 'sfhiv_location',
		'admin_column' => 'from',
		'admin_box' => false,
	) );
}

add_action('init','sfhiv_add_neighborhood_tag');
function sfhiv_add_neighborhood_tag(){
	$labels = array(
    'name' => _x( 'Neighborhood Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Neighborhood Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Neighborhood Categories' ),
    'all_items' => __( 'All Neighborhood Categories' ),
    'parent_item' => __( 'Parent Neighborhood Category' ),
    'parent_item_colon' => __( 'Parent Neighborhood Category:' ),
    'edit_item' => __( 'Edit Neighborhood Category' ),
    'update_item' => __( 'Update Neighborhood Category' ),
    'add_new_item' => __( 'Add New Neighborhood Category' ),
    'new_item_name' => __( 'New Group Neighborhood Name' ),
  ); 	

  register_taxonomy('sfhiv_neighborhood_category',array(
	'sfhiv_location','sfhiv_service_hour','sfhiv_event'
	),
	array(
    'hierarchical' => true,
    'labels' => $labels,
  ));
}

add_filter("manage_sfhiv_location_posts_columns",'sfhiv_location_add_admin_columns');
function sfhiv_location_add_admin_columns($defaults){
	$defaults['room'] = 'Room';
	$defaults['address'] = 'Street Address';
	$defaults['zip_code'] = 'Zip Code';
//	$defaults['city'] = 'City';
//	$defaults['state'] = 'State';
//	$defaults['country'] = 'Country';
	return $defaults;
}
add_action("manage_sfhiv_location_posts_custom_column",'sfhiv_location_add_admin_columns_content',10,2);
function sfhiv_location_add_admin_columns_content($column_name,$post_ID){
	$address = sfhiv_location_get_address(get_post($post_ID));
	if(isset($address[$column_name])){
		echo $address[$column_name];
	}
}


function sfhiv_add_locations_meta_boxes(){
	wp_enqueue_style('sfhiv_location_css', plugins_url('assets/css/admin-location.css',__FILE__));
	//	load google maps
	wp_enqueue_script('sfhiv_location_js', plugins_url('assets/js/admin-location.js',__FILE__),array('jquery'));
	add_meta_box( 'location', 'Where', 'sfhiv_location_metabox', 'sfhiv_location');
}

function sfhiv_location_metabox($post){
	sfhiv_location_address_form($post);
}

function sfhiv_location_get_address($post){
	$data = array();
	
	$data['latitude'] = get_post_meta($post->ID, 'sfhiv_latitude',true);
	$data['longitude'] = get_post_meta($post->ID, 'sfhiv_longitude',true);
	$data['room'] = get_post_meta($post->ID, 'sfhiv_room',true);
	$data['address'] = get_post_meta($post->ID, 'sfhiv_address',true);
	$data['zip_code'] = get_post_meta($post->ID, 'sfhiv_zip_code',true);
	$data['hint'] = get_post_meta($post->ID, 'sfhiv_location_hint',true);
	$data['city'] = get_post_meta($post->ID, 'sfhiv_city',true);
	if(!$data['city']){
		$data['city'] = 'San Francisco';
	}
	$data['state'] = get_post_meta($post->ID, 'sfhiv_state',true);
	if(!$data['state']){
		$data['state'] = 'California';
	}
	$data['country'] = get_post_meta($post->ID, 'sfhiv_country',true);
	if(!$data['country']){
		$data['country'] = 'United States';
	}
	return $data;
}

function sfhiv_location_address_form($post,$args=array()){
	$args = array_merge(array(
		'prefix' => 'sfhiv_location',
	),$args);
	$address = sfhiv_location_get_address($post);
	extract($address);
	?>
	<fieldset class="sfhiv_location address">
		<legend>Address</legend>
		<input type="hidden" name="<?=$args['prefix'];?>[latitude]" value="<?=$latitude;?>" />
		<input type="hidden" name="<?=$args['prefix'];?>[longitude]" value="<?=$longitude;?>" />
		<label for="<?=$args['prefix'];?>-address">Address</label>
		<input id="<?=$args['prefix'];?>-address" type="text" name="<?=$args['prefix'];?>[address]" value="<?=$address;?>" />
		<label for="<?=$args['prefix'];?>-room">Room</label>
		<input id="<?=$args['prefix'];?>-room" type="text" name="<?=$args['prefix'];?>[room]" value="<?=$room;?>" />
		<label for="<?=$args['prefix'];?>-zip_code">Postal Code</label>
		<input id="<?=$args['prefix'];?>-zip_code" type="text" name="<?=$args['prefix'];?>[zip_code]" value="<?=$zip_code;?>" />
		<label for="<?=$args['prefix'];?>-city">City</label>
		<input id="<?=$args['prefix'];?>-city" type="text" name="<?=$args['prefix'];?>[city]" value="<?=$city;?>" />
		<label for="<?=$args['prefix'];?>-state">State</label>
		<input id="<?=$args['prefix'];?>-state" type="text" name="<?=$args['prefix'];?>[state]" value="<?=$state;?>" />
		<label for="<?=$args['prefix'];?>-country">Country</label>
		<input id="<?=$args['prefix'];?>-country" type="text" name="<?=$args['prefix'];?>[country]" value="<?=$country;?>" />
		<label for="<?=$args['prefix'];?>-location_hint" >Hint</label>
		<input id="<?=$args['prefix'];?>-location_hint" type="text" name="<?=$args['prefix'];?>[location_hint]" value="<?=$hint;?>" />
		<label for="<?=$args['prefix'];?>-location_hint" class="hint" >Plain text hint about where this place is. eg. "Close to Mission"</label>
	</fieldset>
	<?
}

function sfhiv_location_new_location_form(){
	?>
	<form class="sfhiv_location create" action="" method="post">
		<a href="#" class="close">Close</a>
		<input type="hidden" name="action" value="sfhiv_location_create">
		<fieldset class="sfhiv_location details">
			<label for="sfhiv_location_new-title">Location Name</label>
			<input id="sfhiv_location_new-title" type="text" name="sfhiv_location[title]" />
		</fieldset>
		<?	sfhiv_location_address_form($empty_post,$args=array());	?>
		<a href="#" class="submit button">Create Location</a>
	</form>
	<?
}

add_action('add_meta_boxes','sfhiv_location_add_choose_location_meta_box');
function sfhiv_location_add_choose_location_meta_box(){
	$post_type = get_post_type(get_the_ID());
	if(!$post_type) return;
	if(!in_array($post_type,array('sfhiv_event','sfhiv_service_hour'))) return;
	wp_enqueue_script('sfhiv_location_js', plugins_url('assets/js/admin-location.js',__FILE__),array('jquery'));
	wp_enqueue_style('sfhiv_location_css', plugins_url('assets/css/admin-location.css',__FILE__));
	add_meta_box('sfhiv_location_choose','Where','sfhiv_location_choose_location_meta_box',$post_type,'side','high');
}

function sfhiv_location_choose_location_meta_box($post){
	sfhiv_location_location_list($post);
}

function sfhiv_location_location_list($post,$args=array()){
	$connected_location = new WP_Query( array(
		'connected_type' => 'related_location',
		'connected_items' => $post->ID,
	));
	$connected_ids = array();
	foreach($connected_location->posts as $location){
		$connected_ids[] = $location->ID;
	}
	sfhiv_location_location_list_draw($connected_ids,$args);
}

function sfhiv_location_location_list_draw($connected_ids,$args=array()){
	$args = array_merge(array(
		'field_name' => 'sfhiv_location',
	),$args);
	?>
	<select class="sfhiv_location list" name="<?=$args['field_name'];?>">
		<option value="">No Location</option>
	<?
	$locations = get_posts(array(
		'post_type'=>'sfhiv_location',
		'nopaging' => true,
	));
	foreach($locations as $location){
		$location_args = array_merge(array(),$args);
		if(in_array($location->ID,$connected_ids)) $location_args['selected']=true;
		sfhiv_location_list_item($location,$location_args);
	}
	?>
	</select>
	<?
	if(!isset($args['hide_create_button'])):
	?>
	<a href="#" class="new sfhiv_location button">New Location</a>
	<?
	endif;
}

function sfhiv_location_list_item($location,$args = array()){
	?>
	<option <?	if($args['selected']) echo 'selected="true"';	?> value="<?=$location->ID;?>"><?=apply_filters('the_title',$location->post_title);?></option>
	<?
}

add_action('wp_ajax_sfhiv_location_form', 'sfhiv_location_ajax_location_form');
function sfhiv_location_ajax_location_form() {
	sfhiv_location_new_location_form();
	die(); // this is required to return a proper result
}

add_action('wp_ajax_sfhiv_location_create', 'sfhiv_location_ajax_location_create');
function sfhiv_location_ajax_location_create() {
	$post_ID = wp_insert_post(array(
		'post_type' => 'sfhiv_location',
		'post_status' => 'publish',
		'post_title' => $_POST['sfhiv_location']['title'],
	));
	if($post_ID){
		sfhiv_location_save($post_ID,$_POST['sfhiv_location']);
		sfhiv_location_list_item(get_post($post_ID));
	}
	die(); // this is required to return a proper result
}

add_action( 'save_post', 'sfhiv_location_post_relation_save' );
function sfhiv_location_post_relation_save($post_ID){
	if(get_post_type($post_ID) == 'sfhiv_location') return;
	if(!isset($_POST['sfhiv_location'])) return;
	sfhiv_location_relation_save($post_ID,$_POST['sfhiv_location']);
}
function sfhiv_location_relation_save($post_ID,$location_ID){
	p2p_delete_connections('related_location',array(
		"from" => $post_ID,
	));
	p2p_create_connection( 'related_location', array(
		'from' => $post_ID,
		'to' => $location_ID,
	));
	$taxonomies = get_object_taxonomies( get_post_type($post_ID) );
	if(in_array('sfhiv_neighborhood_category',$taxonomies)){
		$neighborhood_terms = wp_get_object_terms($location_ID,'sfhiv_neighborhood_category',array('fields'=>'slugs'));
		wp_set_object_terms($post_ID,$neighborhood_terms,'sfhiv_neighborhood_category');
	}
}

add_action( 'save_post', 'sfhiv_location_location_save' );
function sfhiv_location_location_save($post_ID){
	if(get_post_type($post_ID) != 'sfhiv_location') return;
	if(isset($_POST['sfhiv_location']) && is_array($_POST['sfhiv_location'])){
		sfhiv_location_save($post_ID,$_POST['sfhiv_location']);
	}
	$connected_to_location = new WP_Query( array(
		'connected_type' => 'related_location',
		'connected_items' => $post_ID,
	));
	if($connected_to_location->post_count>0){
		foreach($connected_to_location->posts as $post){
			sfhiv_location_relation_save($post->ID,$post_ID);
		}
	}
}
function sfhiv_location_save($post_ID,$postdata = array()){
	foreach($postdata as $key => $value){
		if($value != ""){
			update_post_meta($post_ID,'sfhiv_'.$key,$value);			
		}
	}
}

function sfhiv_location_get_related_location($post_ID){
	$connected_location = new WP_Query( array(
		'connected_type' => 'related_location',
		'connected_items' => $post_ID,
		'nopaging' => true,
	));
	if($connected_location->post_count<1) return false;
	foreach($connected_location->posts as $post){
		return $post;
	}
}


?>