<?php

/**
 * Restrict a user from being moderator in more than one space.
 * Applies when adding a member or changing their role to moderator.
 */

add_filter('rest_pre_dispatch', function ($result, $server, $request) {
    if (
        !class_exists('\FluentCommunity\App\Models\Space') ||
        !class_exists('\FluentCommunity\App\Models\SpaceUserPivot') ||
        !class_exists('\FluentCommunity\App\Models\User')
    ) {
        return $result;
    }

    if (strtoupper($request->get_method()) !== 'POST') {
        return $result;
    }

    $route = $request->get_route();

    // Covers: POST /spaces/{spaceSlug}/members
    if (!preg_match('#/spaces/([^/]+)/members$#', $route, $matches)) {
        return $result;
    }

    $spaceSlug = $request->get_param('spaceSlug');
    if (!$spaceSlug) {
        $spaceSlug = sanitize_title($matches[1]);
    }

    $space = \FluentCommunity\App\Models\Space::where('slug', $spaceSlug)->first();
    if (!$space) {
        return $result;
    }

    $userId = (int) $request->get_param('user_id');
    $role   = sanitize_text_field($request->get_param('role'));

    if (!$userId || $role !== 'moderator') {
        return $result;
    }

    $user = \FluentCommunity\App\Models\User::find($userId);
    if (!$user) {
        return $result;
    }

    // Global moderators/admins are outside space-specific moderation limits.
    if ($user->isCommunityModerator()) {
        return new \WP_Error(
            'fcom_global_moderator_not_allowed',
            __('This user already has a global moderator/admin role and cannot be limited to one space.', 'fluent-community'),
            ['status' => 403]
        );
    }

    // Count other spaces where this user is already a moderator.
    $otherModeratorSpaces = \FluentCommunity\App\Models\SpaceUserPivot::where('user_id', $userId)
        ->where('role', 'moderator')
        ->where('space_id', '!=', $space->id)
        ->count();

    if ($otherModeratorSpaces > 0) {
        return new \WP_Error(
            'fcom_moderator_limit_reached',
            __('This user is already a moderator in another space and cannot be assigned to more than one space.', 'fluent-community'),
            ['status' => 403]
        );
    }

    return $result;
}, 9, 3);
