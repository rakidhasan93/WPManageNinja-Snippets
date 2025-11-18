add_filter('fluent_booking/slot_intervals_schema', function ($intervals) {
    $intervals[] = [
        'value' => '40',
        'label' => __('40 Minutes', 'fluent-booking')
    ];
    return $intervals;
}); 
