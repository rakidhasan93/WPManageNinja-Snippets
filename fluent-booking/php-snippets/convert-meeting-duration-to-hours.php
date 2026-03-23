<?php

add_filter('fluent_booking/meeting_multi_durations_schema', function ($durations) {
    foreach ($durations as &$duration) {
        $minutes = (int) $duration['value'];

        if ($minutes >= 60) {
            $hours = intdiv($minutes, 60);
            $remaining_minutes = $minutes % 60;

            $parts = [];

            if ($hours > 0) {
                $parts[] = sprintf(
                    _n('%d hour', '%d hours', $hours, 'fluent-booking'),
                    $hours
                );
            }

            if ($remaining_minutes > 0) {
                $parts[] = sprintf(
                    _n('%d minute', '%d minutes', $remaining_minutes, 'fluent-booking'),
                    $remaining_minutes
                );
            }

            $duration['label'] = implode(' ', $parts);
        }
    }

    return $durations;
});
