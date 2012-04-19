<ul id="sfhiv_related_pages_current" class="related_pages list">
	<?	foreach($related_items->posts as $item):	?>
	<?	include('related_page_item.php');	?>
	<?	endforeach;	?>
</ul>
<div id="sfhiv_related_pages_search">
	<fieldset>
		<label for="sfhiv_related_pages_search_field">Search for item titles</label>
		<input type="text" id="sfhiv_related_pages_search_field" name="sfhiv_related_pages_search" value="" />
	</fieldset>
	<ul class="related_pages list"></ul>
</div>