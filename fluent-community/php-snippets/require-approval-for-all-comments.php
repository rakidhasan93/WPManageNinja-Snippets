<?php

add_filter('fluent_community/comment/comment_data', function ($commentData, $feed) {
  
    // Optional: allow admins / moderators to comment without approval.
    // Remove this block if literally every comment should require approval.
  
    if (!empty($commentData['is_admin']) || current_user_can('manage_options')) {
        return $commentData;
    }

    $commentData['status'] = 'pending';

    if (empty($commentData['meta']) || !is_array($commentData['meta'])) {
        $commentData['meta'] = [];
    }

    $commentData['meta']['reports_count'] = 1;
    $commentData['meta']['auto_flagged'] = 'yes';
    $commentData['meta']['prevent_published'] = 'yes';

    return $commentData;
}, 20, 2);

add_action('fluent_community/comment/new_comment_pending', function ($comment, $feed) {
    if (!class_exists('\FluentCommunityPro\App\Models\Moderation')) {
        return;
    }

    $moderationClass = '\FluentCommunityPro\App\Models\Moderation';

    $existing = $moderationClass::where('parent_id', $comment->id)
        ->where('content_type', 'comment')
        ->first();

    if ($existing) {
        return;
    }

    $report = $moderationClass::create([
        'post_id'       => $feed->id,
        'content_type'  => 'comment',
        'user_id'       => null,
        'parent_id'     => $comment->id,
        'reports_count' => 1,
        'reason'        => 'requires_approval',
        'status'        => 'flagged',
        'explanation'   => 'All comments require approval.',
        'meta'          => [
            'flagged_by' => 'auto'
        ]
    ]);

    do_action('fluent_community/content_moderation/created', $report, $comment);
}, 20, 2);

?>
