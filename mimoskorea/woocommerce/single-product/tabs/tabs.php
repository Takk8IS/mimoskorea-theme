<?php

/**
 * Single Product tabs - Custom Override for Mimos Korea
 *
 * Stacks content vertically without tab interface.
 * Removes 'display: none' and tab navigation.
 *
 * @package MimosKorea
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 *
 * @see woocommerce_default_product_tabs()
 */
$product_tabs = apply_filters('woocommerce_product_tabs', array());

// Evitar títulos internos duplicados dos templates padrão
add_filter('woocommerce_product_description_heading', '__return_empty_string', 10);
add_filter('woocommerce_product_additional_information_heading', '__return_empty_string', 10);

if (! empty($product_tabs)) : ?>

    <div class="woocommerce-tabs wc-tabs-wrapper mimoskorea-stacked-tabs mt-8">
        <!-- Tab Navigation Removed (ul.tabs) -->

        <?php foreach ($product_tabs as $key => $product_tab) : ?>
            <!--
                Removed 'wc-tab' class to prevent JS from hiding it.
                Added styling for stacked layout.
            -->
            <div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr($key); ?> panel entry-content mb-8" id="tab-<?php echo esc_attr($key); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr($key); ?>">

                <?php if (isset($product_tab['title'])) : ?>
                    <h2 class="text-[1.875rem] font-bold text-black mb-4 font-sans leading-tight">
                        <?php echo wp_kses_post(apply_filters('woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key)); ?>
                    </h2>
                <?php endif; ?>

                <div class="text-[1.125rem] text-black font-sans leading-relaxed">
                    <?php
                    if (isset($product_tab['callback'])) {
                        call_user_func($product_tab['callback'], $key, $product_tab);
                    }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php do_action('woocommerce_product_after_tabs'); ?>
    </div>

    <?php
    // Restaurar filtros para não afetar outros contextos
    remove_filter('woocommerce_product_description_heading', '__return_empty_string', 10);
    remove_filter('woocommerce_product_additional_information_heading', '__return_empty_string', 10);
    ?>

<?php endif; ?>
