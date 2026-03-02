<?php
    
// Change default decimal points to 0

add_filter('fluent_booking/default_currency_settings', function($defaults) {
    $defaults['decimal_points'] = 0;    
    return $defaults;
});

    
// For example: 100 $ (symbol on the right with space)

add_filter('fluent_booking/global_currency_settings', function ($settings) {
    $settings['currency_position'] = 'right_space'; // or 'left_space'
    return $settings;
});

?>
