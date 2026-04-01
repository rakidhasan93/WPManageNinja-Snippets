<?php

add_filter('fluent_calendar/global_booking_vars', function ($vars) {
    $vars['i18']['Add guest']  = 'Ajouter un participant';
    $vars['i18']['Add guests'] = 'Ajouter un participant'; // if plural appears
    return $vars;
});
