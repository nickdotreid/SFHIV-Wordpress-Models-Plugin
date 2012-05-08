<?php

add_action('save_post','sfhiv_group_event_inherit_status');
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


?>