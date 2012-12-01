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
				container.trigger('sort');
			}
			});
	}).bind('sort',function(event){
		var container = jQuery(this);
		var membersContainer = jQuery('.members',container);

		var members = [];
		jQuery('.sfhiv-member',membersContainer).each(function(){
			member = jQuery(this);
			members.push({
				firstName:member.attr("first-name"),
				lastName:member.attr("last-name"),
				weight:jQuery('.weight',member).val(),
				div:member
			});
		});

		members = members.sort(function(a,b){
			if(a['lastName'] > b['lastName']){
				return 1;
			}else if(b['lastName'] > a['lastName']){
				return -1;
			}
			if(a['firstName'] > b['firstName']){
				return 1;
			}else if(b['firstName'] > a['firstName']){
				return -1;
			}
			if(a['weight'] > b['weight']){
				return 1;
			}else if(b['weight'] > a['weight']){
				return -1;
			}
			return 0;
		});

		_.forEach(members,function(member){
			membersContainer.append(member['div']);
		});

	}).bind('add',function(event){
		if(!event.user_id) return;

		var container = jQuery(this);
		var membersContainer = jQuery('.members',container);

		jQuery.post(ajaxurl, {
			'action':'sfhiv_member_add',
			'group_id':jQuery("#post_ID").val(),
			'user_id':event.user_id
		}, function(response) {
			data = jQuery.parseJSON(response);
			var template = container.data("member_template");
			if(data['members']){
				_.forEach(data['members'],function(member){
					membersContainer.append(template(member));
				});
				container.trigger('sort');
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