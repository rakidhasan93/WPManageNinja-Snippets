<?php
/**
 * FluentCommunity: Dark mode only (force dark + hide toggle)
 * Paste in Code Snippets or your theme's functions.php
 */

/**
 * 1) Remove dark/light switch UI from FluentCommunity.
 */
add_filter('fluent_community/has_color_scheme', '__return_false', 999);

/**
 * 2) Also force vars in case scripts read localized settings.
 */
add_filter('fluent_community/general_portal_vars', function ($vars) {
    $vars['default_color'] = 'dark';
    $vars['has_color_scheme'] = false;
    return $vars;
}, 999);

/**
 * 3) Force dark mode before UI paints, and persist in localStorage.
 */
add_action('fluent_community/portal_head', function () {
    ?>
    <script>
    (function () {
        var root = document.documentElement;
        root.classList.remove('light');
        root.classList.add('dark');
        root.setAttribute('data-color-mode', 'dark');

        var key = 'fcom_global_storage';
        var state = {};
        try {
            state = JSON.parse(localStorage.getItem(key) || '{}') || {};
        } catch (e) {
            state = {};
        }

        // Support both keys used by FluentCommunity scripts
        state.fcom_color_mode = 'dark';
        state.colorScheme = 'dark';

        try {
            localStorage.setItem(key, JSON.stringify(state));
        } catch (e) {}
    })();
    </script>
    <?php
}, 1);
