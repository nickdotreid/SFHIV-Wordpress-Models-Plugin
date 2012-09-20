<?php

add_action( 'init', 'sfhiv_day_of_week_taxonomy_init' );
function sfhiv_day_of_week_taxonomy_init(){
    register_taxonomy( 'sfhiv_day_of_week_taxonomy', array('sfhiv_service','sfhiv_service_hour'),
        array(  'hierarchical' => true,
                'label' => __('Day of Week'),
                'query_var' => false,
        )
    );
}

?>