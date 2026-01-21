<?php
/**
 * Remove Attendees from Apple Calendar Events
 * 
 * This code prevents FluentBooking Pro from populating the "Attendee" field
 * when syncing events to Apple Calendar.
 * 
 * Add this code to your theme's functions.php file or a custom plugin.
 * 
 * IMPORTANT: This must be loaded AFTER FluentBooking Pro plugin loads.
 * If using in functions.php, ensure it's added after plugin initialization.
 */

// Use a static flag to prevent double execution
static $apple_calendar_handlers_initialized = false;

// Initialize handlers only once
if (!$apple_calendar_handlers_initialized) {
    $apple_calendar_handlers_initialized = true;
    
    // Hook early to intercept Apple Calendar event creation
    add_action('fluent_booking/create_remote_calendar_event_apple_calendar', function($config, $booking) {
        // Use a static flag per hook to prevent double execution
        static $handled = false;
        if ($handled) {
            return;
        }
        $handled = true;
        
        // Remove all other handlers (this won't affect the current execution)
        $hook_name = 'fluent_booking/create_remote_calendar_event_apple_calendar';
        global $wp_filter;
        if (isset($wp_filter[$hook_name])) {
            // Remove all callbacks except ours (priority 1)
            foreach ($wp_filter[$hook_name]->callbacks as $priority => $callbacks) {
                if ($priority > 1) {
                    unset($wp_filter[$hook_name]->callbacks[$priority]);
                }
            }
        }
        
        // Get the Bootstrap instance and call our custom version
        $bootstrap = new \FluentBookingPro\App\Services\Integrations\Calendars\AppleCalendar\Bootstrap();
        apple_calendar_create_event_without_attendees($config, $booking, $bootstrap);
        
        // Reset flag after processing
        $handled = false;
        
    }, 1, 2); // Priority 1 to run first

    // Hook into event updates (patchEvent)
    add_action('fluent_booking/patch_remote_calendar_event_apple_calendar', function($config, $booking, $updateData, $isRescheduling) {
        static $handled = false;
        if ($handled) {
            return;
        }
        $handled = true;
        
        $hook_name = 'fluent_booking/patch_remote_calendar_event_apple_calendar';
        global $wp_filter;
        if (isset($wp_filter[$hook_name])) {
            foreach ($wp_filter[$hook_name]->callbacks as $priority => $callbacks) {
                if ($priority > 1) {
                    unset($wp_filter[$hook_name]->callbacks[$priority]);
                }
            }
        }
        
        $bootstrap = new \FluentBookingPro\App\Services\Integrations\Calendars\AppleCalendar\Bootstrap();
        apple_calendar_patch_event_without_attendees($config, $booking, $updateData, $isRescheduling, $bootstrap);
        
        $handled = false;
        
    }, 1, 4);

    // Hook into group member updates
    add_action('fluent_booking/refresh_remote_calendar_group_members_apple_calendar', function($config, $booking, $allGroupBookings, $isRescheduling) {
        static $handled = false;
        if ($handled) {
            return;
        }
        $handled = true;
        
        $hook_name = 'fluent_booking/refresh_remote_calendar_group_members_apple_calendar';
        global $wp_filter;
        if (isset($wp_filter[$hook_name])) {
            foreach ($wp_filter[$hook_name]->callbacks as $priority => $callbacks) {
                if ($priority > 1) {
                    unset($wp_filter[$hook_name]->callbacks[$priority]);
                }
            }
        }
        
        $bootstrap = new \FluentBookingPro\App\Services\Integrations\Calendars\AppleCalendar\Bootstrap();
        apple_calendar_update_group_members_without_attendees($config, $booking, $allGroupBookings, $isRescheduling, $bootstrap);
        
        $handled = false;
        
    }, 1, 4);
}

/**
 * Custom createEvent method that removes attendees
 */
function apple_calendar_create_event_without_attendees($config, $booking, $bootstrap) {
    if (!$bootstrap->isConfigured() || $booking->status != 'scheduled') {
        return false;
    }

    if ($booking->getMeta('__apple_calendar_event') && (!$booking->isMultiGuestBooking() && !$booking->isRoundRobinBooking())) {
        return false; // Already created
    }

    $client = apple_calendar_get_client_from_booking_config($config, $booking, $bootstrap);

    if (!$client) {
        return false;
    }

    $data = apple_calendar_prepare_event_data_without_attendees($config, $booking, $bootstrap);

    if (!$data) {
        return false;
    }

    try {
        $apiCalendar = new \FluentBooking\Package\CalDav\Entities\Calendar([
            'href' => $config['remote_calendar_id']
        ], $client->getClient());

        $event = $apiCalendar->createEvent();
        foreach ($data as $key => $datum) {
            $event->{$key} = $datum;
        }

        $event->save();

        $booking->updateMeta('__apple_calendar_event', [
            'remote_event_id' => $event->uid,
            'remote_calendar' => $config['remote_calendar_id']
        ]);

        do_action('fluent_booking/log_booking_activity', [
            'booking_id'  => $booking->id,
            'status'      => 'closed',
            'type'        => 'success',
            'title'       => __('Apple Calendar event created', 'fluent-booking-pro'),
            'description' => sprintf(__('Aplle calendar event has been created. EventID: %s', 'fluent-booking-pro'), $event->uid)
        ]);

    } catch (\Exception $exception) {
        do_action('fluent_booking/log_booking_activity', [
            'booking_id'  => $booking->id,
            'status'      => 'closed',
            'type'        => 'error',
            'title'       => __('Apple Calendar API Error', 'fluent-booking-pro'),
            'description' => sprintf(__('Failed to create event in Apple calendar. API Response: %s', 'fluent-booking-pro'), $exception->getMessage())
        ]);
    }
}

/**
 * Custom patchEvent method that removes attendees
 */
function apple_calendar_patch_event_without_attendees($config, $booking, $updateData, $isRescheduling, $bootstrap) {
    $appleEvent = $booking->getMeta('__apple_calendar_event');

    if (!$appleEvent) {
        return false;
    }
    
    $client = apple_calendar_get_client_from_booking_config($config, $booking, $bootstrap);

    if (!$client) {
        return false;
    }

    try {
        $apiCalendar = new \FluentBooking\Package\CalDav\Entities\Calendar([
            'href' => \FluentBooking\Framework\Support\Arr::get($appleEvent, 'remote_calendar')
        ], $client->getClient());

        $apiEvent = $apiCalendar->getEvent(\FluentBooking\Framework\Support\Arr::get($appleEvent, 'remote_event_id'));

        $eventData = apple_calendar_prepare_event_data_without_attendees($config, $booking, $bootstrap);

        if (!$eventData) {
            return false;
        }

        // Ensure attendees are removed
        unset($eventData['attendees']);

        $eventData['description'] = $booking->getIcsBookingDescription();

        foreach ($eventData as $key => $datum) {
            $apiEvent->{$key} = $datum;
        }
        $apiEvent->save();

        do_action('fluent_booking/log_booking_activity', [
            'booking_id'  => $booking->id,
            'status'      => 'closed',
            'type'        => 'success',
            'title'       => __('Apple event has been updated', 'fluent-booking-pro'),
            'description' => __('Apple calendar event has been updated', 'fluent-booking-pro')
        ]);
    } catch (\Exception $exception) {
        do_action('fluent_booking/log_booking_activity', [
            'booking_id'  => $booking->id,
            'status'      => 'closed',
            'type'        => 'error',
            'title'       => __('Apple Calendar API Error', 'fluent-booking-pro'),
            'description' => sprintf(__('Failed to update event in Apple calendar. API Response: %s', 'fluent-booking-pro'), $exception->getMessage())
        ]);
        return false;
    }

    return true;
}

/**
 * Custom maybeAddOrRemoveGroupMembers method that removes attendees
 */
function apple_calendar_update_group_members_without_attendees($config, $booking, $allGroupBookings, $isRescheduling, $bootstrap) {
    $parentMeta = $parentBooking = null;

    $missingEventBookings = [];

    foreach ($allGroupBookings as $groupBooking) {
        $meta = $groupBooking->getMeta('__apple_calendar_event', []);
        if (!$meta) {
            $missingEventBookings[] = $groupBooking;
        } else if (!$parentMeta) {
            $parentMeta = $meta;
            $parentBooking = $groupBooking;
        }
    }

    if (!$parentMeta || empty($parentMeta['remote_event_id'])) {
        return apple_calendar_create_event_without_attendees($config, $booking, $bootstrap);
    }

    $parentEventId = $parentMeta['remote_event_id'];
    $parentCalendarId = $parentMeta['remote_calendar'];

    $client = apple_calendar_get_client_from_booking_config($config, $booking, $bootstrap);

    if (!$client) {
        return false;
    }

    try {
        $apiCalendar = new \FluentBooking\Package\CalDav\Entities\Calendar(['href' => $parentCalendarId], $client->getClient());
        $apiEvent = $apiCalendar->getEvent($parentEventId);
        $eventData = apple_calendar_prepare_event_data_without_attendees($config, $parentBooking, $bootstrap);
        
        // Remove attendees from group events
        unset($eventData['attendees']);
        
        $eventData['description'] = __('This is a group event.', 'fluent-booking-pro');
        foreach ($eventData as $key => $datum) {
            $apiEvent->{$key} = $datum;
        }
        $apiEvent->save();
    } catch (\Exception $exception) {
        do_action('fluent_booking/log_booking_activity', [
            'booking_id'  => $booking->id,
            'status'      => 'closed',
            'type'        => 'error',
            'title'       => __('Apple Calendar API Error', 'fluent-booking-pro'),
            'description' => sprintf(__('Failed to update event in Apple calendar. API Response: %s', 'fluent-booking-pro'), $exception->getMessage())
        ]);
    }

    foreach ($missingEventBookings as $missingBooking) {
        if ($missingBooking->status != 'cancelled') {
            $missingBooking->updateMeta('__apple_calendar_event', $parentMeta);
        }
    }
}

/**
 * Helper function to prepare event data without attendees
 * This is a modified version of the Bootstrap::prepareEventData method
 */
function apple_calendar_prepare_event_data_without_attendees($config, $booking, $bootstrap) {
    $meta = \FluentBooking\App\Models\Meta::where('object_type', '_apple_calendar_user_token')
        ->where('object_id', $booking->host_user_id)
        ->where('id', $config['db_id'])
        ->first();

    if (!$meta) {
        return;
    }

    $host = $booking->getHostDetails(false);

    $calendarOwnerEmail = $meta->key;
    $calendarOwnerName  = $host['name'];

    if ($user = get_user_by('email', $calendarOwnerEmail)) {
        $calendarOwnerName = trim($user->first_name . ' ' . $user->last_name) ?: $user->display_name;
    }

    // Prepare event data WITHOUT attendees
    $data = [
        'dtstart'     => gmdate('Y-m-d\TH:i:s\Z', strtotime($booking->start_time)),
        'dtend'       => gmdate('Y-m-d\TH:i:s\Z', strtotime($booking->end_time)),
        'status'      => 'confirmed',
        'summary'     => $booking->getBookingTitle(),
        'location'    => $booking->getLocationAsText(),
        'description' => $booking->getIcsBookingDescription(),
        // 'attendees' is intentionally NOT included
        'organizer'   => [
            'email'   => $calendarOwnerEmail,
            'name'    => $calendarOwnerName
        ]
    ];

    if ($booking->isMultiGuestBooking()) {
        $data['summary'] = $booking->calendar_event->title;
    }

    return $data;
}

/**
 * Helper function to get client from booking config
 */
function apple_calendar_get_client_from_booking_config($config, $booking, $bootstrap) {
    if (!$bootstrap->isConfigured()) {
        return false;
    }

    $meta = \FluentBooking\App\Models\Meta::where('object_type', '_apple_calendar_user_token')
        ->where('object_id', $booking->host_user_id)
        ->where('id', $config['db_id'])
        ->first();

    if (!$meta) {
        return false;
    }

    return \FluentBookingPro\App\Services\Integrations\Calendars\AppleCalendar\AppleHelper::getClientByMeta($meta);
}
