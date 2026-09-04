/**
 * Use the FluentCommunity post ID as its slug.
 */

function custom_fcom_numeric_post_slug($feed)
{
    if (!is_object($feed) || empty($feed->id)) {
        return;
    }

    $numeric_slug = (string) absint($feed->id);

    if ($numeric_slug === (string) $feed->slug) {
        return;
    }

    $feed->slug = $numeric_slug;
    $feed->save();
}

add_action(
    'fluent_community/feed/created',
    'custom_fcom_numeric_post_slug',
    5
);

/*
 * Also process scheduled posts.
 */
add_action(
    'fluent_community/feed/scheduled',
    'custom_fcom_numeric_post_slug',
    5
);
