<?php

add_filter('fluent_booking/booking_data', function ($bookingData, $calendarSlot, $customFieldsData, $rawData) {
    if (empty($bookingData['email']) || empty($bookingData['event_id'])) {
        return $bookingData;
    }

    $exists = \FluentBooking\App\Models\Booking::query()
        ->where('event_id', $bookingData['event_id'])
        ->where('email', $bookingData['email'])
        ->whereIn('status', ['scheduled', 'completed', 'pending', 'reserved'])
        ->exists();

    if ($exists) {
        return new \WP_Error(
            'duplicate_booking',
            __('This email has already booked this event.', 'fluent-booking')
        );
    }

    return $bookingData;
}, 10, 4);
