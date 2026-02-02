add_filter('fluentform/currency_symbol', function($currency_symbol, $currency) {
    if ($currency === 'AED') {
        return 'AED '; // or 'د.إ' if you want the Arabic characters
    }
    return $currency_symbol;
}, 10, 2);
