add_filter('gettext', function ($translated, $text, $domain) {

    if ($domain !== 'fluent-community') {
        return $translated;
    }

    // Exclude registration page explicitly
    if (
        isset($_GET['fcom_action']) &&
        $_GET['fcom_action'] === 'auth' &&
        isset($_GET['form']) &&
        $_GET['form'] === 'register'
    ) {
        return $translated;
    }

    if ($text === 'Email Address') {
        return 'Username';
    }

    if ($text === 'Your account email address') {
        return 'Enter your username';
    }

    return $translated;

}, 10, 3);
