<?php

add_filter('user_contactmethods','sfhiv_no_contact_info');
function sfhiv_no_contact_info($contact_methods){
	unset($contact_methods['aim']);
	unset($contact_methods['yim']);
	unset($contact_methods['jabber']);
	
	$contact_methods['sfhiv_suffix'] = 'Suffix';
	$contact_methods['sfhiv_telephone'] = 'Telephone';
	$contact_methods['sfhiv_website'] = 'Profile URL';
	
	$contact_methods['sfhiv_title'] = 'Title';
	$contact_methods['sfhiv_department'] = 'Department';
	
	return $contact_methods;
}

function sfhiv_author_page_get_user(){
	global $wp_query;
	if(!is_author()) return false;
	$author_id = $wp_query->query_vars['author'];
	return get_userdata(intval($author_id));
}

function add_edit_user_admin_bar_link() {
	global $wp_admin_bar,$author,$wp_query;
	if(!is_author()) return;
	$user = sfhiv_author_page_get_user();
	if(!$user || !is_user_logged_in() || 
			!(current_user_can( 'edit_users' ) || get_current_user_id()==$user->ID))
			return;
	if ( !is_admin_bar_showing() )
		return;
	$wp_admin_bar->add_menu( array(
	'id' => 'edit_author_link',
	'title' => __( 'Edit Profile'),
	'href' => __("/wp-admin/user-edit.php?user_id=".$user->ID),
	) );
}
add_action('admin_bar_menu', 'add_edit_user_admin_bar_link',125);

add_action( 'show_user_profile', 'sfhiv_user_show_contact_info' );
add_action( 'edit_user_profile', 'sfhiv_user_show_contact_info' );
function sfhiv_user_show_contact_info($user){
	?>
	<table class="form-table">
		<tr>
		<th></th>
		<td>
			<input type="checkbox" name="sfhiv_show_contact_info" id="sfhiv_show_contact_info" value="true" <? if(get_the_author_meta( 'sfhiv_show_contact_info', $user->ID )) echo "checked='checked'";	?> />
			<label for="sfhiv_show_contact_info">Show contact information</label>
		</td>
		</tr>
	</table>
	<?
}
add_action( 'personal_options_update', 'sfhiv_save_show_contact_info' );
add_action( 'edit_user_profile_update', 'sfhiv_save_show_contact_info' );
function sfhiv_save_show_contact_info( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) )
		return FALSE;
	if(isset($_POST['sfhiv_show_contact_info'])){
		update_usermeta( $user_id, 'sfhiv_show_contact_info', true );
	}else{
		delete_usermeta($user_id,'sfhiv_show_contact_info');
	}
}


function sfhiv_users_sort(&$users){
	$users = apply_filters('sfhiv_users_sort',$users);
}

add_filter('sfhiv_users_sort','sfhiv_users_sort_by_name',1);
function sfhiv_users_sort_by_name($users){
	usort($users,sfhiv_users_sort_by_name_cmp);
	return $users;
}

function sfhiv_users_sort_by_name_cmp($a,$b){
	if(get_the_author_meta('user_lastname',$a->ID) > get_the_author_meta('user_lastname',$b->ID)){
		return 1;
	}
	if(get_the_author_meta('user_lastname',$a->ID) < get_the_author_meta('user_lastname',$b->ID)){
		return -1;
	}
	if(get_the_author_meta('user_firstname',$a->ID) > get_the_author_meta('user_firstname',$b->ID)){
		return 1;
	}
	if(get_the_author_meta('user_firstname',$a->ID) < get_the_author_meta('user_firstname',$b->ID)){
		return -1;
	}
	return 0;
}

?>