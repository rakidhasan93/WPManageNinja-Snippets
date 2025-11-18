<?php
add_filter('fluent_booking/buffer_times_schema', function ($times) {
    $times[] = [
        'value' => '40',
        'label' => __('40 Minutes', 'fluent-booking')
    ];
    return $times;
}); 
