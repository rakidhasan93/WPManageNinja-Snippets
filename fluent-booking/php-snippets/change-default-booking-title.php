add_filter('fluent_booking/booking_meeting_title', function($meetingTitle, $authorName, $guestName, $calendarSlot, $instance) {
    $meetingTitle = "$calendarSlot->description Appointment Between {$authorName} and {$guestName}";
    return $meetingTitle;
}, 10, 5);
