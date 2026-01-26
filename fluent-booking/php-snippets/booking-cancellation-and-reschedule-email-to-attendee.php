<?php
use FluentBooking\App\Services\EmailNotificationService;
use FluentBooking\Framework\Support\Arr;

// Send email to attendee when they cancel
add_action('fluent_booking/booking_schedule_cancelled', function($booking, $calendarEvent) {
    $cancelledBy = $booking->getMeta('cancelled_by_type', 'host');
    
    if ($cancelledBy !== 'guest') {
        return; // Only for attendee cancellations
    }
    
    $notifications = $calendarEvent->getNotifications();
    if (Arr::isTrue($notifications, 'cancelled_by_host.enabled')) {
        $email = Arr::get($notifications, 'cancelled_by_host.email', []);
        EmailNotificationService::bookingCancelOrRejectEmail($booking, $email, 'guest', 'cancel');
    }
}, 20, 2);

// Send email to attendee when they reschedule
add_action('fluent_booking/after_booking_rescheduled', function($booking, $oldBooking, $calendarEvent) {
    $rescheduledBy = $booking->getMeta('rescheduled_by_type', 'host');
    
    if ($rescheduledBy !== 'guest') {
        return; // Only for attendee reschedules
    }
    
    $notifications = $calendarEvent->getNotifications();
    if (Arr::isTrue($notifications, 'rescheduled_by_host.enabled')) {
        $email = Arr::get($notifications, 'rescheduled_by_host.email', []);
        EmailNotificationService::bookingRescheduledEmail($booking, $email, 'guest');
    }
}, 20, 3);
