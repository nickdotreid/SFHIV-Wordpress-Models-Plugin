<fieldset>
	<legend>Days of Week</legend>
	<?	foreach($day_terms as $day):	?>
	<label class="checkbox"><input type="checkbox" name="<?=$form_name;?>[days][]" value="<?=$day->slug;?>" <?	
		if(in_array($day->slug,$days)){ echo 'checked="checked"'; }	
			?>  /><?=$day->name;?></label>
	<?	endforeach;	?>
</fieldset>