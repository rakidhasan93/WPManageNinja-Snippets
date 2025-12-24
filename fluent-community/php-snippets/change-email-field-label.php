add_filter('gettext', function ($translated, $text, $domain) {

    if ($domain !== 'fluent-community') {
        return $translated;
    }

    switch ($text) {
        case 'Email Address':
            return 'Username';

        case 'Your account email address':
            return 'Enter your username';
    }

    return $translated;

}, 10, 3);
