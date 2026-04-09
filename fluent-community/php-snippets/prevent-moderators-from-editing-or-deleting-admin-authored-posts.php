<?php

/**
 * Prevent moderators from editing or deleting admin-authored posts.
 * Works for:
 * - global moderators
 * - space-specific moderators
 */


add_filter('rest_pre_dispatch', function ($result, $server, $request) {
    if (
        !class_exists('\FluentCommunity\App\Models\Feed') ||
        !class_exists('\FluentCommunity\App\Models\User')
    ) {
        return $result;
    }

    $route  = $request->get_route();
    $method = strtoupper($request->get_method());

    if (!preg_match('#/feeds/(\d+)$#', $route, $matches)) {
        return $result;
    }

    if (!in_array($method, ['POST', 'PATCH', 'DELETE'], true)) {
        return $result;
    }

    $currentUserId = get_current_user_id();
    if (!$currentUserId) {
        return $result;
    }

    $feed = \FluentCommunity\App\Models\Feed::find((int) $matches[1]);
    if (!$feed) {
        return $result;
    }

    $actor = \FluentCommunity\App\Models\User::find($currentUserId);
    $author = \FluentCommunity\App\Models\User::find($feed->user_id);

    if (!$actor || !$author) {
        return $result;
    }

    // If the acting user is an admin, allow.
    if ($actor->isCommunityAdmin()) {
        return $result;
    }

    // Detect moderator access in the relevant scope.
    $isGlobalModerator = $actor->isCommunityModerator() && !$actor->isCommunityAdmin();

    $actorSpaceRole = '';
    if ($feed->space) {
        $actorSpaceRole = $actor->getSpaceRole($feed->space);
    }

    $isSpaceModerator = ($actorSpaceRole === 'moderator');
    $isModeratorOnly = $isGlobalModerator || $isSpaceModerator;

    if (!$isModeratorOnly) {
        return $result;
    }

    // Determine whether the post author is an admin in the relevant scope.
    $authorIsAdmin = $author->isCommunityAdmin();

    if ($feed->space && $author->getSpaceRole($feed->space) === 'admin') {
        $authorIsAdmin = true;
    }

    if (!$authorIsAdmin) {
        return $result;
    }

    return new \WP_Error(
        'fcom_moderator_cannot_manage_admin_post',
        __('Moderators cannot edit or delete posts created by admins.', 'fluent-community'),
        ['status' => 403]
    );
}, 9, 3);
