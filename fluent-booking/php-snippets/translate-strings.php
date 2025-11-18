add_filter('fluent_booking/admin_vars', function ($vars) {
    $vars['trans']['Bookings'] = 'Appointments';

    return $vars;
}, 10, 1); 
