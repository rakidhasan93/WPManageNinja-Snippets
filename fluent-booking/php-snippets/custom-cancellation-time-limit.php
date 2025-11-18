add_filter('fluent_booking/get_calendar_event_settings', function($settings) {
    // Override the default cancellation settings
    $settings['can_not_cancel'] = [
        'enabled'   => true,
        'message'   => 'Sorry! you can not cancel this booking within 48 hours of the meeting',
        'type'      => 'always', // This prevents ALL cancellations
        'condition' => [
            'unit'  => 'hours',
            'value' => 48
        ]
    ];
    
    return $settings;
});
