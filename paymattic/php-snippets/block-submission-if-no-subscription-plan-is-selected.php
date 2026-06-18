<?php
/**
 * Block submission only when no subscription plan is selected.
 * Form ID: 1148
 */
add_filter('wppayform/form_submission_validation_errors', function ($errors, $formId, $formattedElements) {
    if ((int) $formId !== 1148) {
        return $errors;
    }

    $submittedData = [];
    if (!empty($_REQUEST['form_data'])) {
        parse_str(wp_unslash($_REQUEST['form_data']), $submittedData);
    }

    // These are the two recurring fields in your exported form.
    $subscriptionFields = [
        'recurring_payment_item',
        'recurring_payment_item_1',
    ];

    $hasSelection = false;

    foreach ($subscriptionFields as $fieldKey) {
        if (!array_key_exists($fieldKey, $submittedData)) {
            continue;
        }

        $value = $submittedData[$fieldKey];

        // For select/radio plans this is usually a numeric index like "0", "1", "2".
        // "0" is a valid selection, so we only block if it is truly empty.
        if (!is_array($value) && trim((string) $value) !== '') {
            $hasSelection = true;
            break;
        }

        if (is_array($value) && !empty($value)) {
            $hasSelection = true;
            break;
        }
    }

    if (!$hasSelection) {
        $errors['subscription_required'] = 'Please select a subscription plan before submitting the form.';
    }

    return $errors;
}, 10, 3);
