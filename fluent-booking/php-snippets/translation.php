<?php

add_filter('fluent_calendar/global_booking_vars', function ($vars) {
    $vars['i18']['Add guest'] = 'Tilføj gæst';
    $vars['i18']['Name'] = 'Név';
    $vars['i18']['Email'] = 'E-mail';
    $vars['i18']['Please select'] = 'Kérjük válasszon';
    return $vars;
});

