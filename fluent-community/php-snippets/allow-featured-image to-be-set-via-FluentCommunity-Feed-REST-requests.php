<?php 

/**
 * Allow featured_image to be set via FluentCommunity Feed REST requests.
 */

add_filter('fluent_community/feed/new_feed_data', function ($data, $requestData) {
    $featuredImage = !empty($requestData['featured_image']) ? esc_url_raw($requestData['featured_image']) : '';

    if ($featuredImage) {
        $data['featured_image'] = $featuredImage;
    }

    return $data;
}, 10, 2);

add_filter('fluent_community/feed/update_feed_data', function ($data, $requestData) {
    if (array_key_exists('featured_image', $requestData)) {
        $data['featured_image'] = !empty($requestData['featured_image'])
            ? esc_url_raw($requestData['featured_image'])
            : null;
    }

    return $data;
}, 10, 2);
