// Increase the limit to 30,000 characters

add_filter('fluent_community/max_post_length', function($max_length) {
    return 30000; // or any value you want
});

// Or make it unlimited (not recommended for database performance)

add_filter('fluent_community/max_post_length', function($max_length) {
    return PHP_INT_MAX; // or a very large number
});
