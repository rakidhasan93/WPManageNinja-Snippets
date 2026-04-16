<?php
add_filter('fluentform/email_body', 'custom_fluentform_email_rtl', 10, 4);

function custom_fluentform_email_rtl($emailBody, $notification, $submittedData, $form) {
    $emailBody = str_replace(
        ["text-align:left", "text-align: left"],
        ["text-align:right", "text-align: right"],
        $emailBody
    );

    return $emailBody;
}
