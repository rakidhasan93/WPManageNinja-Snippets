add_filter('fluent_boards/get_avatar', function ($avatarUrl, $email, $name = '') {
    $user = get_user_by('email', $email);

    if ($user) {
        // If name not passed, use display name
        if (!$name) {
            $name = $user->display_name;
        }

        // Check for uploaded profile image
        $custom_avatar = get_user_meta($user->ID, 'user_profile_picture', true);
        if (!empty($custom_avatar)) {
            return esc_url($custom_avatar);
        }
    }

    // Ensure there's at least a fallback name
    if (!$name) {
        $name = 'User';
    }

    // Use ui-avatars.com based on name initials (different for each user)
    return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&size=128&background=0D8ABC&color=fff';
}, 10, 3);
