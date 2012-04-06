<?php

add_action('add_meta_boxes','sfhiv_website_link_add_all_boxes');
function sfhiv_website_link_add_all_boxes(){
	sfhiv_website_link_add_meta_box('sfhiv_report');
	sfhiv_website_link_add_meta_box('sfhiv_study');
	sfhiv_website_link_add_meta_box('sfhiv_group');
	sfhiv_website_link_add_meta_box('sfhiv_service');
	sfhiv_website_link_add_meta_box('sfhiv_event');
	sfhiv_website_link_add_meta_box('post');
	sfhiv_website_link_add_meta_box('page');
}

function sfhiv_website_link_add_meta_box($post_type){
	add_meta_box( 'sfhiv_website_link', 'Website', 'sfhiv_website_link_meta_box', $post_type);
}

function sfhiv_website_link_meta_box($post){
	$link = get_post_meta($post->ID,'sfhiv_website_link',true);
	$name = get_post_meta($post->ID,'sfhiv_website_link_name',true);
	$forward = get_post_meta($post->ID,'sfhiv_website_link_forward',true);
	?>
	<div>
		<label for="sfhiv_website_link">Website</label>
		<input type="text" id="sfhiv_website_link" name="sfhiv_website_link" value="<?=$link;?>" />
		<label class="checkbox"><input type="checkbox" name="sfhiv_website_link_forward" value="true" <? if($forward) echo 'checked="checked"';	?> />Automatically Forward</label>
	</div>
	<div>
		<label for="sfhiv_website_link_name">Name</label>
		<input type="text" id="sfhiv_website_link_name" name="sfhiv_website_link_name" value="<?=$name;?>" />
	</div>
	<?
}

add_action('save_post','sfhiv_website_link_save');
function sfhiv_website_link_save($post_ID){
	if(isset($_POST['sfhiv_website_link']) && $_POST['sfhiv_website_link'] != ""){
		update_post_meta($post_ID,'sfhiv_website_link',$_POST['sfhiv_website_link']);
		update_post_meta($post_ID,'sfhiv_website_link_name',$_POST['sfhiv_website_link_name']);
		if(isset($_POST['sfhiv_website_link_forward'])){
			update_post_meta($post_ID,'sfhiv_website_link_forward',$_POST['sfhiv_website_link_forward']);
		}else{
			delete_post_meta($post_ID,'sfhiv_website_link_forward');
		}
	}else if(isset($_POST['sfhiv_website_link'])){
		delete_post_meta($post_ID,'sfhiv_website_link');
		delete_post_meta($post_ID,'sfhiv_website_name');
		delete_post_meta($post_ID,'sfhiv_website_link_forward');
	}
}

function sfhiv_website_link_get_link($post_ID=false){
	if(!$post_ID) $post_ID = get_the_ID();
	$link = get_post_meta($post_ID,'sfhiv_website_link',true);
	if(!$link) return false;
	return (object) array(
		"link" => $link,
		"forward" => get_post_meta($post_ID,'sfhiv_website_link_forward',true),
		"name" => get_post_meta($post_ID,'sfhiv_website_link_name',true),
	);
}

add_filter('post_type_link','sfhiv_website_link_filter',2,2);
add_filter('page_link','sfhiv_website_link_filter',2,2);
function sfhiv_website_link_filter($link,$post_id){
	if(is_admin()){
		return $link;
	}
	if(is_object($post_id)){
		$post_id = $post_id->ID;
	}
	$forward = get_post_meta($post_id,'sfhiv_website_link_forward',true);
	if($forward){
		return get_post_meta($post_id,'sfhiv_website_link',true);
	}
	return $link;
}

?>