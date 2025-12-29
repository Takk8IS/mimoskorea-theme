<?php

defined('ABSPATH') || exit;

global $product;

if (! is_a($product, WC_Product::class)) {
	return;
}

$related_ids = wc_get_related_products($product->get_id(), 8, $product->get_upsell_ids());
$related_products = array();

foreach ($related_ids as $related_id) {
	$related_product = wc_get_product($related_id);

	if (! is_a($related_product, WC_Product::class)) {
		continue;
	}

	$related_products[] = $related_product;
}

if (empty($related_products)) {
	return;
}

$heading = apply_filters('woocommerce_product_related_products_heading', __('Related products', 'woocommerce'));

wc_set_loop_prop('name', 'related');
wc_set_loop_prop('columns', 4);

?>

<section class="related products">
	<?php if ($heading) : ?>
		<h2><?php echo esc_html($heading); ?></h2>
	<?php endif; ?>

	<div class="related-products-grid">
		<?php foreach ($related_products as $related_product) : ?>
			<?php
			if (! is_a($related_product, WC_Product::class)) {
				continue;
			}

			$post_object = get_post($related_product->get_id());

			if (! $post_object) {
				continue;
			}

			setup_postdata($GLOBALS['post'] = $post_object);

			wc_get_template_part('content', 'product');
			?>
		<?php endforeach; ?>
	</div>
</section>

<?php wp_reset_postdata(); ?>
