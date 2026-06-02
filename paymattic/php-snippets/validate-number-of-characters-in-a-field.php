<?php

/*

One small clarification: “alphanumeric” and “15 digits” are different requirements.

- If you want exactly 15 numbers, use Option A: ^\d{15}$
- If you want exactly 15 letters/numbers, use Option B: ^[A-Za-z0-9]{15}$

*/

add_filter('wppayform/form_submission_validation_errors', function ($errors, $formId, $formattedElements) {
    // Change this to your field's ID
    $field_id = 'your_field_id';

    // Get raw submitted value
    $value = isset($_POST[$field_id]) ? sanitize_text_field(wp_unslash($_POST[$field_id])) : '';

    // Option A: exactly 15 digits
    if (!preg_match('/^\d{15}$/', $value)) {
        $errors[$field_id] = 'Please enter exactly 15 digits.';
    }

    // Option B: exactly 15 alphanumeric characters
    // if (!preg_match('/^[A-Za-z0-9]{15}$/', $value)) {
    //     $errors[$field_id] = 'Please enter exactly 15 letters or numbers.';
    // }

    return $errors;
}, 10, 3);


?>
