jQuery(document).ready(function($){
	$("#sfhiv_event_start_date").change(function(){
		var start = $(this);
		var end = $("#sfhiv_event_end_date");
		if(start.val()!="" && end.val()==""){
			end.val(start.val());
		}
	});
});