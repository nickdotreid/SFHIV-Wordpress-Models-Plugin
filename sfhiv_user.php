<?php

add_filter('user_contactmethods','sfhiv_no_contact_info');
function sfhiv_no_contact_info($contact_methods){
	unset($contact_methods['aim']);
	unset($contact_methods['yim']);
	unset($contact_methods['jabber']);
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

add_action( 'show_user_profile', 'sfhiv_user_show_bio' );
add_action( 'edit_user_profile', 'sfhiv_user_show_bio' );
function sfhiv_user_show_bio($user){
	?>
	<table class="form-table">
		<tr>
		<th></th>
		<td>
			<input type="checkbox" name="sfhiv_show_bio" id="sfhiv_show_bio" value="true" <? if(get_the_author_meta( 'sfhiv_show_bio', $user->ID )) echo "checked='checked'";	?> />
			<label for="sfhiv_show_bio">Make Bio Page View able</label>
		</td>
		</tr>
	</table>
	<?
}
add_action( 'personal_options_update', 'sfhiv_save_show_bio' );
add_action( 'edit_user_profile_update', 'sfhiv_save_show_bio' );
function sfhiv_save_show_bio( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) )
		return FALSE;
	if(isset($_POST['sfhiv_show_bio'])){
		update_usermeta( $user_id, 'sfhiv_show_bio', true );
	}else{
		delete_usermeta($user_id,'sfhiv_show_bio');
	}
}

add_action( 'show_user_profile', 'sfhiv_user_telephone' );
add_action( 'edit_user_profile', 'sfhiv_user_telephone' );
function sfhiv_user_telephone($user){
	?>
	<table class="form-table">
		<tr>
		<th><label for="sfhiv_telephone">Phone <span class="description">Use this one</span></label></th>
		<td>
			<input type="text" name="sfhiv_telephone" id="sfhiv_telephone" value="<?php echo esc_attr( get_the_author_meta( 'sfhiv_telephone', $user->ID ) ); ?>" class="regular-text" />
		</td>
		</tr>
	</table>
	<?
}
add_action( 'personal_options_update', 'sfhiv_save_user_telephone' );
add_action( 'edit_user_profile_update', 'sfhiv_save_user_telephone' );
function sfhiv_save_user_telephone( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) )
		return FALSE;
	// should clean phone number and format
	update_usermeta( $user_id, 'sfhiv_telephone', $_POST['sfhiv_telephone'] );
}

add_action( 'show_user_profile', 'sfhiv_user_suffix', 1 );
add_action( 'edit_user_profile', 'sfhiv_user_suffix', 1 );
function sfhiv_user_suffix($user){
	?>
	<table class="form-table">
		<tr>
		<th><label for="sfhiv_suffix">Suffix <span class="description">Displayed after last name</span></label></th>
		<td>
			<input type="text" name="sfhiv_suffix" id="sfhiv_suffix" value="<?php echo esc_attr( get_the_author_meta( 'sfhiv_suffix', $user->ID ) ); ?>" class="regular-text" />
		</td>
		</tr>
	</table>
	<?
}
add_action( 'personal_options_update', 'sfhiv_save_user_suffix' );
add_action( 'edit_user_profile_update', 'sfhiv_save_user_suffix' );
function sfhiv_save_user_suffix( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) )
		return FALSE;
	update_usermeta( $user_id, 'sfhiv_suffix', $_POST['sfhiv_suffix'] );
}


add_action( 'show_user_profile', 'sfhiv_user_title' );
add_action( 'edit_user_profile', 'sfhiv_user_title' );
function sfhiv_user_title($user){
	?>
	<table class="form-table">
		<tr>
		<th><label for="sfhiv_title">Title <span class="description">Default title to display</span></label></th>
		<td>
			<input type="text" name="sfhiv_title" id="sfhiv_title" value="<?php echo esc_attr( get_the_author_meta( 'sfhiv_title', $user->ID ) ); ?>" class="regular-text" />
		</td>
		</tr>
		<tr>
		<th><label for="sfhiv_department">Department <span class="description">Name of department member belongs to</span></label></th>
		<td>
			<input type="text" name="sfhiv_department" id="sfhiv_department" value="<?php echo esc_attr( get_the_author_meta( 'sfhiv_department', $user->ID ) ); ?>" class="regular-text" />
		</td>
		</tr>
	</table>
	<?
}
add_action( 'personal_options_update', 'sfhiv_save_user_title' );
add_action( 'edit_user_profile_update', 'sfhiv_save_user_title' );
function sfhiv_save_user_title( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) )
		return FALSE;
	update_usermeta( $user_id, 'sfhiv_title', $_POST['sfhiv_title'] );
	update_usermeta( $user_id, 'sfhiv_department', $_POST['sfhiv_department'] );
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