<li id="sfhiv_related-<?=$item->ID;?>" class="sfhiv_related_page">
	<input type="hidden" name="sfhiv_related[]" value="<?=$item->ID;?>" />
	<a href="#" class="add">Add <?=apply_filters('the_title',$item->post_title);?></a>
	<a href="#" class="remove">Remove <?=apply_filters('the_title',$item->post_title);?></a>
	<span class="title"><?=apply_filters('the_title',$item->post_title);?></span>
	<span class="post_type"><?=get_post_type($item->ID);?></span>
</li>