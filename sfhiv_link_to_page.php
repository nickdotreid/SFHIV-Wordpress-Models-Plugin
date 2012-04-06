<?php

add_action('add_meta_boxes','sfhiv_link_to_page_add_all_boxes');
function sfhiv_link_to_page_add_all_boxes(){
	sfhiv_link_to_page_add_meta_box('sfhiv_group');
	sfhiv_link_to_page_add_meta_box('sfhiv_event');
	sfhiv_link_to_page_add_meta_box('sfhiv_document');
	sfhiv_link_to_page_add_meta_box('sfhiv_report');
	sfhiv_link_to_page_add_meta_box('sfhiv_service');
}

function sfhiv_link_to_page_add_meta_box($post_type){
	add_meta_box( 'sfhiv_link_to_page', 'Link to Page', 'sfhiv_link_to_page_meta_box', $post_type,'side','high');
}

function sfhiv_link_to_page_meta_box($post){
	$page_id = get_post_meta($post->ID,'sfhiv_link_to_page',true);
	$active = get_post_meta($post->ID,'sfhiv_link_to_page_active',true);
	$pages = get_pages(array(
		'nopaging' => true,
	));
	
	console(sfhiv_link_to_page_get_page($post->ID));
	
	?>
	<p>
	<label class="checkbox"><input type="checkbox" name="sfhiv_link_to_page_active" value="true" <? if($active) echo 'checked="checked"';	?> />Link to Page</label>
	</p>
	<label for="sfhiv_link_to_page">Page</label>
	<select id="sfhiv_link_to_page" name="sfhiv_link_to_page">
		<?	foreach($pages as $page):	?>
		<option value="<?=$page->ID;?>" <?
			if($page->ID == $page_id) echo 'selected="true"';
		?>><?=apply_filters('the_title',$page->post_title);?></option>
		<?	endforeach;	?>
	</select>
	<?
}

add_action('save_post','sfhiv_link_to_page_save');
function sfhiv_link_to_page_save($post_ID){
	if(isset($_POST['sfhiv_link_to_page_active']) && $_POST['sfhiv_link_to_page_active'] != ""){
		update_post_meta($post_ID,'sfhiv_link_to_page_active',$_POST['sfhiv_link_to_page_active']);
		if(isset($_POST['sfhiv_link_to_page'])){
			update_post_meta($post_ID,'sfhiv_link_to_page',$_POST['sfhiv_link_to_page']);
		}else{
			delete_post_meta($post_ID,'sfhiv_link_to_page');
		}
	}else if(isset($_POST['sfhiv_website_link'])){
		delete_post_meta($post_ID,'sfhiv_link_to_page_active');
	}
}

function sfhiv_link_to_page_get_page($post_ID){
	$page_id = get_post_meta($post_ID,'sfhiv_link_to_page',true);
	if(!$page_id) return false;
	return get_post($page_id);
}


?>