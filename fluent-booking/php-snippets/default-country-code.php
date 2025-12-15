 add_filter('fluent_calendar/global_booking_vars', function ($data) {
    $globalSettings = \FluentBooking\App\Services\Helper::getGlobalSettings();
    $data['user_country'] = \FluentBooking\Framework\Support\Arr::get($globalSettings, 'administration.default_country', '');
    return $data;
}); 
