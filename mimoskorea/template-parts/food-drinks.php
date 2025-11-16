<?php
/**
 * Template Part: Food & Drinks Section
 * 
 * Seção "Comida e Bebida" - Produtos WooCommerce mais recentes
 * da categoria "comida-e-bebida" organizados por data de publicação
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

// Obter os 10 produtos mais recentes da categoria "comida-e-bebida"
$args = array(
    'post_type' => 'product',
    'posts_per_page' => 10,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'tax_query' => array(
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => 'comida-e-bebida',
        ),
    )
);

$food_drinks_query = new WP_Query($args);

// Se não houver produtos, não exibir a seção
if (!$food_drinks_query->have_posts()) {
    // Debug: mostrar informações sobre a query
    echo "<!-- DEBUG: Nenhum produto encontrado na categoria 'comida-e-bebida' -->";
    echo "<!-- Query args: " . print_r($args, true) . " -->";
    echo "<!-- Found posts: " . $food_drinks_query->found_posts . " -->";
    wp_reset_postdata();
    return;
}

// Debug: mostrar que encontrou produtos
echo "<!-- DEBUG: Encontrados " . $food_drinks_query->found_posts . " produtos na categoria 'comida-e-bebida' -->";
?>

<section class="food-drinks-section full-width-section">
    <div class="content-container">
        <!-- Título da seção -->
        <h2 class="food-drinks-title">Comida e Bebida</h2>
        
        <!-- Grid de produtos - 2 linhas × 5 colunas -->
        <div class="food-drinks-grid">
            <?php while ($food_drinks_query->have_posts()) : 
                $food_drinks_query->the_post();
                $product = wc_get_product(get_the_ID());
                
                if (!$product || !$product->is_visible()) {
                    continue;
                }
                
                // Dados do produto
                $product_id = get_the_ID();
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
            
            <article class="food-drinks-item" onclick="window.location.href='<?php echo esc_url($product_link); ?>'">
                <!-- Foto do produto com mask para zoom -->
                <div class="food-drinks-item-photo">
                    <img 
                        src="<?php echo esc_url($product_image_url); ?>" 
                        alt="<?php echo esc_attr($product_image_alt); ?>"
                        loading="lazy"
                        width="448"
                        height="560"
                    >
                </div>
                
                <!-- Informações do produto -->
                <div class="food-drinks-item-info">
                    <!-- Nome do produto -->
                    <h3 class="food-drinks-item-name">
                        <?php echo esc_html($product_name); ?>
                    </h3>
                    
                    <!-- Descrição do produto -->
                    <?php if (!empty($product_description)) : ?>
                    <p class="food-drinks-item-description">
                        <?php echo wp_kses_post($product_description); ?>
                    </p>
                    <?php endif; ?>
                    
                    <!-- Preço do produto -->
                    <div class="food-drinks-item-price">
                        <?php echo $product_price; ?>
                    </div>
                </div>
            </article>
            
            <?php endwhile; ?>
        </div>
    </div>
</section>

<?php
// Restaurar dados globais do post
wp_reset_postdata();
?>