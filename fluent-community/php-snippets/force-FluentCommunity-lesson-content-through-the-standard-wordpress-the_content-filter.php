<?php

/**
 * Force FluentCommunity lesson content through the standard WordPress the_content filter
 * so plugins like CM Tooltip Glossary can process lesson text.
 */

add_filter('fluent_community/course_lesson_api_response', function ($data, $requestData) {
    if (empty($data['lesson']['id']) || !class_exists('\FluentCommunity\Modules\Course\Model\CourseLesson')) {
        return $data;
    }

    $lesson = \FluentCommunity\Modules\Course\Model\CourseLesson::find($data['lesson']['id']);
    if (!$lesson) {
        return $data;
    }

    $content = apply_filters('the_content', $lesson->message ?: '');

    if (class_exists('\FluentCommunity\App\Services\SmartCodeParser')) {
        $user = \FluentCommunity\App\Services\Helper::getCurrentUser();
        $content = (new \FluentCommunity\App\Services\SmartCodeParser())->parse($content, $user);
    }

    $data['lesson']['content'] = $content;

    return $data;
}, 20, 2);

add_filter('fluent_community/course_api_response', function ($data, $requestData) {
    if (
        empty($data['sections']) ||
        !is_array($data['sections']) ||
        !class_exists('\FluentCommunity\Modules\Course\Model\CourseLesson')
    ) {
        return $data;
    }

    $user = class_exists('\FluentCommunity\App\Services\Helper')
        ? \FluentCommunity\App\Services\Helper::getCurrentUser()
        : null;

    foreach ($data['sections'] as &$section) {
        if (empty($section['lessons']) || !is_array($section['lessons'])) {
            continue;
        }

        foreach ($section['lessons'] as &$formattedLesson) {
            if (empty($formattedLesson['id']) || empty($formattedLesson['can_view'])) {
                continue;
            }

            // Only process the lesson whose content is actually loaded.
            if (!empty($formattedLesson['lazy_load'])) {
                continue;
            }

            $lesson = \FluentCommunity\Modules\Course\Model\CourseLesson::find($formattedLesson['id']);
            if (!$lesson) {
                continue;
            }

            $content = apply_filters('the_content', $lesson->message ?: '');

            if (class_exists('\FluentCommunity\App\Services\SmartCodeParser')) {
                $content = (new \FluentCommunity\App\Services\SmartCodeParser())->parse($content, $user);
            }

            $formattedLesson['content'] = $content;
        }
    }

    return $data;
}, 20, 2);
