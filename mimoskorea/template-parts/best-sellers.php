<?php

/**
 * Template Part: Best Sellers Section
 *
 * Seção "Mais Vendidos" - Produtos WooCommerce mais vendidos
 * automaticamente gerados por algoritmo inteligente baseado nas vendas
 *
 * Layout: 2 linhas × 5 colunas (10 produtos total)
 * Imagens: Originais 1080×1350px redimensionadas (JAMAIS crop) mantendo qualidade
 * Efeitos hover: Foto (zoom com mask), Texto/preço (mudança para cor primary)
 * Sem: bordas, shadows ou outros efeitos visuais
 *
 * @package MimosKorea
 * @since 1.0.0
 */

// Verificar se WooCommerce está ativo
if (!class_exists('WooCommerce')) {
    return;
}

// Obter os 15 produtos mais vendidos usando o algoritmo inteligente
$best_selling_ids = get_best_selling_products(15);

// Se não houver produtos, não exibir a seção
if (empty($best_selling_ids)) {
    return;
}
?>

<section class="best-sellers-section full-width-section">
    <div class="content-container">
        <!-- Título da seção -->
        <h2 class="best-sellers-title">Mais Vendidos</h2>

        <!-- Grid de produtos - 2 linhas × 5 colunas -->
        <div class="best-sellers-grid">
            <?php foreach ($best_selling_ids as $product_id) :
                $product = wc_get_product($product_id);
                if (!$product || !$product->is_visible()) {
                    continue;
                }

                // Dados do produto
                $product_name = $product->get_name();
                $product_description = $product->get_short_description();
                $product_price = $product->get_price_html();
                $product_link = get_permalink($product_id);
                $product_image = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'full');
                $product_image_url = $product_image ? $product_image[0] : wc_placeholder_img_src('full');
                $product_image_alt = get_post_meta(get_post_thumbnail_id($product_id), '_wp_attachment_image_alt', true);

                // Se não houver alt text, usar o nome do produto
                if (empty($product_image_alt)) {
                    $product_image_alt = $product_name;
                }

                // Limitar descrição se muito longa
                if (strlen($product_description) > 120) {
                    $product_description = substr($product_description, 0, 120) . '...';
                }
            ?>

                <article class="store-item" onclick="window.location.href='<?php echo esc_url($product_link); ?>'">
                    <!-- Foto do produto com mask para zoom -->
                    <div class="store-item-photo">
                        <img
                            src="<?php echo esc_url($product_image_url); ?>"
                            alt="<?php echo esc_attr($product_image_alt); ?>"
                            loading="lazy"
                            width="448"
                            height="560">
                    </div>

                    <!-- Informações do produto -->
                    <div class="store-item-info">
                        <!-- Nome do produto -->
                        <h3 class="store-item-name">
                            <?php echo esc_html($product_name); ?>
                        </h3>

                        <!-- Descrição do produto -->
                        <?php if (!empty($product_description)) : ?>
                            <p class="store-item-description">
                                <?php echo wp_kses_post($product_description); ?>
                            </p>
                        <?php endif; ?>

                        <!-- Preço do produto -->
                        <div class="store-item-price">
                            <?php echo $product_price; ?>
                        </div>
                    </div>
                </article>

            <?php endforeach; ?>
        </div>
    </div>
</section>
