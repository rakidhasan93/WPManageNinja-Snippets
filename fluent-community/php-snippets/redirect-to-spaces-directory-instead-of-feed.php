<?php

add_action('template_redirect', function () {
    $portal_slug = 'portal';

    $req = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    // Skip redirect for FluentCommunity login action
    if (isset($_GET['fcom_action']) && $_GET['fcom_action'] === 'auth') {
        return;
    }

    if ($req === $portal_slug) {
        wp_safe_redirect(home_url("/{$portal_slug}/discover/spaces"), 302);
        exit;
    }
}, 0);
