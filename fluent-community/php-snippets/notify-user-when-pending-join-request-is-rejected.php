<?php

/**
 * FluentCommunity: notify user when pending join request is rejected.
 * Paste in functions.php or Code Snippets.
 */

$GLOBALS['fc_pending_reject_map'] = [];

/**
 * Capture whether the removed user was in pending state before deletion.
 */
add_filter('rest_pre_dispatch', function ($result, $server, $request) {
    if (strtoupper($request->get_method()) !== 'POST') {
        return $result;
    }

    $route = (string) $request->get_route();
    if (!preg_match('#^/fluent-community/v2/spaces/([^/]+)/members/remove$#', $route, $m)) {
        return $result;
    }

    if (!class_exists('\FluentCommunity\App\Models\Space') || !class_exists('\FluentCommunity\App\Models\SpaceUserPivot')) {
        return $result;
    }

    $spaceSlug = sanitize_title($m[1]);
    $userId    = (int) $request->get_param('user_id');

    if (!$spaceSlug || !$userId) {
        return $result;
    }

    $space = \FluentCommunity\App\Models\Space::where('slug', $spaceSlug)->first();
    if (!$space) {
        return $result;
    }

    $pivot = \FluentCommunity\App\Models\SpaceUserPivot::where('space_id', $space->id)
        ->where('user_id', $userId)
        ->first();

    $key = $space->id . ':' . $userId;
    $GLOBALS['fc_pending_reject_map'][$key] = ($pivot && $pivot->status === 'pending');

    return $result;
}, 10, 3);

/**
 * After member removal action, create notification only if it was pending + removed by admin.
 */
add_action('fluent_community/space/user_left', function ($space, $userId, $by) {
    if ($by !== 'by_admin') {
        return;
    }

    if (
        !class_exists('\FluentCommunity\App\Models\Notification') ||
        !class_exists('\FluentCommunity\App\Models\User')
    ) {
        return;
    }

    $key = $space->id . ':' . (int) $userId;
    $wasPending = !empty($GLOBALS['fc_pending_reject_map'][$key]);
    unset($GLOBALS['fc_pending_reject_map'][$key]);

    if (!$wasPending) {
        return;
    }

    $adminId = get_current_user_id();
    if (!$adminId) {
        $adminId = (int) $userId; // fallback, should rarely happen
    }

    $content = sprintf(
        __('Your request to join %s was not approved.', 'fluent-community'),
        '<b>' . esc_html($space->title) . '</b>'
    );

    $route = [
        'name'   => 'space_feeds',
        'params' => [
            'space' => $space->slug
        ]
    ];

    $notification = \FluentCommunity\App\Models\Notification::create([
        'object_id'       => $space->id,
        'src_user_id'     => $adminId,
        'src_object_type' => 'space',
        'action'          => 'space/join_request_rejected',
        'content'         => $content,
        'route'           => $route,
    ]);

    $notification->subscribe([(int) $userId]);
}, 10, 3);
