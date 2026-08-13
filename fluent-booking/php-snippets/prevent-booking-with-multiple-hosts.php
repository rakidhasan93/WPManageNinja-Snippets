<?php

/**
 * Prevent an attendee (by email) from having active bookings with more than one host.
 *
 * Install: paste into a site-specific plugin (or your theme's functions.php,
 * or a snippet manager like WP Code / Code Snippets).
 *
 * Choose ONE of the two modes below by setting $onlyBlockOverlap.
 */

add_action('fluent_booking/before_creating_schedule', function ($bookingData, $postedData, $calendarEvent) {

    // true  = only block if the new time overlaps an existing booking with a different host
    // false = block ANY booking with a different host, regardless of time (one host, ever)
    
  $onlyBlockOverlap = false;

    $email     = \FluentBooking\App\Services\Helper::class ? sanitize_email($bookingData['email']) : $bookingData['email'];
    $newHostId = $bookingData['host_user_id'] ?? null;

    if (!$email || !$newHostId) {
        return;
    }

    $query = \FluentBooking\App\Models\Booking::where('email', $email)
        ->where('status', 'scheduled')
        ->where('host_user_id', '!=', $newHostId);

    if ($onlyBlockOverlap) {
        $newStart = $bookingData['start_time'];
        $duration = $bookingData['slot_minutes'] ?? 30;
        $newEnd   = gmdate('Y-m-d H:i:s', strtotime($newStart) + ($duration * 60));

        // Overlap condition: existing.start < newEnd AND existing.end > newStart
        $query->where('start_time', '<', $newEnd)
              ->whereRaw("DATE_ADD(start_time, INTERVAL slot_minutes MINUTE) > ?", [$newStart]);
    }

    $conflict = $query->exists();

    if ($conflict) {
        wp_send_json([
            'message' => __('You already have a booking with a different host. Please cancel that booking first, or choose the same host again.', 'fluent-booking')
        ], 422);
        // wp_send_json() calls wp_die() internally, so execution stops here.
    }

}, 10, 3);
