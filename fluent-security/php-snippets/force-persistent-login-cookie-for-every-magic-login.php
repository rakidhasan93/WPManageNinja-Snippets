<?php

add_action('wp_login', function ($user_login, $user) {
    if (
        !class_exists('\FluentAuth\App\Helpers\Helper') ||
        \FluentAuth\App\Helpers\Helper::getLoginMedia() !== 'magic_login'
    ) {
        return;
    }

    if ($user instanceof WP_User) {
        wp_set_auth_cookie($user->ID, true, is_ssl());
    }
}, 99, 2);

?>
