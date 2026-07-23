<?php

add_filter('fluent_booking/meeting_multi_durations_schema', function ($durations) {
    $custom = [
        [
            'value' => '15',
            'label' => __('Quick Check-in (15 Minutes)', 'fluent-booking')
        ],
        [
            'value' => '30',
            'label' => __('Standard Support Session (30 Minutes)', 'fluent-booking')
        ],
        [
            'value' => '60',
            'label' => __('Deep Support Session (60 Minutes)', 'fluent-booking')
        ],
        [
            'value' => '90',
            'label' => __('Extended Support Session (90 Minutes)', 'fluent-booking')
        ]
    ];

    return $custom;
});

?>
