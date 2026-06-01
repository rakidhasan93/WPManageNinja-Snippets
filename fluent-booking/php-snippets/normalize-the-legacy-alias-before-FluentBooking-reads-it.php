<?Php

add_action('init', function () {
    $aliases = [
        'Asia/Calcutta' => 'Asia/Kolkata',
    ];

    foreach (['_GET', '_POST', '_REQUEST', '_COOKIE'] as $superglobal) {
        if (empty($GLOBALS[$superglobal]['timezone'])) {
            continue;
        }

        $tz = $GLOBALS[$superglobal]['timezone'];

        if (isset($aliases[$tz])) {
            $GLOBALS[$superglobal]['timezone'] = $aliases[$tz];
        }
    }

    if (!empty($_COOKIE['fluent_booking_user_timezone']) && isset($aliases[$_COOKIE['fluent_booking_user_timezone']])) {
        $_COOKIE['fluent_booking_user_timezone'] = $aliases[$_COOKIE['fluent_booking_user_timezone']];
    }
}, 1);

?>
