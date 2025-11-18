add_filter('fluent_booking/event_landing_page_vars',function($data) {
    $assetUrl = \FluentBooking\App\App::getInstance('url.assets');
    $data['css_files'][] = $assetUrl . 'public/custom.css';
    return $data;
},10,1);
