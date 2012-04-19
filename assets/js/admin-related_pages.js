jQuery(document).ready(function($){
	jQuery("#sfhiv_related_pages_search_field").keyup(function(event){
		related_field = jQuery(this);
		if(related_field.data('timeout')){
			clearTimeout(related_field.data('timeout'));
		}
		related_field.data('timeout',setTimeout(function(){
			clearTimeout(related_field.data('timeout'));
			jQuery.post(ajaxurl, {
			'action':'sfhiv_related_pages_search',
			'term':related_field.val(),
			}, function(response) {
				$("#sfhiv_related_pages_search .list").html(response);
			});
		},'500'));
	});
	
	jQuery("#related_pages").delegate('.sfhiv_related_page .add','click',function(event){
		event.preventDefault();
		jQuery(this).parents('.sfhiv_related_page:first').appendTo('#sfhiv_related_pages_current');
	}).delegate('.sfhiv_related_page .remove','click',function(event){
		event.preventDefault();
		jQuery(this).parents('.sfhiv_related_page:first').remove();
	});
	
	jQuery("form#post").submit(function(){
		jQuery("#sfhiv_related_pages_search").remove();
	});
	
	$( "#sfhiv_related_pages_current" ).sortable();
	$( "#sfhiv_related_pages_current li" ).disableSelection();
	
});