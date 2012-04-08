<fieldset>
	<legend>Days of Week</legend>
	<div><?=implode(",",$day_values);?></div>
	<?	foreach($days as $day):	?>
	<label class="checkbox"><input type="radio" name="<?=$form_name;?>[day_of_week]" value="<?=$day->slug;?>" <?	
		if(in_array($day->slug,$has_days)){ echo 'checked="checked"'; }	
			?>  /><?=$day->name;?></label>
	<?	endforeach;	?>
</fieldset>