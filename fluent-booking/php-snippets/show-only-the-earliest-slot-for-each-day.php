<?php

add_filter('fluent_booking/available_slots_for_view', function ($availableSpots, $calendarEvent, $calendar, $timeZone, $duration) {
    foreach ($availableSpots as $date => $spots) {
        if (is_array($spots) && $spots) {
            $availableSpots[$date] = array_slice(array_values($spots), 0, 1);
        }
    }

    return $availableSpots;
}, 10, 5);

?>
