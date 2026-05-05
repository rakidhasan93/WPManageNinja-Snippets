<?php

/**
 * Use /community-login as the public login URL.
 */

add_action('init', function () {
    add_rewrite_rule('^community', 'wp-login.php', 'top');
});

add_filter('login_url', function ($login_url, $redirect, $force_reauth) {
    $url = home_url('/community');
    if (!empty($redirect)) {
        $url = add_query_arg('redirect_to', rawurlencode($redirect), $url);
    }
    return $url;
}, 10, 3);

add_action('login_init', function () {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
        wp_safe_redirect(home_url('/community'));
        exit;
    }
});
