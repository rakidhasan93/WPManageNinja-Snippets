// Hide "Lost your password?" link on login page (keep Register link visible)
        
        add_action('login_head', function () {
            ?>
            <style>
                #login #nav a[href*="lostpassword"] {
                    display: none !important;
                }
            </style>
            <?php
        });

        // Remove the separator (|) between Register and Lost Password links
        
        add_filter('login_link_separator', function ($separator) {
            return '';
        });
