<?php

add_filter('fluent_booking/meeting_durations_schema', function ($durations) {
    $exists = wp_list_pluck($durations, 'value');

    if (!in_array('20', $exists, true)) {
        array_splice($durations, 1, 0, [[
            'value' => '20',
            'label' => __('20 Minutes', 'fluent-booking')
        ]]);
    }

    $exists = wp_list_pluck($durations, 'value');

    if (!in_array('40', $exists, true)) {
        array_splice($durations, 3, 0, [[
            'value' => '40',
            'label' => __('40 Minutes', 'fluent-booking')
        ]]);
    }

    return $durations;
});

add_filter('fluent_booking/meeting_multi_durations_schema', function ($durations) {
    $exists = wp_list_pluck($durations, 'value');

    if (!in_array('20', $exists, true)) {
        array_splice($durations, 3, 0, [[
            'value' => '20',
            'label' => __('20 Minutes', 'fluent-booking')
        ]]);
    }

    $exists = wp_list_pluck($durations, 'value');

    if (!in_array('40', $exists, true)) {
        array_splice($durations, 5, 0, [[
            'value' => '40',
            'label' => __('40 Minutes', 'fluent-booking')
        ]]);
    }

    return $durations;
});
