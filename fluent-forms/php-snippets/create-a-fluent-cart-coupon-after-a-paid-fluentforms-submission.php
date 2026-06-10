<?php

/**
 * Create a Fluent Cart coupon after a paid Fluent Forms submission.
 */

add_action('fluentform/before_form_actions_processing', function ($submissionId, $formData, $form) {
    // Change these.
    $targetFormId = 123;              // Fluent Forms form ID
    $productVariationId = 456;        // Fluent Cart product/variation ID
    $discountType = 'percentage';     // percentage or fixed
    $discountAmount = 100;            // 100 = 100% if percentage, or 20 = $20 if fixed
    $expiresInDays = 30;

    if ((int) $form->id !== $targetFormId) {
        return;
    }

    if (!class_exists('\FluentCart\Api\Resource\CouponResource')) {
        return;
    }

    if (!class_exists('\FluentForm\App\Helpers\Helper')) {
        return;
    }

    $submission = wpFluent()
        ->table('fluentform_submissions')
        ->where('id', $submissionId)
        ->first();

    if (!$submission || $submission->payment_status !== 'paid') {
        return;
    }

    $existingCode = \FluentForm\App\Helpers\Helper::getSubmissionMeta($submissionId, 'ff_fc_coupon_code');

    if ($existingCode) {
        return;
    }

    $code = 'FF-' . $submissionId . '-' . strtoupper(wp_generate_password(8, false, false));

    $couponData = [
        'title'            => 'Fluent Form Coupon #' . $submissionId,
        'code'             => $code,
        'status'           => 'active',
        'type'             => $discountType,
        'amount'           => $discountAmount,
        'stackable'        => 'no',
        'priority'         => 10,
        'show_on_checkout' => 'no',
        'start_date'       => current_time('mysql'),
        'end_date'         => date('Y-m-d H:i:s', current_time('timestamp') + DAY_IN_SECONDS * $expiresInDays),
        'conditions'       => [
            'included_products'   => [$productVariationId],
            'excluded_products'   => [],
            'included_categories' => [],
            'excluded_categories' => [],
            'max_uses'            => 1,
            'max_per_customer'    => 1,
            'min_purchase_amount' => 0,
            'max_discount_amount' => null,
        ],
    ];

    $result = \FluentCart\Api\Resource\CouponResource::create($couponData);

    \FluentForm\App\Helpers\Helper::setSubmissionMeta($submissionId, 'ff_fc_coupon_code', $code, $form->id);
    \FluentForm\App\Helpers\Helper::setSubmissionMeta($submissionId, 'ff_fc_coupon_expires_at', $couponData['end_date'], $form->id);
}, 10, 3);
