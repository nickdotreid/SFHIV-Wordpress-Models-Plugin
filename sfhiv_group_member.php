<?php

add_filter('sfhiv_users_sort','sfhiv_group_members_sort_by_weight',14);
function sfhiv_group_members_sort_by_weight($users){
	$sfhiv_original_user_order = $users;
	usort($users,sfhiv_group_members_sort_by_weight_cmp);
	return $users;
}

function sfhiv_group_members_sort_by_weight_cmp($a,$b){
	if(!$a->p2p_id || !$b->p2p_id) return sfhiv_users_sort_by_name_cmp($a,$b);
	$a_order = p2p_get_meta( $a->p2p_id, 'weight', true );
	$b_order = p2p_get_meta( $b->p2p_id, 'weight', true );
	if($a_order && $a_order != "" && $b_order && $b_order != ""){
		if($a_order == $b_order){
			return sfhiv_users_sort_by_name_cmp($a,$b);
		}else if($a_order < $b_order){
			return -1;
		}else{
			return 1;
		}
	}
	if($a_order && $a_order != ""){
		return -1;
	}
	if($b_order && $b_order != ""){
		return 1;
	}
	return sfhiv_users_sort_by_name_cmp($a,$b);
}

function sfhiv_group_has_members($ID=false){
	if(!$ID){
		$ID = get_the_ID();
	}
	$users = get_users( array(
	  'connected_type' => 'group_members',
	  'connected_items' => get_the_ID(),
		'orderby' => 'display_name',
	));
	if(count($users)>0){
		return true;
	}
	return false;
}

function sfhiv_group_get_members($ID = false){
	if(!$ID){
		$ID = get_the_ID();
	}
	$users = get_users( array(
	  'connected_type' => 'group_members',
	  'connected_items' => get_the_ID(),
		'orderby' => 'display_name',
	));
	if(count($users)<1) return array();
	sfhiv_users_sort($users);
	return $users;
}

?>