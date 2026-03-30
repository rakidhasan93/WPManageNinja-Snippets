<?php

add_filter('login_redirect', function ($redirect_to, $requested_redirect_to, $user) {
    if ($user instanceof WP_User) {
        return home_url('/portal');
    }
    return $redirect_to;
}, 10, 3);
