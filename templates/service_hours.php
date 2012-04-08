
<fieldset>
	<legend>Days of Week</legend>
	<?	foreach($days_in_week as $day):	?>
	<label class="checkbox"><input type="checkbox" name="<?=$form_name;?>[day_of_week][]" value="<?=$day;?>" <?	if(in_array($day,$days)){ echo 'checked="checked"'; }	?>  /><?=$day;?></label>
	<?	endforeach;	?>
</fieldset>