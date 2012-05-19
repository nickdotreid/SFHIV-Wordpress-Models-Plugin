<?php

add_filter('wp_page_menu_args','sfhiv_change_post_status');
add_filter('sfhiv_filter_args','sfhiv_change_post_status');
function sfhiv_change_post_status($args){
	if(!is_user_logged_in()) return $args;
	$args['post_status'] = array("publish","private","draft","pending");
	return $args;
}

?>