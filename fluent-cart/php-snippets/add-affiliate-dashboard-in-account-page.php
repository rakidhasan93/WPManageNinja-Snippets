add_action('init', function () {
    \FluentCart\Api\FluentCartGeneralApi::getInstance()->addCustomerDashboardEndpoint(
        'affiliate', [
            'title' => __('Affiliate Dashboard', 'fluent-cart-pro'),
            // 'render_callback' => function () {
            //     echo 'Put your text';
            // },
            'page_id' => 6,
        ]
    );
});
