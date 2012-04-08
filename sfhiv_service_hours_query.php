<?php

function sfhiv_orderby_sfhiv_day_of_week( $clauses, $wp_query ) {
	global $wpdb;
 
 	if(!is_admin() && $wp_query->query_vars['post_type'] == 'sfhiv_service_hour'){	
		$clauses['join'] .= <<<SQL
 LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;
 
		$clauses['where'] .= " AND (taxonomy = 'sfhiv_day_of_week_taxonomy' OR taxonomy IS NULL)";
		$clauses['groupby'] = "object_id";
		$add_orderby = "GROUP_CONCAT({$wpdb->terms}.term_order ORDER BY name ASC) ";
 		$add_orderby .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
		
		$clauses['orderby'] = $add_orderby.', '.$clauses['orderby'];
	}
 
	return $clauses;
}
add_filter( 'posts_clauses', 'sfhiv_orderby_sfhiv_day_of_week', 10, 2 );


?>