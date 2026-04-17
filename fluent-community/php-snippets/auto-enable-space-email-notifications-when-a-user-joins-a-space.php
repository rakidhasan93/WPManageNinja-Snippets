<?php

/**
 * Auto-enable Space email notifications when a user joins a Space.
 * This enables:
 * - admin post emails
 * - member post emails
 */

add_action('fluent_community/space/joined', function ($space, $userId, $by = null) {
    if (
        !class_exists('\FluentCommunity\App\Services\NotificationPref') ||
        !$space ||
        !$userId
    ) {
        return;
    }

    // Enable "all member posts" => both prefs must be on
    \FluentCommunity\App\Services\NotificationPref::updateUserSinglePref(
        $userId,
        'np_by_admin_mail',
        1,
        $space->id
    );

    \FluentCommunity\App\Services\NotificationPref::updateUserSinglePref(
        $userId,
        'np_by_member_mail',
        1,
        $space->id
    );
}, 10, 3);
