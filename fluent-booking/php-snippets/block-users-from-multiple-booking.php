add_action('fluent_booking/before_booking', function ($bookingData, $calendarEvent) {
    $email = sanitize_email($bookingData['email']);

    // Only apply for Calendar ID = 1, Event ID = 2
    if ((int) $calendarEvent->calendar_id === 1 && (int) $calendarEvent->id === 2) {

        $alreadyBooked = \FluentBooking\App\Models\Booking::where('calendar_id', 1)
            ->where('event_id', 2)
            ->where('email', $email)   // direct email check
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($alreadyBooked) {
            throw new \Exception(__('You have already booked this event.', 'fluent-booking'));
        }
    }
}, 10, 2);
