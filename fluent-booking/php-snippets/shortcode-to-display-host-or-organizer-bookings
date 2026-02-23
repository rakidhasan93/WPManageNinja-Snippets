<?php

/**
 * Plugin Name: FluentBooking Host Lists Shortcode
 * Description: Host/Organizer-facing booking list shortcode using FluentBooking's native front-end template.
 */

add_action('init', function () {
    add_shortcode('fluent_booking_host_lists', function ($atts) {
        if (!is_user_logged_in()) {
            return __('Please login to view your bookings', 'fluent-booking');
        }

        if (!class_exists('\FluentBooking\App\Models\Booking')) {
            return '';
        }

        $atts = shortcode_atts([
            'title'        => __('My Hosted Bookings', 'fluent-booking'),
            'filter'       => 'show',
            'pagination'   => 'show',
            'period'       => 'all',
            'calendar_ids' => 'all',
            'no_bookings'  => __('No bookings found', 'fluent-booking'),
            'per_page'     => 10
        ], $atts);

        $atts['title']       = sanitize_text_field($atts['title']);
        $atts['filter']      = sanitize_text_field($atts['filter']);
        $atts['pagination']  = sanitize_text_field($atts['pagination']);
        $atts['no_bookings'] = sanitize_text_field($atts['no_bookings']);

        $request       = $_REQUEST; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $perPage       = intval(\FluentBooking\Framework\Support\Arr::get($request, 'booking_per_page', $atts['per_page']));
        $currentPage   = intval(\FluentBooking\Framework\Support\Arr::get($request, 'booking_page', 1));
        $bookingPeriod = sanitize_text_field(\FluentBooking\Framework\Support\Arr::get($request, 'booking_period', $atts['period']));
        $hostId        = get_current_user_id();

        $bookingQuery = \FluentBooking\App\Models\Booking::query()
            ->with('calendar_event')
            ->where(function ($query) use ($hostId) {
                $query->where('host_user_id', $hostId)
                    ->orWhereHas('hosts', function ($hostQuery) use ($hostId) {
                        $hostQuery->where('user_id', $hostId);
                    });
            })
            ->applyComputedStatus($bookingPeriod)
            ->applyBookingOrderByStatus($bookingPeriod);

        if ($atts['calendar_ids'] !== 'all') {
            $calendarIds = array_filter(array_map('intval', explode(',', $atts['calendar_ids'])));
            if ($calendarIds) {
                $bookingQuery->whereIn('calendar_id', $calendarIds);
            }
        }

        $bookings = $bookingQuery->paginate($perPage, ['*'], 'booking_page', $currentPage)
            ->appends(['booking_page' => $currentPage])
            ->withQueryString();

        foreach ($bookings as &$booking) {
            $booking->author_name         = $booking->getHostDetails(false)['name'];
            $booking->happening_status    = $booking->getOngoingStatus();
            $booking->booking_status_text = $booking->getBookingStatus();
            $booking->payment_status_text = $booking->getPaymentStatus();
            $booking->booking_date        = \FluentBooking\App\Services\DateTimeHelper::formatToLocale($booking->getAttendeeStartTime(), 'date');
            $booking->booking_time        = \FluentBooking\App\Services\DateTimeHelper::formatToLocale($booking->getAttendeeStartTime(), 'time') . ' - ' .
                                            \FluentBooking\App\Services\DateTimeHelper::formatToLocale($booking->getAttendeeEndTime(), 'time');
        }

        $currentPage = $bookings->currentPage();
        $lastPage    = $bookings->lastPage();
        $startPage   = max(1, $currentPage - 2);
        $endPage     = min($lastPage, $currentPage + 2);

        if ($currentPage < 3) {
            $endPage = min($lastPage, 5);
        }
        if ($currentPage > $lastPage - 2) {
            $startPage = max(1, $lastPage - 4);
        }

        $periodOptions = \FluentBooking\App\Services\Helper::getBookingPeriodOptions();
        $pageOptions   = apply_filters('fluent_booking/booking_per_page_options', [5, 10, 15, 20, 50, 100]);

        wp_enqueue_script(
            'fluent-booking-list',
            \FluentBooking\App\App::getInstance('url.assets') . 'public/js/bookings.js',
            [],
            FLUENT_BOOKING_ASSETS_VERSION,
            true
        );

        return \FluentBooking\App\App::make('view')->make('public.bookings', [
            'bookings'       => $bookings,
            'attributes'     => $atts,
            'per_page'       => $perPage,
            'start_page'     => $startPage,
            'end_page'       => $endPage,
            'booking_period' => $bookingPeriod,
            'page_options'   => $pageOptions,
            'period_options' => $periodOptions
        ]);
    });
});
