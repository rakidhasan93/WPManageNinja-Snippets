add_filter('fluent_booking/meeting_multi_durations_schema', function ($durations) {
    $durations[] = [
        'value' => '20',
        'label' => __('20 Minutes', 'fluent-booking') // 5 hours
    ];

return $durations;

});
