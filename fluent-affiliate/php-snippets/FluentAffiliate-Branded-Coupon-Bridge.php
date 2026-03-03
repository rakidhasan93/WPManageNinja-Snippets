<?php

add_filter('fluent_affiliate/affiliate_attached_coupons', function ($coupons, $affiliate, $context) {
    // Keep existing provider output if another integration already handled it.
    if (!empty($coupons)) {
        return $coupons;
    }

    if (empty($affiliate) || empty($affiliate->id)) {
        return $coupons;
    }

    if (!class_exists('\FluentCart\App\Models\Coupon')) {
        return $coupons;
    }

    try {
        $affiliateId = (int) $affiliate->id;
        $results = [];

        // Scan coupons and keep those mapped to this affiliate via _fa_affiliate_id.
        $allCoupons = \FluentCart\App\Models\Coupon::query()->limit(2000)->get();

        foreach ($allCoupons as $coupon) {
            $mappedAffiliateId = (int) $coupon->getMeta('_fa_affiliate_id');

            if ($mappedAffiliateId !== $affiliateId) {
                continue;
            }

            $code = isset($coupon->code) ? (string) $coupon->code : '';
            if ($code === '') {
                continue;
            }

            $description = '';
            if (isset($coupon->description) && $coupon->description) {
                $description = (string) $coupon->description;
            } elseif (isset($coupon->title) && $coupon->title) {
                $description = (string) $coupon->title;
            }

            $results[] = [
                'id'          => isset($coupon->id) ? (int) $coupon->id : 0,
                'code'        => sanitize_text_field($code),
                'description' => sanitize_text_field(wp_strip_all_tags($description)),
            ];
        }

        return $results;
    } catch (\Throwable $e) {
        return $coupons;
    }
}, 10, 3);
