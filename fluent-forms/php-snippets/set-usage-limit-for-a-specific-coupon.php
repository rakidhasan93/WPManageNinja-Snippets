<?php

/**
 * Fluent Forms global usage limit for coupon UM300OFF.
 * Works with stackable coupons by checking/removing the final discount order item.
 */

if (!function_exists('ff_um300off_coupon_config')) {
    function ff_um300off_coupon_config() {
        return [
            'code'     => 'UM300OFF',
            'limit'    => 100,
            'form_id'  => 0, // Set to your form ID if this limit should apply only to one form. Otherwise keep 0.
        ];
    }
}

if (!function_exists('ff_um300off_get_coupon_title')) {
    function ff_um300off_get_coupon_title($couponCode) {
        global $wpdb;

        $couponTable = $wpdb->prefix . 'fluentform_coupons';

        $title = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT title FROM {$couponTable} WHERE code = %s LIMIT 1",
                $couponCode
            )
        );

        return $title ?: $couponCode;
    }
}

if (!function_exists('ff_um300off_used_count')) {
    function ff_um300off_used_count() {
        global $wpdb;

        $config = ff_um300off_coupon_config();

        $couponTitle = ff_um300off_get_coupon_title($config['code']);

        $orderItemsTable = $wpdb->prefix . 'fluentform_order_items';
        $submissionsTable = $wpdb->prefix . 'fluentform_submissions';

        $whereForm = '';
        $params = [
            'discount',
            $couponTitle,
        ];

        if (!empty($config['form_id'])) {
            $whereForm = ' AND oi.form_id = %d';
            $params[] = absint($config['form_id']);
        }

        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT COUNT(DISTINCT oi.submission_id)
                FROM {$orderItemsTable} oi
                INNER JOIN {$submissionsTable} fs ON fs.id = oi.submission_id
                WHERE oi.type = %s
                AND oi.item_name = %s
                AND fs.status NOT IN ('trashed', 'spam')
                {$whereForm}
                ",
                $params
            )
        );
    }
}

if (!function_exists('ff_um300off_limit_reached')) {
    function ff_um300off_limit_reached() {
        $config = ff_um300off_coupon_config();

        return ff_um300off_used_count() >= absint($config['limit']);
    }
}

/**
 * Block the coupon when user tries to apply it.
 */
add_action('wp_ajax_fluentform_apply_coupon', 'ff_um300off_block_coupon_ajax', 1);
add_action('wp_ajax_nopriv_fluentform_apply_coupon', 'ff_um300off_block_coupon_ajax', 1);

function ff_um300off_block_coupon_ajax() {
    $config = ff_um300off_coupon_config();

    $requestedCoupon = isset($_REQUEST['coupon'])
        ? strtoupper(trim(sanitize_text_field(wp_unslash($_REQUEST['coupon']))))
        : '';

    if ($requestedCoupon !== strtoupper($config['code'])) {
        return;
    }

    if (!ff_um300off_limit_reached()) {
        return;
    }

    wp_send_json([
        'message' => 'Sorry, this coupon has reached its maximum usage limit.'
    ], 423);
}

/**
 * Final enforcement.
 * This removes UM300OFF from the actual order items if the limit has been reached.
 * This is the most important part for stackable coupons.
 */
add_filter('fluentform/submission_order_items', function ($orderItems, $submissionData, $form, $paymentMethod) {
    $config = ff_um300off_coupon_config();

    if (!empty($config['form_id']) && (int) $form->id !== (int) $config['form_id']) {
        return $orderItems;
    }

    if (!ff_um300off_limit_reached()) {
        return $orderItems;
    }

    $couponTitle = ff_um300off_get_coupon_title($config['code']);

    foreach ($orderItems as $index => $item) {
        $type = isset($item['type']) ? $item['type'] : '';
        $itemName = isset($item['item_name']) ? $item['item_name'] : '';

        if ($type === 'discount' && $itemName === $couponTitle) {
            unset($orderItems[$index]);
        }
    }

    return array_values($orderItems);
}, 9999, 4);

?>
