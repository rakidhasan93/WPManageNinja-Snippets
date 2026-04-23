<?php

/**
 * Send new comment emails to all active members of the Space.
 * Add in Code Snippets or your theme's functions.php
 */

add_action('fluent_community/comment_added', function ($comment, $feed, $mentionedUsers = []) {
    if (
        !class_exists('\FluentCommunity\App\Models\User') ||
        !class_exists('\FluentCommunity\App\Models\SpaceUserPivot') ||
        !class_exists('\FluentCommunity\App\Services\ProfileHelper') ||
        !class_exists('\FluentCommunity\App\Services\Libs\Mailer')
    ) {
        return;
    }

    if (!$comment || !$feed || empty($feed->space_id)) {
        return;
    }

    $userIds = \FluentCommunity\App\Models\SpaceUserPivot::where('space_id', $feed->space_id)
        ->where('status', 'active')
        ->pluck('user_id')
        ->toArray();

    if (!$userIds) {
        return;
    }

    $userIds = array_values(array_unique(array_diff($userIds, [(int) $comment->user_id])));

    if (!$userIds) {
        return;
    }

    $users = \FluentCommunity\App\Models\User::query()
        ->whereIn('ID', $userIds)
        ->whereHas('xprofile', function ($query) {
            $query->where('status', 'active');
        })
        ->get();

    if ($users->isEmpty()) {
        return;
    }

    $emailBody = $comment->getCommentHtml(true);
    $emailSubject = $comment->getEmailSubject($feed);
    $feedPermalink = $feed->getPermalink() . '?comment_id=' . $comment->id;

    foreach ($users as $user) {
        $newEmailBody = str_replace(
            ['##feed_permalink##', '##email_notification_url##'],
            [
                \FluentCommunity\App\Services\ProfileHelper::signUserUrlWithAuthHash($feedPermalink, $user->ID),
                \FluentCommunity\App\Services\ProfileHelper::getSignedNotificationPrefUrl($user->ID)
            ],
            $emailBody
        );

        $mailer = new \FluentCommunity\App\Services\Libs\Mailer('', $emailSubject, $newEmailBody);
        $mailer->to($user->user_email, $user->display_name);
        $mailer->send();
    }
}, 50, 3);
