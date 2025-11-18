add_filter('fluent_booking/admin_menu_items', function ($menuItems) {
    foreach ($menuItems as &$menuItem) {
        if ($menuItem['key'] === 'scheduled-events') {
            // Change the URL for the Bookings page
            $menuItem['permalink'] = 'https://dev-rakidhasan.pantheonsite.io/wp-admin/admin.php?page=fluent-booking#/scheduled-events?period=upcoming&author=all&event=all&event_type=all&search=';
        }
    }
    return $menuItems;
});
