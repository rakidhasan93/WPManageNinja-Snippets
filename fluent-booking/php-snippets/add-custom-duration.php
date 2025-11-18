function custom_meeting_durations($durations) {
    // Define the new durations to be added.
    $new_durations = [
        [
            'value' => '300',
            'label' => __('300 Minutes', 'fluent-booking-pro')
        ],
        [
            'value' => '600',
            'label' => __('600 Minutes', 'fluent-booking-pro')
        ],
        [
            'value' => '720',
            'label' => __('720 Minutes', 'fluent-booking-pro')
        ]
    ];

    return $durations;
}
