<?php

add_action('fluent_booking/author_landing_head', function () {
    ?>
    <style>
        /* Standalone booking page wrapper */
        .calendar_wrap {
            height: auto !important;
            min-height: 100vh !important;
            overflow: visible !important;
            padding-bottom: 80px !important;
        }

        /* Main app box */
        .fluent_booking_app,
        .fcal_calendar_inner,
        .fcal_booking_form_wrap {
            overflow: visible !important;
        }

        /* Extra room for bottom notices / validation messages */
        .fcal_booking_form_wrap .fcal_booking_form {
            padding-bottom: 40px !important;
        }
    </style>
    <?php
}, 20);
