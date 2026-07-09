<?php

/**
 * Fluent Forms: email button to manually send interview notification.
 */

use FluentForm\App\Models\SubmissionMeta;
use FluentForm\App\Services\FormBuilder\ShortCodeParser;

add_filter('fluentform/shortcode_parser_callback_send_interview_button', function ($value, $parser) {
    $entry = ShortCodeParser::getEntry();

    if (!$entry || empty($entry->id) || empty($entry->form_id)) {
        return '';
    }

    $url = ff_interview_email_trigger_url($entry->id, $entry->form_id);

    return '<a href="' . esc_url($url) . '" style="background:#1a7efb;color:#ffffff;padding:12px 18px;text-decoration:none;border-radius:4px;display:inline-block;">Send Schedule Interview Email</a>';
}, 10, 2);

function ff_interview_email_trigger_url($entry_id, $form_id) {
    $ts = time();

    $token = hash_hmac(
        'sha256',
        $entry_id . '|' . $form_id . '|' . $ts,
        wp_salt('auth')
    );

    return add_query_arg([
        'ff_send_interview_email' => 1,
        'entry_id'                => absint($entry_id),
        'form_id'                 => absint($form_id),
        'ts'                      => $ts,
        'token'                   => $token,
    ], home_url('/'));
}

add_action('init', function () {
    if (empty($_GET['ff_send_interview_email'])) {
        return;
    }

    $entry_id = absint($_GET['entry_id'] ?? 0);
    $form_id  = absint($_GET['form_id'] ?? 0);
    $ts       = absint($_GET['ts'] ?? 0);
    $token    = sanitize_text_field($_GET['token'] ?? '');

    if (!$entry_id || !$form_id || !$ts || !$token) {
        wp_die('Invalid request.');
    }

    if (abs(time() - $ts) > DAY_IN_SECONDS * 14) {
        wp_die('This link has expired.');
    }

    $expected_token = hash_hmac(
        'sha256',
        $entry_id . '|' . $form_id . '|' . $ts,
        wp_salt('auth')
    );

    if (!hash_equals($expected_token, $token)) {
        wp_die('Invalid security token.');
    }

    $already_sent = SubmissionMeta::retrieve('_interview_email_sent', $entry_id, false);

    if ($already_sent) {
        wp_die('The interview email has already been sent for this submission.');
    }

    $sent = ff_send_interview_notification($entry_id, $form_id);

    if (is_wp_error($sent)) {
        wp_die(esc_html($sent->get_error_message()));
    }

    SubmissionMeta::persist($entry_id, '_interview_email_sent', current_time('mysql'), $form_id);

    wp_die('Interview scheduling email sent successfully.');
});

function ff_send_interview_notification($entry_id, $form_id) {
    $notification_name = 'Schedule Your Interview';

    $entry = wpFluent()->table('fluentform_submissions')
        ->where('id', $entry_id)
        ->where('form_id', $form_id)
        ->first();

    if (!$entry) {
        return new WP_Error('missing_entry', 'Submission not found.');
    }

    $form = wpFluent()->table('fluentform_forms')
        ->where('id', $form_id)
        ->first();

    if (!$form) {
        return new WP_Error('missing_form', 'Form not found.');
    }

    $notifications = wpFluent()->table('fluentform_form_meta')
        ->where('form_id', $form_id)
        ->where('meta_key', 'notifications')
        ->get();

    $target_notification = null;

    foreach ($notifications as $notification) {
        $settings = json_decode($notification->value, true);

        if (($settings['name'] ?? '') === $notification_name) {
            $target_notification = $notification;
            $target_notification->value = $settings;
            break;
        }
    }

    if (!$target_notification) {
        return new WP_Error('missing_notification', 'Interview email notification not found.');
    }

    $form_data = json_decode($entry->response, true);

    ShortCodeParser::resetData();

    $processed_values = ShortCodeParser::parse(
        $target_notification->value,
        $entry,
        $form_data,
        $form,
        false,
        'notifications'
    );

    $notifier = wpFluentForm()->make(
        'FluentForm\App\Services\FormBuilder\Notifications\EmailNotification'
    );

    $result = $notifier->notify($processed_values, $form_data, $form, $entry->id);

    if (!$result) {
        return new WP_Error('email_failed', 'The email could not be sent.');
    }

    return true;
}

?>
