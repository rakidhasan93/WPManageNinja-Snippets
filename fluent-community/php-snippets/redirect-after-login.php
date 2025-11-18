add_action('template_redirect', function () {
    $portal_slug = 'community';

    // Current request path without domain/query
    $req = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    // Only redirect if visiting exactly /community AND user is logged in
    if ($req === $portal_slug && is_user_logged_in()) {
        wp_safe_redirect(home_url("/{$portal_slug}/courses"), 302);
        exit;
    }
}, 0);
