<?php

add_filter('fluent_community/auth/signup_fields', function ($fields) {
    if (isset($fields['username'])) {
        $fields['username']['placeholder'] = 'e.g. HealthyJane42 — NOT your email address';
    }

    $publicUsernameCheckbox = [
        'type'              => 'inline_checkbox',
        'inline_label'      => 'I understand my chosen username will be displayed publicly and I will not use my email address as my username.',
        'required'          => true,
        'sanitize_callback' => 'sanitize_text_field',
    ];

    // Put the new checkbox directly before the existing Terms checkbox.
    $newFields = [];

    foreach ($fields as $key => $field) {
        if ($key === 'terms') {
            $newFields['public_username_ack'] = $publicUsernameCheckbox;
        }

        $newFields[$key] = $field;
    }

    return $newFields;
}, 20);




/**
 * Optional hard protection: reject email-style usernames during Fluent Community signup.
 */

add_action('wp_ajax_nopriv_fcom_user_registration', 'fcom_block_email_username_before_signup', 1);
add_action('wp_ajax_fcom_user_registration', 'fcom_block_email_username_before_signup', 1);

function fcom_block_email_username_before_signup() {
    $username = isset($_POST['username']) ? sanitize_text_field(wp_unslash($_POST['username'])) : '';
    $email    = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';

    if (!$username) {
        return;
    }

    $normalizedUsername = strtolower(preg_replace('/[^a-z0-9]/i', '', $username));
    $normalizedEmail    = strtolower(preg_replace('/[^a-z0-9]/i', '', $email));
    $emailLocalPart     = $email ? strtolower(preg_replace('/[^a-z0-9]/i', '', strtok($email, '@'))) : '';

    $looksLikeEmail = (
        strpos($username, '@') !== false ||
        is_email($username) ||
        ($email && $normalizedUsername === $normalizedEmail) ||
        ($emailLocalPart && $normalizedUsername === $emailLocalPart)
    );

    if (!$looksLikeEmail) {
        return;
    }

    wp_send_json([
        'message' => 'Please choose an anonymous username. Do not use your email address as your username.',
        'errors'  => [
            'username' => 'Your username is visible to all members. Choose something anonymous — do not use your email address.'
        ]
    ], 422);
}

?>
