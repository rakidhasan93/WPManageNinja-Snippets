<?php

add_filter('fluentform/validation_errors', function ($errors, $formData, $form, $fields) {
    $couponCode = 'UM300OFF';
    $usageLimit = 100;

    $submittedCoupons = [];

    if (!empty($formData['payment-coupon'])) {
        $submittedCoupons[] = strtoupper(trim($formData['payment-coupon']));
    }

    if (!empty($formData['__ff_all_applied_coupons'])) {
        $decodedCoupons = json_decode(stripslashes($formData['__ff_all_applied_coupons']), true);

        if (is_array($decodedCoupons)) {
            foreach ($decodedCoupons as $code) {
                $submittedCoupons[] = strtoupper(trim($code));
            }
        }
    }

    $submittedCoupons = array_unique(array_filter($submittedCoupons));

    if (!in_array($couponCode, $submittedCoupons, true)) {
        return $errors;
    }

    global $wpdb;

    $entryDetailsTable = $wpdb->prefix . 'fluentform_entry_details';
    $submissionsTable  = $wpdb->prefix . 'fluentform_submissions';

    $usedCount = (int) $wpdb->get_var(
        $wpdb->prepare(
            "
            SELECT COUNT(DISTINCT ed.submission_id)
            FROM {$entryDetailsTable} ed
            INNER JOIN {$submissionsTable} fs ON fs.id = ed.submission_id
            WHERE ed.field_name = %s
            AND (
                ed.field_value = %s
                OR ed.field_value LIKE %s
            )
            AND fs.status NOT IN ('trashed', 'spam')
            ",
            'payment-coupon',
            $couponCode,
            '%' . $wpdb->esc_like($couponCode) . '%'
        )
    );

    if ($usedCount >= $usageLimit) {
        $errors['payment-coupon'][] = 'Sorry, this coupon has reached its maximum usage limit.';
    }

    return $errors;
}, 10, 4);

?>
