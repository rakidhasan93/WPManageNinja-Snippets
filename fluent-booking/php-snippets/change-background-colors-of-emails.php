<?php

add_filter('wp_mail', function ($args) {
    if (empty($args['message']) || !is_string($args['message'])) {
        return $args;
    }

    $message = $args['message'];

    /*
     * Only target FluentBooking's default email template.
     * These markers are specific to that template.
     */
    $is_fluentbooking_email =
        strpos($message, 'class="body-inner"') !== false &&
        strpos($message, "font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;") !== false &&
        strpos($message, 'border-top: 4px solid #0069ff;') !== false;

    if (!$is_fluentbooking_email) {
        return $args;
    }

    // Set your custom colors here.
    $outer_bg   = '#f7f4ee'; // grey/background around the email
    $top_border = '#1f4db8'; // blue line at the top

    $message = str_replace(
        ['background-color: #f1f1f1;', 'border-top: 4px solid #0069ff;'],
        ['background-color: ' . $outer_bg . ';', 'border-top: 4px solid ' . $top_border . ';'],
        $message
    );

    $args['message'] = $message;

    return $args;
}, 20);

?>
