<?php


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
	sfhiv_users_sort_by_name($users);
	usort($users,function($a,$b){
		$a_title = p2p_get_meta( $a->p2p_id, 'title', true );
		$b_title = p2p_get_meta( $b->p2p_id, 'title', true );
		if($a_title && $b_title){
			$a_order = p2p_get_meta( $a->p2p_id, 'order', true );
			$b_order = p2p_get_meta( $b->p2p_id, 'order', true );
			if($a_order && $b_order){
				if($a_order < $b_order){
					return -1;
				}else{
					return 1;
				}
			}
			if($a_order){
				return -1;
			}
			if($b_order){
				return 1;
			}
			return 0;
		}
		if($a_title){
			return -1;
		}
		if($b_title){
			return 1;
		}
		return 0;
	});
	return $users;
}

?>