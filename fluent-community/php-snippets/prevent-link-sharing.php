add_filter('fluent_community/feed/new_feed_data', function($feedData, $allData) {
    $message = $feedData['message'];
    if (preg_match('/https?:\/\/|[\[(.+?)\](.+?)]/i', $message)) {
        return new WP_Error('links_not_allowed', 'Links are not allowed in posts.');
    }
    return $feedData;
}, 10, 2);
