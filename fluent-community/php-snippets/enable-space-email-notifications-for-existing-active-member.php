<?php

/**
 * One-time: Enable space email notifications for existing active members.
 *
 * How to use:
 * 1. Add this snippet.
 * 2. Change $targetSpaceSlug if needed.
 * 3. Visit any frontend/admin page once while logged in as admin.
 * 4. After it runs, remove the snippet.
 */

add_action('init', function () {
    if (!is_user_logged_in() || !current_user_can('manage_options')) {
        return;
    }

    if (!class_exists('\FluentCommunity\App\Models\Space') ||
        !class_exists('\FluentCommunity\App\Models\SpaceUserPivot') ||
        !class_exists('\FluentCommunity\App\Services\NotificationPref')) {
        return;
    }

    if (get_option('_fcom_enabled_space_email_notifications_done')) {
        return;
    }

    $targetSpaceSlug = ''; // Example: 'backend'. Leave empty for all spaces.

    $spacesQuery = \FluentCommunity\App\Models\Space::query();

    if ($targetSpaceSlug) {
        $spacesQuery->where('slug', $targetSpaceSlug);
    }

    $spaces = $spacesQuery->get();

    foreach ($spaces as $space) {
        $userIds = \FluentCommunity\App\Models\SpaceUserPivot::where('space_id', $space->id)
            ->where('status', 'active')
            ->pluck('user_id')
            ->toArray();

        foreach ($userIds as $userId) {
            \FluentCommunity\App\Services\NotificationPref::updateUserSinglePref($userId, 'np_by_admin_mail', 1, $space->id);
            \FluentCommunity\App\Services\NotificationPref::updateUserSinglePref($userId, 'np_by_member_mail', 1, $space->id);
        }
    }

    update_option('_fcom_enabled_space_email_notifications_done', 1);
});
