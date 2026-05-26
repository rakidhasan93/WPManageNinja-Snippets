<?php

add_action('wp_footer', function () {
    if (is_admin()) {
        return;
    }
    ?>
    <script>
    (function () {
        function setFieldValue(selector, value) {
            if (!value) return false;

            var field = document.querySelector(selector);
            if (!field) return false;

            field.value = value;
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('change', { bubbles: true }));
            return true;
        }

        function applyFluentBookingPrefill() {
            var params = new URLSearchParams(window.location.search);

            var name    = params.get('invitee_name') || params.get('name') || '';
            var email   = params.get('invitee_email') || params.get('email') || '';
            var phone   = params.get('phone') || '';
            var address = params.get('address') || '';

            setFieldValue('input[name="name"]', name);
            setFieldValue('input[name="email"]', email);
            setFieldValue('input[name="phone_number"]', phone);
            setFieldValue('textarea[name="address"]', address);

            // Optional: custom booking fields
            // Example URL params:
            // custom_company=Acme
            // custom_notes=Hello
            params.forEach(function (value, key) {
                if (key.indexOf('custom_') !== 0) {
                    return;
                }

                setFieldValue('[name="' + key + '"]', value);
                setFieldValue('textarea[name="' + key + '"]', value);
                setFieldValue('input[name="' + key + '"]', value);
            });
        }

        function runPrefill() {
            applyFluentBookingPrefill();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', runPrefill);
        } else {
            runPrefill();
        }

        var observer = new MutationObserver(function () {
            applyFluentBookingPrefill();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    })();
    </script>
    <?php
});
