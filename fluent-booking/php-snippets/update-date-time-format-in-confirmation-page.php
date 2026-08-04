<?php

add_filter('fluent_booking/schedule_receipt_data', function ($data, $booking) {
    if (empty($data['sections']['when'])) {
        return $data;
    }

    $timezone = $booking->person_time_zone ?: 'UTC';

    try {
        $date = new DateTime($booking->start_time, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone($timezone));

        $formatted = wp_date('d. F Y \u\m G \U\h\r', $date->getTimestamp(), new DateTimeZone($timezone));

        $data['sections']['when']['content'] = $formatted . ' (' . $timezone . ')';
    } catch (Exception $e) {
        // Keep original output if anything fails.
    }

    return $data;
}, 10, 2);

?>
