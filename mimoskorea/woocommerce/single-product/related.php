<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     10.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! isset( $related_products ) || ! is_array( $related_products ) ) {
	$related_products = [];
}

if ( empty( $related_products ) && is_a( $product, WC_Product::class ) ) {
	$related_ids = wc_get_related_products( $product->get_id(), 8, $product->get_upsell_ids() );
	foreach ( $related_ids as $related_id ) {
		$related_product = wc_get_product( $related_id );
		if ( is_a( $related_product, WC_Product::class ) ) {
			$related_products[] = $related_product;
		}
	}
}

if ( $related_products ) :
	if ( function_exists( 'wp_increase_content_media_count' ) ) {
		$content_media_count = wp_increase_content_media_count( 0 );
		if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
			wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
		}
	}

	$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) );

	wc_set_loop_prop( 'name', 'related' );
	wc_set_loop_prop( 'columns', 4 );
	?>

	<section class="related products">
		<?php if ( $heading ) : ?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<div class="related-products-grid">
			<?php foreach ( $related_products as $related_product ) : ?>
				<?php
				if ( ! is_a( $related_product, WC_Product::class ) ) {
					continue;
				}

				$post_object = get_post( $related_product->get_id() );
				if ( ! $post_object ) {
					continue;
				}

				setup_postdata( $GLOBALS['post'] = $post_object );
				wc_get_template_part( 'content', 'product' );
				?>
			<?php endforeach; ?>
		</div>

	</section>
	<?php
endif;

wp_reset_postdata();
