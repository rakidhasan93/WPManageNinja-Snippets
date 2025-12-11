add_filter('fluent_booking/meeting_multi_durations_schema', function ($durations) {
    $durations[] = [
        'value' => '300',
        'label' => __('300 Minutes', 'fluent-booking') // 5 hours
    ];

    $durations[] = [
        'value' => '600',
        'label' => __('600 Minutes', 'fluent-booking') // 6 hours
    ];
return $durations;
});
