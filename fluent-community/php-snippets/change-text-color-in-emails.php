<?php

/**
 * Make FluentCommunity notification email links readable.
 */

add_filter('fluent_community/theme_color', function ($color) {
    // Used for email links, post titles, unread notification links, footer links.
    return '#2B2E33';
}, 20);

add_filter('fluent_community/theme_button_text_color', function ($color) {
    return '#ffffff';
}, 20);


/**
 * Optional: darken FluentCommunity email footer/helper text.
 */
add_filter('wp_mail', function ($args) {
    if (empty($args['message']) || strpos($args['message'], 'fcom_email') === false) {
        return $args;
    }

    $args['message'] = str_replace(
        [
            'color: #9a9ea6',
            'color:#9a9ea6',
            'color: #8e8e90',
            'color:#8e8e90',
        ],
        [
            'color: #525866',
            'color:#525866',
            'color: #525866',
            'color:#525866',
        ],
        $args['message']
    );

    return $args;
});

?>
