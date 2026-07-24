<?php

add_filter('pre_wp_mail', function ($pre, $atts) {
    if (!function_exists('wp_mail')) {
        return $pre;
    }

    $message = isset($atts['message']) ? (string) $atts['message'] : '';

    // Only target FluentCommunity email templates.
    $is_fluent_community_email = (
        strpos($message, 'fcom_email') !== false ||
        strpos($message, 'Manage Your Email Notifications Preference') !== false ||
        strpos($message, 'fcom_route') !== false
    );

    if (!$is_fluent_community_email) {
        return $pre;
    }

    $recipients = isset($atts['to']) ? (array) $atts['to'] : [];

    if (!$recipients) {
        return $pre;
    }

    global $wpdb;

    foreach ($recipients as $recipient) {
        if (preg_match('/<([^>]+)>/', $recipient, $matches)) {
            $email = sanitize_email($matches[1]);
        } else {
            $email = sanitize_email($recipient);
        }

        if (!$email) {
            continue;
        }

        $status = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT status
                 FROM {$wpdb->prefix}fc_subscribers
                 WHERE email = %s
                 ORDER BY id DESC
                 LIMIT 1",
                $email
            )
        );

        // Block FluentCommunity emails if the FluentCRM contact is unsubscribed.
        if ($status === 'unsubscribed') {
            return false;
        }
    }

    return $pre;
}, 10, 2);

?>
