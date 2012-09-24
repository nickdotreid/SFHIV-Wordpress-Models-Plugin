<?php

//add_action('save_post','sfhiv_group_event_inherit_status');
function sfhiv_group_event_inherit_status($post_ID){
	if(get_post_type($post_ID) == 'sfhiv_group'){
		$events_query = sfhiv_group_get_events($post_ID);
		foreach($events_query->posts as $event){
			sfhiv_group_event_pass_status($event->ID);
		}
	}
	if(get_post_type($post_ID) == 'sfhiv_event'){
		sfhiv_group_event_pass_status($post_ID);
	}
}

function sfhiv_group_event_pass_status($event_id){
	$groups = new WP_Query( array_merge(array(
		'connected_type' => 'group_events',
		'connected_items' => $event_id,
		'nopaging' => true,
		'post_type' => 'sfhiv_group',
	)));
	$group_ids = array();
	foreach($groups->posts as $group){
		array_push($group_ids,$group->ID);
	}
	if(count($group_ids)<1) return;
	remove_action('save_post','sfhiv_group_event_inherit_status');
	// update event status
	$tax_terms = get_object_taxonomies('sfhiv_group');
	foreach($tax_terms as $tax){
		$parent_terms = wp_get_object_terms($group_ids,$tax,array('fields'=>'slugs'));
		wp_set_object_terms($event_id,$parent_terms,$tax);
	}
	add_action('save_post','sfhiv_group_event_inherit_status');
}

function sfhiv_group_event_update_title($title, $post_ID){
	if(get_post_type($post_ID) != 'sfhiv_event') return $title;
	$groups = new WP_Query(array(
		'connected_type' => 'group_events',
		'connected_items' => $post_ID,
		'nopaging' => true,
		'post_type' => 'sfhiv_group',
	));
	$title = "";
	for($i=0;$i<count($groups->posts);$i++){
		$group = $groups->posts[$i];
		$group_title = apply_filters('the_title',$group->post_title,$group->ID);
		if($i>0){
			$connection = ", ";
			if($i==count($group->posts)-1) $connection = ", and ";
			$title = $title.$connection.$group_title;
		}else{
			$title = $group_title;
		}
	}
	$title = $title." Meeting";
	return $title;
}
add_filter('single_post_title', 'sfhiv_group_event_update_title', 10, 2);
add_filter('the_title', 'sfhiv_group_event_update_title', 10, 2);


?>