<?php

/**
 * Enforce allowed donation amounts for Paymattic donation forms.
 * Put this in a small mu-plugin or your theme's functions.php.
 *
 * If your build stores amounts as cents, change the allowed values accordingly.
 */

add_filter('wppayform/validate_data_on_submission_donation_item', function ($error, $field, $value, $formData) {
    // Allowed preset amounts from the donation field
    $allowedAmounts = [50, 100, 250, 500, 1000, 2000];

    $amount = is_numeric($value) ? (float) $value : 0;

    if (!in_array($amount, $allowedAmounts, true)) {
        return __('Please choose one of the preset donation amounts.', 'your-textdomain');
    }

    return $error;
}, 10, 4);
