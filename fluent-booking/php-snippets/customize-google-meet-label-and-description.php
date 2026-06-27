<?php
/**
 * FluentBooking: Customize Google Meet label + description
 *
 * What this does:
 * 1. Renames the built-in "Google Meet" option to your custom label
 * 2. Replaces the frontend booking-page display
 * 3. Replaces the confirmation/location details display
 * 4. Keeps automatic Google Meet link generation working
 */


/**
 * Change these to your preferred text.
 */

function my_fb_google_meet_texts()
{
    return [
        'label'       => __('Visioconference', 'your-textdomain'),
        'description' => __('You will receive your secure video meeting link after booking confirmation.', 'your-textdomain'),
        'join_label'  => __('Join video meeting', 'your-textdomain'),
    ];
}

/**
 * Rename the Google Meet option in FluentBooking location settings.
 */

add_filter('fluent_booking/get_location_fields', function ($fields, $calendar = null) {
    $texts = my_fb_google_meet_texts();

    if (!empty($fields['conferencing']['options']['google_meet'])) {
        $fields['conferencing']['options']['google_meet']['title'] = $texts['label'];
    }

    return $fields;
}, 20, 2);

/**
 * Change the Google Meet heading shown on the booking form.
 */

add_filter('fluent_booking/location_icon_heading_html', function ($html, $details, $calendarEvent) {
    if (empty($details['type']) || $details['type'] !== 'google_meet') {
        return $html;
    }

    $texts = my_fb_google_meet_texts();

    $icon = '<img class="fcal_location_icon" src="' . esc_url(FLUENT_BOOKING_PLUGIN_URL . 'assets/images/google-meet.svg') . '" alt="' . esc_attr($texts['label']) . '" />';
    $title = '<span class="fcal_loc_text">' . esc_html($texts['label']) . '</span>';
    $desc = '<div class="fcal_loc_description" style="margin-top:6px;">' . esc_html($texts['description']) . '</div>';

    return $icon . '<div class="fcal_loc_details">' . $title . $desc . '</div>';
}, 20, 3);

/**
 * Change the HTML location details on confirmation/admin views.
 */

add_filter('fluent_booking/location_details_html', function ($html, $details) {
    if (empty($details['type']) || $details['type'] !== 'google_meet') {
        return $html;
    }

    $texts = my_fb_google_meet_texts();
    $meetLink = !empty($details['online_platform_link']) ? $details['online_platform_link'] : '';

    $output  = '<div class="fcal_custom_google_meet_details">';
    $output .= '<strong>' . esc_html($texts['label']) . '</strong>';
    $output .= '<div style="margin-top:6px;">' . esc_html($texts['description']) . '</div>';

    if ($meetLink) {
        $output .= '<div style="margin-top:8px;">';
        $output .= '<a href="' . esc_url($meetLink) . '" target="_blank" rel="noopener noreferrer">' . esc_html($texts['join_label']) . '</a>';
        $output .= '</div>';
    }

    $output .= '</div>';

    return $output;
}, 20, 2);

/**
 * Change the plain-text location details where FluentBooking uses text instead of HTML.
 */

add_filter('fluent_booking/location_details_text', function ($text, $details) {
    if (empty($details['type']) || $details['type'] !== 'google_meet') {
        return $text;
    }

    $texts = my_fb_google_meet_texts();
    $meetLink = !empty($details['online_platform_link']) ? $details['online_platform_link'] : '';

    $output = $texts['label'] . ' - ' . $texts['description'];

    if ($meetLink) {
        $output .= ' - ' . $texts['join_label'] . ': ' . esc_url_raw($meetLink);
    }

    return $output;
}, 20, 2);
