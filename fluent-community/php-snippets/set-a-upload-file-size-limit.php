add_filter('fluent_community/media_upload_max_file_size', function () {
    return 5; // 5 MB
});
add_filter('fluent_community/media_upload_max_file_unit', function () {
    return 'MB'; // 'KB', 'MB', or 'GB'
});
