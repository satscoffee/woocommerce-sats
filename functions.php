<?php
/*
 * Generated By Satoshi Coffee Co. using OpenAI
 * If you find value with this code, please tip me some sats at tips@btcpay.sats.coffee
 * You can also purchase delicious coffee with bitcoin at https://sats.coffee
 *
 * To use, copy the code below these comments and add to your wordpress Child theme's function.php file.
 * You must use woocommerce as your store plugin and obviously a bitcoin payment gateway like btcpayserver.
 * Note this does not change the shipping nor tax, which will both be shown in fiat.
 * Enjoy!
 */
 
function get_bitcoin_exchange_rate() {
    $api_url = 'https://blockchain.info/ticker';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => true
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $json_data = json_decode($response, true);
    return $json_data['USD']['last'];
}

function convert_to_bitcoin($amount) {
    $exchange_rate = get_bitcoin_exchange_rate();
    $bitcoin_amount = $amount / $exchange_rate;
    $satoshi_amount = $bitcoin_amount * 100000000;
    return number_format($satoshi_amount, 0, '.', ',') . ' sats'; // Display Bitcoin amount up to 8 decimal places
}

// Convert product price to Bitcoin
add_filter('woocommerce_get_price_html', 'display_price_in_bitcoin', 10, 2);
function display_price_in_bitcoin($price, $product) {
    $bitcoin_price = convert_to_bitcoin($product->get_price());
    return '<span class="woocommerce-Price-amount amount">' . $bitcoin_price . '</span>';
}

// Convert subtotal to Bitcoin
add_filter('woocommerce_cart_subtotal', 'display_subtotal_in_bitcoin');
function display_subtotal_in_bitcoin($subtotal) {
    $bitcoin_subtotal = convert_to_bitcoin(WC()->cart->subtotal);
    return '<span class="woocommerce-Price-amount amount">' . $bitcoin_subtotal . '</span>';
}

// Convert total to Bitcoin
add_filter('woocommerce_cart_total', 'display_total_in_bitcoin');
function display_total_in_bitcoin($total) {
    $bitcoin_total = convert_to_bitcoin(WC()->cart->total);
    return '<span class="woocommerce-Price-amount amount">' . $bitcoin_total . '</span>';
}

function woocommerce_template_single_price() {
    global $product;
    $price = $product->get_price();
    $bitcoin_price = convert_to_bitcoin($price);
    ?>
    <span class="price"><?php echo $bitcoin_price; ?></span>
    <?php
}

// Convert cart item price to Bitcoin
add_filter('woocommerce_cart_item_price', 'display_cart_item_price_in_bitcoin', 10, 3);
function display_cart_item_price_in_bitcoin($product_price, $cart_item, $cart_item_key) {
    $bitcoin_price = convert_to_bitcoin($cart_item['data']->get_price());
    return '<span class="woocommerce-Price-amount amount">' . $bitcoin_price . '</span>';
}

// Convert cart subtotal price to Bitcoin
add_filter('woocommerce_cart_item_subtotal', 'display_product_subtotal_in_bitcoin', 10, 3);
function display_product_subtotal_in_bitcoin($product_subtotal, $cart_item, $cart_item_key) {
    $bitcoin_price = convert_to_bitcoin($cart_item['data']->get_price() * $cart_item['quantity']);
    return '<span class="product-subtotal">' . $bitcoin_price . '</span>';
}
