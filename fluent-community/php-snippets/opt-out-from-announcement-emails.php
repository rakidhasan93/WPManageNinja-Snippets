<?php

/**
 * FluentCommunity Announcement Email Opt-out
 * Adds an unsubscribe link to announcement emails and blocks future announcement emails.
 */

add_action('init', function () {
    if (empty($_GET['fcom_announcement_optout']) || empty($_GET['uid']) || empty($_GET['token'])) {
        return;
    }

    $user_id = absint($_GET['uid']);
    $token   = sanitize_text_field($_GET['token']);
    $user    = get_user_by('id', $user_id);

    if (!$user) {
        wp_die('Invalid unsubscribe link.');
    }

    $expected_token = hash_hmac(
        'sha256',
        $user_id . '|' . $user->user_email,
        wp_salt('auth')
    );

    if (!hash_equals($expected_token, $token)) {
        wp_die('Invalid or expired unsubscribe link.');
    }

    update_user_meta($user_id, 'fcom_announcement_email_optout', 'yes');

    wp_die(
        '<h2>You have been unsubscribed from community announcement emails.</h2><p>You will no longer receive future FluentCommunity announcement emails.</p>',
        'Announcement Emails Unsubscribed',
        ['response' => 200]
    );
});


add_filter('wp_mail', function ($atts) {
    if (!doing_action('fluent_community/email_notify_users_everyone_tag')) {
        return $atts;
    }

    $recipients = isset($atts['to']) ? (array) $atts['to'] : [];
    $recipient  = reset($recipients);

    if (!$recipient) {
        return $atts;
    }

    if (preg_match('/<([^>]+)>/', $recipient, $matches)) {
        $email = sanitize_email($matches[1]);
    } else {
        $email = sanitize_email($recipient);
    }

    if (!$email) {
        return $atts;
    }

    $user = get_user_by('email', $email);

    if (!$user) {
        return $atts;
    }

    $token = hash_hmac(
        'sha256',
        $user->ID . '|' . $user->user_email,
        wp_salt('auth')
    );

    $unsubscribe_url = add_query_arg([
        'fcom_announcement_optout' => 1,
        'uid'                      => $user->ID,
        'token'                    => $token,
    ], home_url('/'));

    $atts['message'] .= '
        <div style="margin-top:30px;padding-top:20px;border-top:1px solid #e5e7eb;text-align:center;font-size:13px;color:#6b7280;">
            <p>If you no longer want to receive community announcement emails, you can opt out below.</p>
            <p>
                <a href="' . esc_url($unsubscribe_url) . '" style="color:#6b7280;text-decoration:underline;">
                    Unsubscribe from announcement emails
                </a>
            </p>
        </div>
    ';

    return $atts;
});


add_filter('pre_wp_mail', function ($pre, $atts) {
    if (!doing_action('fluent_community/email_notify_users_everyone_tag')) {
        return $pre;
    }

    $recipients = isset($atts['to']) ? (array) $atts['to'] : [];

    foreach ($recipients as $recipient) {
        if (preg_match('/<([^>]+)>/', $recipient, $matches)) {
            $email = sanitize_email($matches[1]);
        } else {
            $email = sanitize_email($recipient);
        }

        if (!$email) {
            continue;
        }

        $user = get_user_by('email', $email);

        if ($user && get_user_meta($user->ID, 'fcom_announcement_email_optout', true) === 'yes') {
            return false;
        }
    }

    return $pre;
}, 10, 2);

?>
