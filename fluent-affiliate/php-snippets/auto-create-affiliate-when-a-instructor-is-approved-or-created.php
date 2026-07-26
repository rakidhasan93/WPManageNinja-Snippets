<?php

use FluentAffiliate\App\Models\Affiliate;
use FluentAffiliate\App\Models\User as FluentAffiliateUser;

/**
 * Create affiliate profile if it does not exist yet.
 */

function srm_maybe_create_affiliate($user_id, $status = 'active') {
    $user_id = (int) $user_id;
    if (!$user_id) {
        return;
    }

    if (!class_exists(FluentAffiliateUser::class) || !class_exists(Affiliate::class)) {
        return;
    }

    $existing = Affiliate::where('user_id', $user_id)->first();
    if ($existing) {
        return;
    }

    $faUser = FluentAffiliateUser::find($user_id);
    if (!$faUser) {
        return;
    }

    $faUser->syncAffiliateProfile([
        'status'    => $status,
        'rate_type' => 'default',
    ]);
}

/**
 * Auto-create affiliate when a Tutor instructor is approved/created.
 */

add_action('tutor_new_instructor_after', function ($user_id) {
    srm_maybe_create_affiliate($user_id, 'active');
}, 10, 1);

/**
 * Auto-create affiliate when a student is enrolled in a Tutor course.
 * This is safer than using plain user registration.
 */

add_action('tutor_after_enrolled', function ($course_id, $user_id, $enrolled_id) {
    srm_maybe_create_affiliate($user_id, 'active');
}, 10, 3);

/**
 * Compatibility for newer Tutor enrollment hook if used in your version.
 */

add_action('tutor_enrollment/after/complete', function ($enrol_id) {
    $user_id = (int) get_post_field('post_author', $enrol_id);
    if ($user_id) {
        srm_maybe_create_affiliate($user_id, 'active');
    }
}, 10, 1);
