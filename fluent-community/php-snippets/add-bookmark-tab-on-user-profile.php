<?php

add_filter('fluent_community/profile_view_data', function ($profile) {
    $profile['profile_navs'][] = [
        'slug'          => 'bookmarks',
        'wrapper_class' => 'fcom_profile_bookmarks',
        'title'         => __('Bookmarks', 'fluent-community'),
        'route'         => [
            'name' => 'bookmarks'
        ]
    ];
    return $profile;
}); 
