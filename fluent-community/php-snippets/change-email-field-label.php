add_filter('gettext', function ($translated, $text, $domain) {

    // Scope strictly to FluentCommunity login form
    if (
        $domain !== 'fluent-community' ||
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

}, 20, 3);
