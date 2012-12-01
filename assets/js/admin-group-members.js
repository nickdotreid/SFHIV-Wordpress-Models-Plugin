jQuery(document).ready(function(){



	jQuery('#sfhiv-group-members').bind('setup',function(event){
		var container = jQuery(this);
		var tempContent = jQuery(".member-template",container).html();
		tempContent = tempContent.replace(/&lt;/gi,'<').replace(/&gt;/gi,'>');
		container.data('member_template',_.template(tempContent));
		jQuery(".member-template",container).remove();
		container.trigger("update");
	}).bind('update',function(event){
		var container = jQuery(this);
		var membersContainer = jQuery('.members',container);
		jQuery.post(ajaxurl, {
			'action':'sfhiv_members_get',
			'group_id':jQuery("#post_ID").val()
		}, function(response) {
			data = jQuery.parseJSON(response);
			var template = container.data("member_template");
			if(data['members']){
				_.forEach(data['members'],function(member){
					membersContainer.append(template(member));
				});
			}
			});
	}).trigger('setup');

	jQuery('#sfhiv-group-members').delegate('.sfhiv-member .remove','click',function(event){
		event.preventDefault();
		var user = jQuery(this).parents('.sfhiv-member:first');
		if(confirm("Remove user from group")){
			jQuery.post(ajaxurl, {
				'action':'sfhiv_member_remove',
				'group_id':jQuery("#post_ID").val(),
				'user_id':user.attr('user-id')
			});
			user.remove();
		}
	});

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
				button.show();
				jQuery('input:not(.button)',container).val("");
			});
	});

});