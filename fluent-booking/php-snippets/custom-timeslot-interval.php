add_filter('fluent_booking/slot_intervals_schema', function ($intervals) {
    $intervals[] = [
        'value' => '600',
        'label' => __('600 Minutes', 'fluent-booking')
    ];
    return $intervals;
}); 
