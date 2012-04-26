jQuery(document).ready(function($){
	$("#new_sfhiv_service_hour").click(function(event){
		event.preventDefault();
		var button = $(this);
		$.post(ajaxurl, {
			'action':'sfhiv_service_hour_form'
		}, function(response) {
				button.before(response);
			});
	});
	$('form').submit(function(){
		$(".service_hour",$(this)).each(function(index){
			$("input,select,textarea",$(this)).each(function(){
				this.name = this.name.replace(/position/gi,index);
			});
		});
	})
});