<?php

add_filter('upload_mimes', function ($mimes) {
    $mimes['dcm'] = 'application/dicom';
    $mimes['dicom'] = 'application/dicom';

    return $mimes;
});

add_filter('fluentform/file_upload_validations', function ($validations, $form) {
    // Change to your form ID.
    if ((int) $form->id !== 123) {
        return $validations;
    }

    [$rules, $messages] = $validations;

    // Change this to your Fluent Forms file upload field name.
    $fieldName = 'dicom_upload';

    if (!empty($rules[$fieldName])) {
        if (preg_match('/(^|\|)mimes:([^|]*)/', $rules[$fieldName])) {
            $rules[$fieldName] = preg_replace_callback('/(^|\|)mimes:([^|]*)/', function ($matches) {
                $existing = array_filter(array_map('trim', explode(',', $matches[2])));
                $existing[] = 'dcm';
                $existing[] = 'dicom';
                $existing = array_unique($existing);

                return $matches[1] . 'mimes:' . implode(',', $existing);
            }, $rules[$fieldName]);
        } else {
            $rules[$fieldName] .= '|mimes:dcm,dicom';
        }
    }

    return [$rules, $messages];
}, 10, 2);

?>
