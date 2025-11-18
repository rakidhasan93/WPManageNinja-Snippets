add_filter('fluent_booking/location_icon_heading_html', function ($html, $details, $calendarEvent) {
    return str_replace(__('Attendee Phone Number', 'fluent-booking'), __('Custom Phone Title', 'fluent-booking'), $html);
}, 10, 3);
