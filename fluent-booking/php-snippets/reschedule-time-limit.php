add_filter('fluent_booking/get_calendar_event_settings', function($settings) {
    // Override the default reschdule settings
    $settings['can_not_reschedule'] = [
        'enabled'   => true,
        'message'   => 'Sorry! you can not reschedule this booking within 48 hours of the meeting',
        'type'      => 'always', // This prevents ALL reschedule
        'condition' => [
            'unit'  => 'hours',
            'value' => 48
        ]
    ];
    
    return $settings;
});
