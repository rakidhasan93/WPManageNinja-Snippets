add_filter('fluent_booking/meeting_multi_durations_schema', function ($durations) {
    foreach ($durations as &$duration) {
        $minutes = (int) $duration['value'];
        if ($minutes >= 60) {
            $hours = $minutes / 60;
            $duration['label'] = sprintf(__('%d Hour%s', 'fluent-booking'), $hours, $hours > 1 ? 's' : '');
        }
    }
    return $durations;
});
