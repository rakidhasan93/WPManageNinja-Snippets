add_filter('fluent_community/feed/general_config', function ($config, $feed, $userId) {
    $spaceSlugs = \FluentCommunity\App\Services\FeedsHelper::getSpaceSlugsByUserId($userId);

    // Add a flag to the config to indicate if the user is a member of any spaces
    $config['can_create_post'] = !empty($spaceSlugs);

    return $config;
}, 10, 3);
