add_action('fluent_booking/before_creating_schedule', function ($bookingData, $postedData, $calendarEvent) {

    if (!is_user_logged_in()) {

        $login_url = wp_login_url( get_permalink() ); 
        // This sends users back to the booking page after login

        wp_send_json([
            'success' => false,
            'message' => sprintf(
                __('You need to be logged in to schedule a meeting. <a href="%s">Login Now</a>', 'fluent-booking'),
                esc_url($login_url)
            )
        ], 403);
    }

}, 10, 3);
