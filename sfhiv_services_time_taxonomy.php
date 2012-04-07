<?php

add_action( 'init', 'sfhiv_time_of_day_taxonomy_init' );
function sfhiv_time_of_day_taxonomy_init()
{
    register_taxonomy( 'sfhiv_time_of_day_taxonomy', array('sfhiv_event','sfhiv_service_hours'),
        array(  'hierarchical' => false,
                'label' => __('Time of Day'),
                'query_var' => false
        )
    );
}

add_action( 'sfhiv_time_of_day_taxonomy_edit_form_fields', 'sfhiv_time_of_day_taxonomy_edit', 10, 2);
function sfhiv_time_of_day_taxonomy_edit($tag, $taxonomy){
	echo $tag->term_id;
	$period_start = get_option('sfhiv_time_of_day_start_'.$tag->term_id);
	$start = date('g:i a',$period_start);
	$period_end = get_option('sfhiv_time_of_day_end_'.$tag->term_id);
	$end = date('g:i a',$period_end);

    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="sfhiv_time_of_day_start">Start Time</label></th>
        <td>
			<input type="text" id="sfhiv_time_of_day_start" name="sfhiv_time_of_day_start" value="<?=$start;?>" />
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="sfhiv_time_of_day_end">End Time</label></th>
        <td>
			<input type="text" id="sfhiv_time_of_day_end" name="sfhiv_time_of_day_end" value="<?=$end;?>" />
        </td>
    </tr>
    <?php
}

add_action( 'edited_sfhiv_time_of_day_taxonomy', 'sfhiv_time_of_day_taxonomy_save', 10, 2);
function sfhiv_time_of_day_taxonomy_save($term_id, $tt_id){
    if (!$term_id) return;
	
	if (isset($_POST['sfhiv_time_of_day_start']))
		$name = 'sfhiv_time_of_day_start_'.$term_id;
		$value = sfhiv_service_hours_string_to_time($_POST['sfhiv_time_of_day_start']);
		update_option($name, $value);
	if (isset($_POST['sfhiv_time_of_day_end'])){
		$name = 'sfhiv_time_of_day_end_'.$term_id;
		$value = sfhiv_service_hours_string_to_time($_POST['sfhiv_time_of_day_end']);
		update_option($name, $value);
	}
}


?>