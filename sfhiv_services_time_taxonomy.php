<?php

add_action( 'init', 'sfhiv_time_of_day_taxonomy_init' );
function sfhiv_time_of_day_taxonomy_init()
{
    register_taxonomy( 'sfhiv_time_of_day_taxonomy', array('sfhiv_service_hour'),
        array(  'hierarchical' => true,
                'label' => __('Time of Day'),
                'query_var' => false
        )
    );
}

add_action( 'sfhiv_time_of_day_taxonomy_edit_form_fields', 'sfhiv_time_of_day_taxonomy_edit', 10, 2);
function sfhiv_time_of_day_taxonomy_edit($tag, $taxonomy){
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
		$start_value = sfhiv_service_hours_string_to_time($_POST['sfhiv_time_of_day_start']);
		update_option($name, $start_value);
	if (isset($_POST['sfhiv_time_of_day_end'])){
		$name = 'sfhiv_time_of_day_end_'.$term_id;
		$end_value = sfhiv_service_hours_string_to_time($_POST['sfhiv_time_of_day_end']);
		update_option($name, $end_value);
	}
	
	sfhiv_service_time_of_day_update_all();
}

function sfhiv_service_time_of_day_update_all(){
	$terms = sfhiv_service_time_of_day_get_terms();
	$query = new WP_Query(array(
		'post_type' => 'sfhiv_service_hour',
		'nopaging' => true,
		));
	foreach($query->posts as $post){
		sfhiv_service_time_of_day_update($post,$terms);
	}
}

function sfhiv_service_time_of_day_get_terms(){
	$all_terms = get_terms('sfhiv_time_of_day_taxonomy',array('hide_empty'=>false));
	$terms = array();
	foreach($all_terms as $tag){
		$terms[] = (object) array(
			'ID' => $tag->term_id,
			'slug' => $tag->slug,
			'start' => get_option('sfhiv_time_of_day_start_'.$tag->term_id),
			'end' => get_option('sfhiv_time_of_day_end_'.$tag->term_id),
		);
	}
	return $terms;
}

function sfhiv_service_time_of_day_update($post,$terms){
	$start_time = get_post_meta($post->ID,'sfhiv_service_start',true);
	$end_time = get_post_meta($post->ID,'sfhiv_service_end',true);
	
	$in_terms = array();
	foreach($terms as $tag){
		if(($start_time > $tag->start && $start_time < $tag->end)
			||($end_time > $tag->start && $end_time < $tag->end)){
			$in_terms[] = $tag->slug;
		}
	}
	wp_set_object_terms( $post->ID, $in_terms, 'sfhiv_time_of_day_taxonomy');
}

add_action( 'save_post', 'sfhiv_service_time_of_day_save_post_update' );
function sfhiv_service_time_of_day_save_post_update($post_ID){
	if(get_post_type($post_ID) != 'sfhiv_service_hour') return;
	$terms = sfhiv_service_time_of_day_get_terms();
	$post = get_post($post_ID);
	sfhiv_service_time_of_day_update($post,$terms);
}

?>