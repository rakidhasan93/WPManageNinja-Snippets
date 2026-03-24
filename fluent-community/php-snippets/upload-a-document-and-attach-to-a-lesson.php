<?php

/**
 * REST: Upload a document and attach to a FluentCommunity lesson.
 * POST /wp-json/fcom-custom/v1/lessons/{lesson_id}/documents
 * Form-data: file=<your file>
 */
add_action('rest_api_init', function () {
    register_rest_route('fcom-custom/v1', '/lessons/(?P<lesson_id>\d+)/documents', [
        'methods'             => 'POST',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
        'callback'            => function (\WP_REST_Request $request) {
            if (
                !class_exists('\FluentCommunity\Modules\Course\Model\CourseLesson') ||
                !class_exists('\FluentCommunity\App\Models\Media') ||
                !class_exists('\FluentCommunity\App\Services\Libs\FileSystem')
            ) {
                return new \WP_Error('fcom_missing', 'FluentCommunity classes not available', ['status' => 500]);
            }

            $lessonId = (int) $request->get_param('lesson_id');
            $lesson = \FluentCommunity\Modules\Course\Model\CourseLesson::find($lessonId);
            if (!$lesson) {
                return new \WP_Error('fcom_lesson_not_found', 'Lesson not found', ['status' => 404]);
            }

            $files = $request->get_file_params();
            if (empty($files['file'])) {
                return new \WP_Error('fcom_no_file', 'No file uploaded', ['status' => 400]);
            }

            $allowedMimeTypes = apply_filters('fcom_lesson_document_mimes', [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'text/plain',
                'text/csv',
                'application/zip'
            ]);

            $file = $files['file'];
            $fileType = wp_check_filetype($file['name']);
            if (empty($fileType['type']) || !in_array($fileType['type'], $allowedMimeTypes, true)) {
                return new \WP_Error('fcom_invalid_type', 'File type not allowed', ['status' => 415]);
            }

            if (!function_exists('wp_handle_upload')) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }

            $fs = new \FluentCommunity\App\Services\Libs\FileSystem();
            $file = $fs->_renameFileName($file);

            add_filter('upload_dir', [$fs, '_setCustomUploadDir']);
            $uploaded = wp_handle_upload($file, ['test_form' => false]);
            remove_filter('upload_dir', [$fs, '_setCustomUploadDir']);

            if (isset($uploaded['error'])) {
                return new \WP_Error('fcom_upload_error', $uploaded['error'], ['status' => 500]);
            }

            $media = \FluentCommunity\App\Models\Media::create([
                'object_source' => 'lesson_document',
                'user_id'       => get_current_user_id(),
                'feed_id'       => $lesson->id,
                'sub_object_id' => $lesson->id,
                'is_active'     => 1,
                'media_type'    => $uploaded['type'],
                'driver'        => 'local',
                'media_path'    => $uploaded['file'],
                'media_url'     => $uploaded['url'],
                'settings'      => [
                    'original_name' => $file['name']
                ]
            ]);

            $meta = is_array($lesson->meta) ? $lesson->meta : [];
            $meta['document_lists'] = isset($meta['document_lists']) && is_array($meta['document_lists']) ? $meta['document_lists'] : [];
            $meta['document_ids'] = isset($meta['document_ids']) && is_array($meta['document_ids']) ? $meta['document_ids'] : [];

            $meta['document_lists'][] = $media->getPrivateFileMeta();
            $meta['document_ids'][] = $media->id;

            $lesson->meta = $meta;
            $lesson->save();

            return [
                'message' => 'Document attached to lesson',
                'media'   => $media->getPrivateFileMeta(),
                'lesson'  => [
                    'id'   => $lesson->id,
                    'meta' => $lesson->getPublicLessonMeta(true)
                ]
            ];
        }
    ]);
});
