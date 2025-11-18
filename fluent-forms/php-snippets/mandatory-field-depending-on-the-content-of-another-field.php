add_filter('fluentform/validate_input_item_input_text', function ($errorMessage, $field, $formData, $fields, $form) {
    $fieldName = $field['name'];

    // You can use dropdown/radio field as "optional_field" to control requirement
    
    if ($field['name'] === 'input_text') {
        if (isset($formData['dropdown']) && $formData['dropdown'] === 'Option A') {
            if (empty($formData['input_text'])) {
                return __('This field is required when Option A is selected.', 'fluentform');
            }
        }
    }

    return $errorMessage;
}, 10, 5);
