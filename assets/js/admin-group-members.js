jQuery(document).ready(function(){

	jQuery('#sfhiv-create-member .button').click(function(event){
		event.preventDefault();
		var button = jQuery(this);
		var container = jQuery('#sfhiv-create-member');
		button.hide();
		jQuery.post(ajaxurl, {
			'action':'sfhiv_member_create',
			'first_name':jQuery('.first-name',container).val(),
			'last_name':jQuery('.last-name',container).val()
		}, function(response) {
				var data = jQuery.parseJSON(response);
				button.show();
				jQuery('input:not(.button)',container).val("");
				if(data['ID']){
					jQuery('#sfhiv-group-members').trigger({
						type:'add',
						user_id:data['ID']
					});
				}
			});
	});

});