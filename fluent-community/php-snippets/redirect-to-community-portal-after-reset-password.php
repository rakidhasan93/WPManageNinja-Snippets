add_action('after_password_reset', function ($user, $new_pass) {
    // Redirect to your FluentCommunity login page
    wp_safe_redirect(home_url('/New/aaa_community_portal'));
    exit;
}, 10, 2);
