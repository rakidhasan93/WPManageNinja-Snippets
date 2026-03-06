<?php
add_filter('fluent_auth/signup_policy_url', function ($policyUrl) {
    // Point to your signup page (or terms section anchor)
    return site_url('/terms-and-conditions');
});
