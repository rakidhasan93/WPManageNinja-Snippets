<?php
/**
 * Block submission if no subscription plan is selected.
 * Form ID: 1148
 */
add_filter('wppayform/form_submission_validation_errors', function ($errors, $formId, $formattedElements) {
    if ((int) $formId !== 1148) {
        return $errors;
    }

    // These are the two recurring fields in your exported form.
    $subscriptionFields = [
        'recurring_payment_item',
        'recurring_payment_item_1',
    ];

    $hasSelection = false;

    foreach ($subscriptionFields as $fieldKey) {
        if (!isset($_POST[$fieldKey])) {
            continue;
        }

        $value = trim((string) wp_unslash($_POST[$fieldKey]));

        // Important: "0" is a valid selection, so don't use empty()
        if ($value !== '') {
            $hasSelection = true;
            break;
        }
    }

    if (!$hasSelection) {
        $errors['subscription_required'] = 'Please select a subscription plan before submitting the form.';
    }

    return $errors;
}, 10, 3);
