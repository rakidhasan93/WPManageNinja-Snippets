<?php

/**
 * Centrally override FluentBooking's default email subject/body text,
 * translated once, applied to every calendar that hasn't saved its own
 * per-event override.
 *
 * Install: site-specific plugin, child theme functions.php, or a snippet
 * manager (WP Code / Code Snippets), set to run everywhere.
 */

add_filter('fluent_booking/default_email_notification_settings', function ($defaults) {

    // Only touch the keys you want to change - everything else stays as-is.

    $defaults['booking_conf_attendee']['email']['subject'] =
        __('Your booking is confirmed: {{host.name}} & {{guest.full_name}}', 'your-textdomain');

    $defaults['booking_conf_attendee']['email']['body'] = str_replace(
        'Your event has been scheduled',
        __('Uw afspraak is bevestigd', 'your-textdomain'), // example: Dutch translation
        $defaults['booking_conf_attendee']['email']['body']
    );

    $defaults['booking_conf_host']['email']['subject'] =
        __('New booking: {{guest.full_name}} @ {{booking.start_date_time_for_host}}', 'your-textdomain');

    // ...repeat for any of: reminder_to_attendee, reminder_to_host,
    // cancelled_by_attendee, cancelled_by_host, rescheduled_by_attendee,
    // rescheduled_by_host, booking_request_host, booking_request_attendee,
    // declined_by_host

    return $defaults;
});
