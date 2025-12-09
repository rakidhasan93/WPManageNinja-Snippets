add_filter('fluent_booking/default_currency_settings', function($defaults) {
    $defaults['decimal_points'] = 0;
    return $defaults;
});
