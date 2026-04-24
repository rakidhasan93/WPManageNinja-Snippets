<?php

add_filter('fluentform/email_summary_body_text', function ($text, $submissions) {
	return '';
}, 10, 2);

add_filter('fluentform/email_summary_footer_text', function ($footerText) {
	return '';
});
