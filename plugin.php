<?php
/*
    Plugin Name: Disable Variable Product Price Range & Show Default Variation Price Woocommerce
    Description: With this snippet you will be able to hide the price range and display default variation price. Works with sale prices.
    Version: 1.0
    Author: vWare
	Author URI: https://vware.org/
*/

add_filter( 'woocommerce_variable_price_html', 'custom_variable_displayed_price', 10, 2 );

function custom_variable_displayed_price( $price_html, $product ) {
    if ( ! ( is_shop() || is_product_category() || is_product_tag() ) )
        return $price_html;

    $default_attributes = $product->get_default_attributes();
    foreach($product->get_available_variations() as $variation){
        $found = true;
        foreach( $variation['attributes'] as $key => $value ){
            $taxonomy = str_replace( 'attribute_', '', $key );
            if( isset($default_attributes[$taxonomy]) && $default_attributes[$taxonomy] != $value ){
                $found = false;
                break;
            }
        }
        if( $found ) {
            $default_variaton = $variation;
            break;
        }
        else {
            continue;
        }
    }

    if( ! isset($default_variaton) )
        $price_html;

    if ( $default_variaton['display_price'] !== $default_variaton['display_regular_price'] && $product->is_on_sale()) {
        $price_html = '<del>' . wc_price($default_variaton['display_regular_price']) . '</del> <ins>' . wc_price($default_variaton['display_price']) . '</ins>';
    } else {
        $price_html = wc_price($default_variaton['display_price']);
    }
    return $price_html;
}