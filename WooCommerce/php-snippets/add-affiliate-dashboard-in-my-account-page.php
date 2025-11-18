/ Add new tab to My Account menu

add_filter( 'woocommerce_account_menu_items', function( $items ) {
    $items['affiliate-program'] = __( 'Affiliate Program', 'woocommerce' );
    return $items;
}, 99 );

//remove my account slug from affiliate dashboard link

add_filter( 'woocommerce_get_endpoint_url', 'custom_affiliate_program_tab_url', 10, 4 );
function custom_affiliate_program_tab_url( $url, $endpoint, $value, $permalink ) {

    if ( $endpoint === 'affiliate-program' ) {
        $url = home_url( '/affiliate-program/' ); // your custom page slug
    }

    return $url;
}
