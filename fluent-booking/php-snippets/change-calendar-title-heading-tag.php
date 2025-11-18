add_filter('fluent_booking/public_event_vars', function ($eventVars) { 
$eventVars['title_tag'] = 'h3'; 
return $eventVars; 
}, 10, 1);
