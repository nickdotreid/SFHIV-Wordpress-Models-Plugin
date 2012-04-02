<?php

add_action('add_meta_boxes','sfhiv_website_link_add_all_boxes');
function sfhiv_website_link_add_all_boxes(){
	sfhiv_website_link_add_meta_box('sfhiv_group');
	sfhiv_website_link_add_meta_box('sfhiv_event');
	sfhiv_website_link_add_meta_box('page');
}

function sfhiv_website_link_add_meta_box($post_type){
	add_meta_box( 'sfhiv_website_link', 'Website', 'sfhiv_website_link_meta_box', $post_type);
}

function sfhiv_website_link_meta_box($post){
	$link = get_post_meta($post->ID,'sfhiv_website_link',true);
	$forward = get_post_meta($post->ID,'sfhiv_website_link_forward',true);
	?>
	<label for="sfhiv_website_link">Website</label>
	<input type="text" id="sfhiv_website_link" name="sfhiv_website_link" value="<?=$link;?>" />
	<label class="checkbox"><input type="checkbox" name="sfhiv_website_link_forward" value="true" <? if($forward) echo 'checked="checked"';	?> />Automatically Forward</label>
	<?
}

add_action('save_post','sfhiv_website_link_save');
function sfhiv_website_link_save($post_ID){
	if(isset($_POST['sfhiv_website_link']) && $_POST['sfhiv_website_link'] != ""){
		update_post_meta($post_ID,'sfhiv_website_link',$_POST['sfhiv_website_link']);
		if(isset($_POST['sfhiv_website_link_forward'])){
			update_post_meta($post_ID,'sfhiv_website_link_forward',$_POST['sfhiv_website_link_forward']);
		}else{
			delete_post_meta($post_ID,'sfhiv_website_link_forward');
		}
	}else if(isset($_POST['sfhiv_website_link'])){
		delete_post_meta($post_ID,'sfhiv_website_link');
		delete_post_meta($post_ID,'sfhiv_website_link_forward');
	}
}

?>