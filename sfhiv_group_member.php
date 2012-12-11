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
		'admin_box' => 	array(
			'show' => 'any',
			'context' => 'advanced'
		),
	));
}

add_filter('sfhiv_users_sort','sfhiv_group_members_sort_by_weight',14);
function sfhiv_group_members_sort_by_weight($users){
	$sfhiv_original_user_order = $users;
	usort($users,function($a,$b){
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
	});
	return $users;
}

function sfhiv_group_has_members($ID=false){
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
	  'connected_items' => $ID,
		'orderby' => 'display_name',
	));
	if(count($users)<1) return array();
	sfhiv_users_sort($users);
	return $users;
}

add_filter( 'p2p_admin_box_show', 'sfhiv_show_user_meta_box', 10, 3 );
function sfhiv_show_user_meta_box( $show, $ctype, $post ){
	if($ctype->name == 'group_members'){
		return true;
	}
	return $show;
}

/*** ADD MEMBER META BOX ***/
add_action('add_meta_boxes','sfhiv_group_member_metabox');
function sfhiv_group_member_metabox(){
	$post_type = get_post_type(get_the_ID());
	if(!in_array($post_type,array('sfhiv_group'))) return;
	wp_enqueue_script('sfhiv_underscore_js', plugins_url('assets/js/underscore.min.js',__FILE__));
	wp_enqueue_script('sfhiv_group_member_js', plugins_url('assets/js/admin-group-members.js',__FILE__),array('jquery'));
	wp_enqueue_style('sfhiv_group_member_css', plugins_url('assets/css/admin-group-members.css',__FILE__));
	add_meta_box('sfhiv-group-members','Create New Member','sfhiv_group_members_draw_meta_box',$post_type,'advanced','low');
}

add_action( 'save_post', 'sfhiv_group_members_save_connection_info' );
function sfhiv_group_members_save_connection_info($post_ID){
	if(get_post_type($post_ID) != 'sfhiv_group') return;
	if(!isset($_POST['sfhiv-group-member'])) return;
	$connections = $_POST['sfhiv-group-member'];
	foreach($connections as $id => $connection){
		foreach($connection as $key => $value ){
			p2p_update_meta($id,$key,$value);
		}
	}
}

function sfhiv_group_members_draw_meta_box(){
	?>
	<div id="sfhiv-create-member" class="sfhiv-new-member">
		<label for="sfhiv-member-first-name">First Name</label>
		<input type="text" class="first-name" name="sfhiv-member-first-name" />
		<label for="sfhiv-member-last-name">Last Name</label>
		<input type="text" class="last-name" name="sfhiv-member-last-name" />
		<input type="submit" class="create-button button" value="Add New Member" />
	</div>
	<?
}

function sfhiv_group_member_get_object($user){
	return $member = array(
		"ID" => $user->ID,
		"display_name" => $user->display_name,
		"p2p_id" => $user->p2p_id,
		"first_name" => get_user_meta($user->ID,"first_name",true),
		"last_name" => get_user_meta($user->ID,"last_name",true),
		'hide' => p2p_get_meta($user->p2p_id,'hide',true),
		'title' => p2p_get_meta($user->p2p_id,'title',true),
		'weight' => p2p_get_meta($user->p2p_id,'weight',true),
		'group' => p2p_get_meta($user->p2p_id,'group',true),
		'show_contact_info' => p2p_get_meta($user->p2p_id,'hide',true),
		);
}

add_action('wp_ajax_sfhiv_member_add', 'sfhiv_group_members_ajax_add');
function sfhiv_group_members_ajax_add() {
	$members = array();
	if(isset($_POST['group_id']) && isset($_POST['user_id'])){
		p2p_type( 'group_members' )->connect( $_POST['group_id'], $_POST['user_id'], array() );
		$user = get_user_by('id',$_POST['user_id']);
		if($user){
			$members[] = sfhiv_group_member_get_object($user);
		}
	}
	echo json_encode(array(
	"members" => $members,
	));
	die();
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
		'user_pass' => sfhiv_group_member_make_password(),
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
	preg_replace("/[^A-Za-z0-9\. ]/", '', $name);
	#check if name is unique -- otherwise append random numbers
	$users = get_users('search='.$name);
	if(count($users) > 0){
		$append += 1;
		return sfhiv_group_member_make_username($name,$append);
	}
	return $name;
}

function sfhiv_group_member_make_password($length = 8) {
    # from http://stackoverflow.com/questions/6101956/generating-a-random-password-in-php 
	$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < $length; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

?>