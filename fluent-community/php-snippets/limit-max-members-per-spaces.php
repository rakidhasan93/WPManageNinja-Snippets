
<?php

add_filter('rest_pre_dispatch', function ($result, $server, $request) {
    if (!class_exists('\FluentCommunity\App\Models\Space') || !class_exists('\FluentCommunity\App\Models\SpaceUserPivot')) {
        return $result;
    }

    if (strtoupper($request->get_method()) !== 'POST') {
        return $result;
    }

    $route = $request->get_route();

    // Only intercept FluentCommunity join / add-member routes.
    if (!preg_match('#/spaces/([^/]+)/(join|members)$#', $route, $matches)) {
        return $result;
    }

    $spaceSlug = $request->get_param('spaceSlug');
    if (!$spaceSlug) {
        $spaceSlug = sanitize_title($matches[1]);
    }

    // Per-space limits by slug.
    $spaceLimits = [
        'say-hello' => 1,
    ];

    // Optional fallback for any space not listed above (0 = no global limit).
    $defaultLimit = 0;

    $limit = isset($spaceLimits[$spaceSlug]) ? (int) $spaceLimits[$spaceSlug] : (int) $defaultLimit;
    if ($limit <= 0) {
        return $result;
    }

    $space = \FluentCommunity\App\Models\Space::where('slug', $spaceSlug)->first();
    if (!$space) {
        return $result;
    }

    // Count active + pending to avoid overbooking via pending requests.
    $currentCount = \FluentCommunity\App\Models\SpaceUserPivot::where('space_id', $space->id)
        ->whereIn('status', ['active', 'pending'])
        ->count();

    if ($currentCount >= $limit) {
        return new \WP_Error(
            'fcom_space_limit_reached',
            __('Sorry, the membership total of this Space has been reached.', 'fluent-community'),
            ['status' => 403]
        );
    }

    return $result;
}, 9, 3);
