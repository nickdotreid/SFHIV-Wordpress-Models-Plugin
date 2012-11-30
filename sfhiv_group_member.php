<?php

add_action( 'wp_loaded', 'sfhiv_group_connection_to_users' );
function sfhiv_group_connection_to_users() {
	// Make sure the Posts 2 Posts plugin is active.
	if ( !function_exists( 'p2p_register_connection_type' ) )
		return;
	
	p2p_register_connection_type( array(
		'name' => 'group_members',
		'from' => 'sfhiv_group',
		'to' => 'user',
		'title' => array( 'from' => __( 'Members in Group', 'sfhiv' ), 'to' => __( 'Groups for Member', 'sfhiv' ) ),
		'fields' => array(
				'hide' => array(
					'title' => 'Hide Title',
					'type' => 'checkbox',
				),
				'title' => 'Title',
				'weight' => 'Weight',
				'group' => 'Grouping',
				'show_contact_info' => array(
					'title' => 'Contactable',
					'type' => 'checkbox',
				),
			),
#		'admin_box' => false,
	));
}

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
	$users = sfhiv_group_get_members($ID);
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

/*** ADD MEMBER META BOX ***/
add_action('add_meta_boxes','sfhiv_group_member_metabox');
function sfhiv_group_member_metabox(){
	$post_type = get_post_type(get_the_ID());
	if(!in_array($post_type,array('sfhiv_group'))) return;
	wp_enqueue_script('sfhiv_group_member_js', plugins_url('assets/js/admin-group-members.js',__FILE__),array('jquery'));
	wp_enqueue_style('sfhiv_group_member_css', plugins_url('assets/css/admin-group-members.css',__FILE__));
	add_meta_box('sfhiv-create-member','Create New Member','sfhiv_group_members_draw_meta_box',$post_type,'advanced','low');
}

function sfhiv_group_members_draw_meta_box(){
	?>
	<div class="sfhiv-new-member">
		<label for="sfhiv-member-first-name">First Name</label>
		<input type="text" class="first-name" name="sfhiv-member-first-name" />
		<label for="sfhiv-member-last-name">Last Name</label>
		<input type="text" class="last-name" name="sfhiv-member-last-name" />
		<input type="submit" class="create-button button" value="Add Member" />
	</div>
	<?
}

add_action('wp_ajax_sfhiv_member_create', 'sfhiv_group_members_create_new');
function sfhiv_group_members_create_new() {
	// make sure first and last name set -- otherwise error
	if(!isset($_POST['first_name']) || !isset($_POST['last_name'])){
		echo json_encode(array(
		"ID"=>false,
		));
	}
	$user_ID = wp_insert_user(array(
		'user_login' => sfhiv_group_member_make_username($_POST['first_name']." ".$_POST['last_name']),
		'first_name' => $_POST['first_name'],
		'last_name' => $_POST['last_name'],
		));
	echo json_encode(array(
		"ID"=>$user_ID,
		));
	die(); // this is required to return a proper result
}

function sfhiv_group_member_make_username($name,$append=0){
	if($append > 0){
		$name = $name.$append;
	}
	$name = strtolower($name);
	$name = str_replace(' ','.',$name);
	preg_replace("/[^A-Za-z0-9\. ]/", '', $string);
	#check if name is unique -- otherwise append random numbers
	$users = get_users('search='.$name);
	if(count($users) > 0){
		$append += 1;
		return sfhiv_group_member_make_username($name,$append);
	}
	return $name;
}

?>