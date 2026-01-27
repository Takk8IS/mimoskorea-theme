<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility and valid product type.
if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
    return;
}

// Get product data
$product_id = $product->get_id();
$product_name = $product->get_name();
$product_price = $product->get_price_html();
$product_link = get_permalink($product_id);
$product_short_description = $product->get_short_description();
$product_price_value = (float) wc_get_price_to_display($product);
$product_currency = function_exists('get_woocommerce_currency') ? get_woocommerce_currency() : '';
$product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'names'));
$product_categories_value = !empty($product_categories) ? implode('|', array_map('sanitize_text_field', $product_categories)) : '';
$product_sku = $product->get_sku();

// Limit description to 100 characters
if (strlen($product_short_description) > 100) {
    $product_short_description = substr($product_short_description, 0, 100) . '...';
}

// Get product image
$image_id = $product->get_image_id();
if ($image_id) {
    $image_url = wp_get_attachment_image_url($image_id, 'full');
    $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
} else {
    // Use placeholder.svg when no image
    $image_url = get_template_directory_uri() . '/assets/images/placeholder.svg';
    $image_alt = 'Imagem do produto ' . esc_attr($product_name);
}
?>

<article class="store-item" data-product-id="<?php echo esc_attr($product_id); ?>" data-product-sku="<?php echo esc_attr($product_sku); ?>" data-product-name="<?php echo esc_attr($product_name); ?>" data-product-price="<?php echo esc_attr($product_price_value); ?>" data-product-currency="<?php echo esc_attr($product_currency); ?>" data-product-categories="<?php echo esc_attr($product_categories_value); ?>">
    <a href="<?php echo esc_url($product_link); ?>" class="store-item-link">
        <div class="store-item-photo">
            <img src="<?php echo esc_url($image_url); ?>" 
                 alt="<?php echo esc_attr($image_alt); ?>" 
                 loading="lazy">
        </div>
        <div class="store-item-info">
            <h3 class="store-item-name"><?php echo esc_html($product_name); ?></h3>
            <?php if ($product_short_description): ?>
                <p class="store-item-description"><?php echo esc_html($product_short_description); ?></p>
            <?php endif; ?>
            <div class="store-item-price"><?php echo $product_price; ?></div>
        </div>
    </a>
</article>
