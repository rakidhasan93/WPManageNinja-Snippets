add_filter('gettext', function ($translated, $text, $domain) {

    // Only FluentCommunity
    if ($domain !== 'fluent-community') {
        return $translated;
    }

    // Only login form
    if (
        empty($_GET['fcom_action']) ||
        $_GET['fcom_action'] !== 'auth' ||
        empty($_GET['form']) ||
        $_GET['form'] !== 'login'
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
