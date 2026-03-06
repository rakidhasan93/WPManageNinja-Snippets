<?php
add_filter('fluent_booking/booking_data', function ($bookingData) {
    $bookingData['ip_address'] = '';   // or unset($bookingData['ip_address']);
    return $bookingData;
}, 10, 4);
