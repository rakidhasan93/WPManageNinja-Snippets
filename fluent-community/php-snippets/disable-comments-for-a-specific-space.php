<?php

/**
 * Disable comments for a specific space (update the slug with your space slug "start-here").
 */

add_filter('fluent_community/user/space/permissions', function ($permissions, $space, $role, $user) {
    if ($space && $space->slug === 'start-here') {
        $permissions['can_comment'] = false;
    }
    return $permissions;
}, 10, 4);

