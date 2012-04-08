<?php

function sfhiv_orderby_sfhiv_day_of_week( $clauses, $wp_query ) {
	global $wpdb;
 
 	if(!is_admin() && $wp_query->query_vars['post_type'] == 'sfhiv_service_hour'){
		if(isset($wp_query->query_vars['tax_query']) and is_array($wp_query->query_vars['tax_query'])){
			return $clauses;
		}
		if(!isset($wp_query->query_vars['taxonomy'])){
			$clauses['join'] .= " LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id";
			$clauses['where'] .= " AND (taxonomy = 'sfhiv_day_of_week_taxonomy' OR taxonomy IS NULL)";
		}else{
			$clauses['where'] .= " AND (taxonomy IN ('".$wp_query->query_vars['taxonomy']."','sfhiv_day_of_week_taxonomy') OR taxonomy IS NULL)";
		}
		
		$clauses['join'] .= " LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)";
		$clauses['join'] .= " LEFT OUTER JOIN {$wpdb->terms} USING (term_id)";
 
		$clauses['groupby'] = "object_id";
		$add_orderby = "GROUP_CONCAT({$wpdb->terms}.term_order ORDER BY name ASC) ASC";
// 		$add_orderby .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
		
		$clauses['orderby'] = $add_orderby.', '.$clauses['orderby'];
	}
 
	return $clauses;
}
add_filter( 'posts_clauses', 'sfhiv_orderby_sfhiv_day_of_week', 10, 2 );


?>