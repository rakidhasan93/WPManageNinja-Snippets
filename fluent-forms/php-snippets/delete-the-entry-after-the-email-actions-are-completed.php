<?php

add_action('fluentform/global_notify_completed', function ($entryId, $form) {
    // Change this to your form ID
    if ((int) $form->id !== 123) {
        return;
    }

    $submission = wpFluent()->table('fluentform_submissions')->find($entryId);

    if (!$submission || empty($submission->response)) {
        return;
    }

    $response = json_decode($submission->response, true);

    // Change this to your File Upload field name
    $uploadFieldName = 'file_upload';

    if (empty($response[$uploadFieldName])) {
        return;
    }

    $files = (array) $response[$uploadFieldName];
    $uploadDir = wp_upload_dir();

    foreach ($files as $fileUrl) {
        $filePath = str_replace($uploadDir['baseurl'], $uploadDir['basedir'], $fileUrl);

        if (is_readable($filePath) && !is_dir($filePath)) {
            wp_delete_file($filePath);
        }
    }

    // Optional: delete the entry after the email/actions are completed
    // wpFluent()->table('fluentform_submissions')->where('id', $entryId)->delete();

}, 999, 2);

?>
