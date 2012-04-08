<?php

add_filter('posts_join', 'sfhiv_service_hours_days_of_week_join', 10, 2);
function sfhiv_service_hours_days_of_week_join( $join, $query ){
	global $wpdb;
	if(is_admin() || $query->query_vars['post_type'] != 'sfhiv_service_hour') return $join;
	// JOIN day of week hours table && meta data table
	return $join;
}


add_filter('posts_orderby', 'sfhiv_service_hours_days_of_week_orderby', 10, 2 );
function sfhiv_service_hours_days_of_week_orderby( $orderby, $query ){
	global $wpdb;
	if(is_admin() || $query->query_vars['post_type'] != 'sfhiv_service_hour') return $orderby;
	// order by day of week, then time of day
	return $orderby;
}

?>