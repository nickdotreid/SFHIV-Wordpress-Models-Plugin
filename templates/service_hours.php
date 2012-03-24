<fieldset>
	<legend>Hours of Operation</legend>
	<fieldset>
		<legend>Days of Week</legend>
		<?	foreach($days_in_week as $day):	?>
		<label class="checkbox"><input type="checkbox" name="<?=$form_name;?>[day_of_week][]" value="<?=$day;?>" <?	if(in_array($day,$days)){ echo 'checked="checked"'; }	?>  /><?=$day;?></label>
		<?	endforeach;	?>
	</fieldset>
	<fieldset>
		<legend>Time</legend>
		<label for="hours_start">Start Time</label>
		<input id="hours_start" type="text" name="<?=$form_name;?>[start]" value="<?=$start;?>" />
		<label for="hours_end">End Time</label>
		<input id="hours_end" type="text" name="<?=$form_name;?>[end]" value="<?=$end;?>" />
	</fieldset>
</fieldset>
