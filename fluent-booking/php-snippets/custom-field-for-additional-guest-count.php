<?php

add_filter('fluent_booking/booking_data', function ($bookingData, $calendarSlot, $customFieldsData, $rawData) {
    if (!$calendarSlot->isMultiGuestEvent()) {
        return $bookingData;
    }

    $countField = 'custom_additional_guest_count';
    $extraGuests = max(0, (int) ($customFieldsData[$countField] ?? 0));

    if (!$extraGuests) {
        return $bookingData;
    }

    $maxBooking = (int) $calendarSlot->getMaxBookingPerSlot();

    if ($maxBooking > 0) {
        $alreadyBooked = \FluentBooking\App\Models\Booking::query()
            ->where('event_id', $calendarSlot->id)
            ->where('start_time', $bookingData['start_time'])
            ->whereIn('status', ['scheduled', 'completed', 'pending', 'reserved'])
            ->count();

        $requestedTotal = 1 + $extraGuests;

        if (($alreadyBooked + $requestedTotal) > $maxBooking) {
            $remaining = max(0, $maxBooking - $alreadyBooked);

            return new \WP_Error(
                'guest_count_exceeded',
                sprintf(
                    __('Only %d seat(s) are available for this time slot.', 'fluent-booking'),
                    $remaining
                )
            );
        }
    }

    $primaryEmail = sanitize_email($bookingData['email'] ?? '');
    $primaryName  = trim(($bookingData['first_name'] ?? '') . ' ' . ($bookingData['last_name'] ?? ''));

    if (!$primaryName && !empty($bookingData['name'])) {
        $primaryName = trim($bookingData['name']);
    }

    if (!$primaryName) {
        $primaryName = 'Guest 1';
    }

    $emails = [$primaryEmail];
    $names  = [$primaryName];

    $seed = substr(md5($calendarSlot->id . '|' . $bookingData['start_time'] . '|' . $primaryEmail), 0, 8);

    for ($i = 1; $i <= $extraGuests; $i++) {
        $emails[] = sprintf('fb-seat-%d-%s-%d@example.invalid', $calendarSlot->id, $seed, $i);
        $names[]  = 'Additional Guest ' . ($i + 1);
    }

    $bookingData['email'] = $emails;
    $bookingData['first_name'] = $names;
    $bookingData['last_name'] = '';

    return $bookingData;
}, 10, 4);
?>
