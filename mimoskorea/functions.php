<?php

/**
 * Mimos Korea Theme Functions
 */

if (!defined('ABSPATH')) {
    exit;
}

// Theme setup
function mimoskorea_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // WooCommerce support
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'mimoskorea_setup');

// Enqueue styles and scripts
function mimoskorea_scripts()
{
    // Google Font Inter (única fonte permitida)
    wp_enqueue_style('google-font-inter', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap', array(), null);

    // TailwindCSS 4.1 via CDN (sem build local)
    wp_enqueue_script('tailwindcss', 'https://cdn.tailwindcss.com', array(), null, array('strategy' => 'defer'));
    wp_add_inline_script(
        'tailwindcss',
        'tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#169485",
                        secondary: "#1DC7D4",
                        tertiary: "#F1F0EE",
                        yellow: "#FFC313",
                        green: "#6BFF32",
                        blue: "#0068FD",
                        red: "#F44000",
                        black: "#000000",
                        white: "#ffffff"
                    }
                },
                fontFamily: {
                    sans: ["Inter", "system-ui", "sans-serif"]
                }
            }
        };',
        'after'
    );

    // Estilos do tema (inclui import dos ícones Phosphor)
    wp_enqueue_style('mimoskorea-style', get_stylesheet_uri(), array('google-font-inter'), '1.0.2');

    // Scripts do tema
    wp_enqueue_script('mimoskorea-script', get_template_directory_uri() . '/js/main.js', array(), '1.0.0', array('strategy' => 'defer'));
}
add_action('wp_enqueue_scripts', 'mimoskorea_scripts');

// Register navigation menus
function mimoskorea_menus()
{
    register_nav_menus(array(
        'primary' => 'Menu Principal',
        'footer' => 'Menu Footer',
    ));
}
add_action('init', 'mimoskorea_menus');

// Register widget areas
function mimoskorea_widgets_init()
{
    register_sidebar(array(
        'name'          => 'Sidebar Principal',
        'id'            => 'sidebar-1',
        'description'   => 'Adicione widgets aqui.',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'mimoskorea_widgets_init');

// Disable the new widgets editor (Gutenberg widgets) to prevent wp-editor script conflicts
// This fixes the "wp-editor script should not be enqueued together with the new widgets editor" notice
function mimoskorea_disable_widgets_editor()
{
    remove_theme_support('widgets-block-editor');
}
add_action('after_setup_theme', 'mimoskorea_disable_widgets_editor');

// Remove wp-editor script from widgets page to prevent conflicts
function mimoskorea_remove_wp_editor_widgets()
{
    $screen = get_current_screen();
    if ($screen && $screen->id === 'widgets') {
        wp_dequeue_script('wp-editor');
        wp_deregister_script('wp-editor');
    }
}
add_action('admin_enqueue_scripts', 'mimoskorea_remove_wp_editor_widgets', 100);

// Removido: filtro incorreto em wp_nav_menu_args que não alterava $args
// e podia causar comportamento inesperado. Mantemos apenas o suporte a menus
// e a injeção de ícones via wp_nav_menu_objects.

/**
 * Adiciona suporte para CSS Classes nos menus
 */
function mimoskorea_add_menu_css_classes_support()
{
    add_theme_support('menus');
    add_filter('wp_nav_menu_objects', 'mimoskorea_add_menu_icons', 10, 2);
}
add_action('after_setup_theme', 'mimoskorea_add_menu_css_classes_support');

/**
 * Adiciona ícones Phosphor aos itens do menu baseado nas CSS Classes
 */
function mimoskorea_add_menu_icons($items, $args)
{
    foreach ($items as &$item) {
        $classes = $item->classes;
        if (is_array($classes)) {
            // Procura por classes que começam com 'ph-'
            foreach ($classes as $class) {
                if (strpos($class, 'ph-') === 0) {
                    // Remove a classe ph- das classes do item para evitar duplicação
                    $item->classes = array_diff($item->classes, array($class));
                    // Adiciona o ícone antes do título do menu
                    $icon_class = 'ph ' . $class;
                    $item->title = '<i class="' . esc_attr($icon_class) . '"></i><span>' . $item->title . '</span>';
                    break;
                }
            }
        }
    }
    return $items;
}

/**
 * Algoritmo Inteligente para Produtos Mais Vendidos
 *
 * Obtém os produtos mais vendidos do WooCommerce baseado em:
 * - Total de vendas (peso 60%)
 * - Avaliações e ratings (peso 20%)
 * - Visualizações recentes (peso 10%)
 * - Data de criação (produtos mais novos têm leve vantagem) (peso 10%)
 *
 * @param int $limit Número de produtos a retornar (padrão: 12)
 * @return array Array com IDs dos produtos mais vendidos
 */
function get_best_selling_products(int $limit = 12): array
{
    // Verificar se WooCommerce está ativo
    if (!class_exists('WooCommerce')) {
        return [];
    }

    // Cache da consulta por 1 hora para performance
    $cache_key = 'mimoskorea_best_sellers_' . $limit;
    $cached_result = get_transient($cache_key);

    if ($cached_result !== false) {
        return $cached_result;
    }

    global $wpdb;

    // Query otimizada com LIMIT para performance
    $query = $wpdb->prepare(
        "
        SELECT
            p.ID,
            p.post_date,
            COALESCE(pm_sales.meta_value, 0) as total_sales,
            COALESCE(pm_rating.meta_value, 0) as average_rating,
            COALESCE(pm_rating_count.meta_value, 0) as rating_count,
            COALESCE(pm_views.meta_value, 0) as view_count
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_sales ON (p.ID = pm_sales.post_id AND pm_sales.meta_key = 'total_sales')
        LEFT JOIN {$wpdb->postmeta} pm_rating ON (p.ID = pm_rating.post_id AND pm_rating.meta_key = '_wc_average_rating')
        LEFT JOIN {$wpdb->postmeta} pm_rating_count ON (p.ID = pm_rating_count.post_id AND pm_rating_count.meta_key = '_wc_review_count')
        LEFT JOIN {$wpdb->postmeta} pm_views ON (p.ID = pm_views.post_id AND pm_views.meta_key = '_product_views')
        WHERE p.post_type = 'product'
        AND p.post_status = 'publish'
        ORDER BY total_sales DESC
        LIMIT %d
        ",
        $limit * 2 // Buscar o dobro para scoring
    );

    $products = $wpdb->get_results($query);

    if (empty($products)) {
        // Fallback: produtos mais recentes se não houver dados de vendas
        // Remove meta_query obsoleta de _visibility (depreciado no WooCommerce);
        // confiamos em products publicados por padrão.
        $fallback_query = new WP_Query(array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'orderby' => 'date',
            'order' => 'DESC',
            'fields' => 'ids'
        ));

        $result = $fallback_query->posts;
        set_transient($cache_key, $result, HOUR_IN_SECONDS);
        return $result;
    }

    // Calcular score para cada produto
    $scored_products = [];
    $max_sales = 1;
    $max_rating = 5;
    $max_views = 1;
    $current_time = time();

    // Encontrar valores máximos para normalização
    foreach ($products as $product) {
        $max_sales = max($max_sales, (int)$product->total_sales);
        $max_views = max($max_views, (int)$product->view_count);
    }

    foreach ($products as $product) {
        // Verificar se o produto está visível
        $product_obj = wc_get_product($product->ID);
        if (!$product_obj || !$product_obj->is_visible()) {
            continue;
        }

        $sales_score = ((int)$product->total_sales / $max_sales) * 0.6; // 60%
        $rating_score = ((float)$product->average_rating / $max_rating) * 0.2; // 20%
        $views_score = ((int)$product->view_count / $max_views) * 0.1; // 10%

        // Bonus para produtos mais novos (últimos 6 meses)
        $product_age = $current_time - strtotime($product->post_date);
        $six_months = 6 * 30 * 24 * 60 * 60; // 6 meses em segundos
        $newness_score = ($product_age < $six_months) ?
            (1 - ($product_age / $six_months)) * 0.1 : 0; // 10%

        $total_score = $sales_score + $rating_score + $views_score + $newness_score;

        $scored_products[] = [
            'id' => $product->ID,
            'score' => $total_score,
        ];
    }

    // Ordenar por score (maior para menor)
    usort($scored_products, fn($a, $b) => $b['score'] <=> $a['score']);

    // Extrair apenas os IDs dos produtos
    $result = array_slice(array_column($scored_products, 'id'), 0, $limit);

    // Cache por 1 hora
    set_transient($cache_key, $result, HOUR_IN_SECONDS);

    return $result;
}

/**
 * Limpar cache dos produtos mais vendidos quando um pedido for concluído
 */
function clear_best_sellers_cache($order_id)
{
    // Limpar todos os caches relacionados aos produtos mais vendidos
    for ($i = 1; $i <= 20; $i++) {
        delete_transient('mimoskorea_best_sellers_' . $i);
    }
}
add_action('woocommerce_order_status_completed', 'clear_best_sellers_cache');
add_action('woocommerce_order_status_processing', 'clear_best_sellers_cache');

/**
 * Contar visualizações de produtos para o algoritmo
 */
function track_product_views()
{
    if (is_product()) {
        global $post;
        $views = get_post_meta($post->ID, '_product_views', true);
        $views = $views ? $views : 0;
        update_post_meta($post->ID, '_product_views', $views + 1);
    }
}
add_action('wp_head', 'track_product_views');

/**
 * Substituir placeholder padrão do WooCommerce por placeholder.svg personalizado
 */
function mimoskorea_custom_woocommerce_placeholder($image_html, $size, $dimensions)
{
    // URL do placeholder personalizado
    $placeholder_url = get_template_directory_uri() . '/assets/images/placeholder.svg';

    // Criar HTML da imagem placeholder personalizada
    $custom_placeholder = sprintf(
        '<img src="%s" alt="%s" class="woocommerce-placeholder wp-post-image" width="%d" height="%d" />',
        esc_url($placeholder_url),
        esc_attr__('Placeholder', 'mimoskorea'),
        esc_attr($dimensions['width']),
        esc_attr($dimensions['height'])
    );

    return $custom_placeholder;
}
add_filter('woocommerce_placeholder_img', 'mimoskorea_custom_woocommerce_placeholder', 10, 3);

/**
 * Definir URL do placeholder personalizado para WooCommerce
 */
function mimoskorea_custom_placeholder_src($src)
{
    return get_template_directory_uri() . '/assets/images/placeholder.svg';
}
add_filter('woocommerce_placeholder_img_src', 'mimoskorea_custom_placeholder_src');

/**
 * Definir 5 colunas como padrão para produtos (igual à home)
 */
function mimoskorea_woocommerce_columns()
{
    return 5;
}
add_filter('loop_shop_columns', 'mimoskorea_woocommerce_columns');

/**
 * Remover hooks padrão do WooCommerce que interferem no layout personalizado
 */
function mimoskorea_remove_woocommerce_hooks()
{
    // Remove o wrapper padrão dos produtos
    remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
}
add_action('init', 'mimoskorea_remove_woocommerce_hooks');

/**
 * Remover breadcrumb do WooCommerce em todas as páginas
 * O breadcrumb é removido para manter o layout limpo e consistente
 */
function mimoskorea_remove_woocommerce_breadcrumb()
{
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
}
add_action('init', 'mimoskorea_remove_woocommerce_breadcrumb');

/**
 * Definir tamanhos de imagem personalizados para produtos
 * Garantir que as imagens sejam 1080x1350px sem crop
 */
function mimoskorea_custom_image_sizes()
{
    // Tamanho personalizado para produtos: 1080x1350px sem crop
    add_image_size('product-single', 1080, 1350, false); // false = sem crop
    add_image_size('product-gallery', 1080, 1350, false); // false = sem crop

    // Atualizar tamanhos padrão do WooCommerce
    update_option('woocommerce_single_image_width', 1080);
    update_option('woocommerce_thumbnail_image_width', 1080);
    update_option('woocommerce_gallery_thumbnail_image_width', 150);

    // Desabilitar crop para imagens de produto
    update_option('woocommerce_single_image_crop', 'no');
    update_option('woocommerce_thumbnail_crop', 'no');
}
add_action('after_setup_theme', 'mimoskorea_custom_image_sizes');

/**
 * Forçar uso do tamanho de imagem personalizado na galeria de produtos
 */
function mimoskorea_custom_single_product_image_size($size)
{
    return 'product-single';
}
add_filter('woocommerce_gallery_image_size', 'mimoskorea_custom_single_product_image_size');

/**
 * Personalizar HTML da galeria de imagens do produto
 * Garantir que as imagens mantenham proporção 1080x1350 sem crop
 */
function mimoskorea_custom_product_gallery_image($html, $attachment_id)
{
    $image_url = wp_get_attachment_image_url($attachment_id, 'product-single');
    $image_alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);

    if ($image_url) {
        $html = sprintf(
            '<img src="%s" alt="%s" class="product-gallery-image" loading="lazy" />',
            esc_url($image_url),
            esc_attr($image_alt)
        );
    }

    return $html;
}
add_filter('woocommerce_single_product_image_thumbnail_html', 'mimoskorea_custom_product_gallery_image', 10, 2);

/**
 * Remover product_meta e sku_wrapper das páginas de produto
 * Remove SKU, categorias, tags e outras informações meta do produto
 * para manter o layout limpo e profissional
 */
function mimoskorea_remove_product_meta()
{
    // Remove o SKU do produto
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

    // Remove informações adicionais (SKU, categorias, tags)
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);

    // Remove o wrapper do SKU especificamente
    add_filter('wc_product_sku_enabled', '__return_false');
}
add_action('init', 'mimoskorea_remove_product_meta');

/**
 * Ocultar SKU completamente via CSS e PHP
 * Garantir que o SKU não apareça em nenhum lugar do site
 */
function mimoskorea_hide_sku_completely()
{
    // CSS para ocultar qualquer elemento relacionado ao SKU
    echo '<style>
        .sku_wrapper,
        .sku,
        .product_meta .sku_wrapper,
        .product_meta .sku,
        span.sku_wrapper,
        span.sku,
        .woocommerce div.product .product_meta .sku_wrapper,
        .woocommerce div.product .product_meta .sku {
            display: none !important;
            visibility: hidden !important;
        }
    </style>';
}
add_action('wp_head', 'mimoskorea_hide_sku_completely');

/**
 * Remover SKU do admin e frontend completamente
 * Desabilitar funcionalidade de SKU no WooCommerce
 */
function mimoskorea_disable_sku_functionality()
{
    // Remover campo SKU do admin
    add_filter('wc_product_has_unique_sku', '__return_false');

    // Desabilitar SKU na busca
    add_filter('woocommerce_product_search_fields', function ($fields) {
        return array_diff($fields, array('sku'));
    });

    // Remover SKU dos dados do produto
    add_filter('woocommerce_structured_data_product', function ($markup) {
        if (isset($markup['sku'])) {
            unset($markup['sku']);
        }
        return $markup;
    });
}
add_action('init', 'mimoskorea_disable_sku_functionality');

/**
 * Remover notificações do WooCommerce (woocommerce-notices-wrapper)
 * Remove as notificações que aparecem ao adicionar produtos ao carrinho
 */
function mimoskorea_remove_woocommerce_notices()
{
    // Remove as notificações das páginas de loja
    remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10);

    // Remove as notificações das páginas de produto único
    remove_action('woocommerce_before_single_product', 'woocommerce_output_all_notices', 10);

    // Remove as notificações de outras páginas do WooCommerce
    remove_action('woocommerce_before_main_content', 'woocommerce_output_all_notices', 10);
    remove_action('woocommerce_before_cart', 'woocommerce_output_all_notices', 10);
    remove_action('woocommerce_before_checkout_form', 'woocommerce_output_all_notices', 10);
    remove_action('woocommerce_account_content', 'woocommerce_output_all_notices', 5);

    // Remove também via filtro para garantir que não apareçam
    add_filter('wc_add_to_cart_message_html', '__return_empty_string');
    add_filter('woocommerce_add_to_cart_fragments', 'mimoskorea_remove_cart_fragments');
}
add_action('init', 'mimoskorea_remove_woocommerce_notices');

/**
 * Remove fragmentos de notificação do carrinho via AJAX
 */
function mimoskorea_remove_cart_fragments($fragments)
{
    // Remove o fragmento das notificações
    if (isset($fragments['.woocommerce-notices-wrapper'])) {
        unset($fragments['.woocommerce-notices-wrapper']);
    }

    return $fragments;
}

/**
 * Ocultar completamente as notificações via CSS
 * Garantir que nenhuma notificação apareça visualmente
 */
function mimoskorea_hide_woocommerce_notices_css()
{
    echo '<style>
        .woocommerce-notices-wrapper,
        .woocommerce-message,
        .woocommerce-info,
        .woocommerce-error,
        .wc-block-components-notice-banner,
        .woocommerce .woocommerce-message,
        .woocommerce .woocommerce-info,
        .woocommerce .woocommerce-error,
        div.woocommerce-message,
        div.woocommerce-info,
        div.woocommerce-error {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            overflow: hidden !important;
        }
    </style>';
}
add_action('wp_head', 'mimoskorea_hide_woocommerce_notices_css');

/**
 * Remover botão "Retornar ao carrinho" na página de Checkout (WooCommerce Blocks)
 * Implementação via CSS injetado pelo tema para não alterar plugins/core
 */
function mimoskorea_hide_return_to_cart_on_checkout()
{
    if (function_exists('is_checkout') && is_checkout()) {
        echo '<style>
            .wc-block-components-checkout-return-to-cart-button {
                display: none !important;
                visibility: hidden !important;
            }
        </style>';
    }
}
add_action('wp_head', 'mimoskorea_hide_return_to_cart_on_checkout');

/**
 * Remover texto 'in stock' da página de produto, mantendo 'out of stock' se aplicável
 */
function mimoskorea_remove_in_stock_text($availability, $product)
{
    if ($product->is_in_stock()) {
        $availability['availability'] = '';
    }
    return $availability;
}
add_filter('woocommerce_get_availability', 'mimoskorea_remove_in_stock_text', 10, 2);

/**
 * Remove /produto/ and /categoria-produto/ slugs from WooCommerce URLs.
 *
 * This code ensures that the URLs for products and product categories are clean,
 * removing the default WooCommerce slugs for better SEO and user experience.
 * It handles URL generation and request parsing to prevent 404 errors.
 */

// 1. Remove /produto/ slug from product post type link
add_filter('post_type_link', 'mimoskorea_remove_product_slug', 10, 3);
function mimoskorea_remove_product_slug($post_link, $post, $leavename)
{
    if ('product' !== $post->post_type || 'publish' !== $post->post_status) {
        return $post_link;
    }
    $post_link = str_replace('/produto/', '/', $post_link);
    return $post_link;
}

// 2. Remove /categoria-produto/ slug from product category term link
add_filter('term_link', 'mimoskorea_remove_product_cat_slug', 10, 3);
function mimoskorea_remove_product_cat_slug($term_link, $term, $taxonomy)
{
    if ($taxonomy === 'product_cat') {
        $term_link = str_replace('/categoria-produto/', '/', $term_link);
    }
    return $term_link;
}

// 3. Handle parsing of clean URLs for products and categories.
add_action('parse_request', 'mimoskorea_parse_request_for_clean_urls');
function mimoskorea_parse_request_for_clean_urls(&$wp)
{
    if (isset($wp->request)) {
        $path = trim($wp->request, '/');

        // 1. Check if it's a product first (exact match)
        $post = get_page_by_path($path, OBJECT, 'product');
        if ($post) {
            $wp->query_vars = [
                'name'      => $path,
                'post_type' => 'product',
            ];
            return;
        }

        // 2. Check if it's a product category (exact match)
        $term = get_term_by('slug', $path, 'product_cat');
        if ($term && !is_wp_error($term)) {
            $wp->query_vars = [
                'product_cat' => $path,
            ];
            // Support query param page=X for pagination on clean category URLs
            if (isset($_GET['page'])) {
                $paged = intval($_GET['page']);
                if ($paged > 1) {
                    $wp->query_vars['paged'] = $paged;
                }
            }
            return;
        }

        // 3. Check for pagination (e.g., category-slug/page/2)
        if (preg_match('#(.+?)/page/([0-9]+)/?$#', $path, $matches)) {
            $base_path = $matches[1];
            $paged = intval($matches[2]);

            // Check if the base path is a product category
            $term = get_term_by('slug', $base_path, 'product_cat');
            if ($term && !is_wp_error($term)) {
                $wp->query_vars = [
                    'product_cat' => $base_path,
                    'paged'       => $paged,
                ];
                return;
            }
        }
    }
}

/**
 * Force WooCommerce category pagination links to use ?page=X instead of /page/X/.
 */
add_filter('woocommerce_pagination_args', 'mimoskorea_woocommerce_pagination_args');
function mimoskorea_woocommerce_pagination_args($args)
{
    if (function_exists('is_tax') && is_tax('product_cat')) {
        $term = get_queried_object();
        $base_url = get_term_link($term, 'product_cat');
        if (!is_wp_error($base_url)) {
            $base_url = trailingslashit($base_url);
            $args['base']     = esc_url_raw(add_query_arg('page', '%#%', $base_url));
            $args['format']   = '?page=%#%';
            $args['add_args'] = array();
        }
    }
    return $args;
}

/**
 * Prevent canonical redirects that try to force /page/X on category pages when using ?page=X.
 */
add_filter('redirect_canonical', 'mimoskorea_disable_canonical_for_category_page_param', 10, 2);
function mimoskorea_disable_canonical_for_category_page_param($redirect_url, $requested_url)
{
    if (function_exists('is_tax') && is_tax('product_cat') && isset($_GET['page'])) {
        return false;
    }
    return $redirect_url;
}

// 4. Flush rewrite rules on theme activation.
add_action('after_switch_theme', 'flush_rewrite_rules');

/**
 * Traduções ptBR: sobrescrever strings padrão via filtros sem alterar plugins.
 * - Não modifica arquivos de terceiros.
 * - Funciona para WooCommerce e outros domínios.
 */
add_action('after_setup_theme', function () {
    load_theme_textdomain('mimoskorea', get_template_directory() . '/languages');
});

add_action('wp_enqueue_scripts', 'mimoskorea_enqueue_blocks_ptbr_overrides');
function mimoskorea_enqueue_blocks_ptbr_overrides()
{
    if (function_exists('is_cart') && (is_cart() || is_checkout())) {
        $path = get_template_directory() . '/js/woo-blocks-ptbr.js';
        $uri  = get_template_directory_uri() . '/js/woo-blocks-ptbr.js';
        if (file_exists($path)) {
            wp_enqueue_script('mimoskorea-woo-blocks-ptbr', $uri, array('wp-i18n'), filemtime($path), true);
        }
    }
}

function mimoskorea_force_theme_home_on_front_page($template)
{
    if (is_admin()) {
        return $template;
    }

    $has_search_param = isset($_GET['s']) && trim((string) $_GET['s']) !== '';
    if ((function_exists('is_search') && is_search()) || $has_search_param) {
        return $template;
    }

    $home_path = wp_parse_url(home_url('/'), PHP_URL_PATH) ?: '/';
    $request_path = wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $is_home_path = trailingslashit($request_path) === trailingslashit($home_path);

    $is_home_context = (function_exists('is_front_page') && is_front_page())
        || (function_exists('is_home') && is_home())
        || (function_exists('is_shop') && is_shop())
        || (function_exists('is_post_type_archive') && is_post_type_archive('product'));

    if ($is_home_path && $is_home_context) {
        $forced = locate_template('index.php');
        if (is_string($forced) && $forced !== '') {
            return $forced;
        }
    }

    return $template;
}
add_filter('template_include', 'mimoskorea_force_theme_home_on_front_page', 9999);

function mimoskorea_force_front_page_title($title)
{
    $has_search_param = isset($_GET['s']) && trim((string) $_GET['s']) !== '';
    if ((function_exists('is_search') && is_search()) || $has_search_param) {
        return $title;
    }

    $home_path = wp_parse_url(home_url('/'), PHP_URL_PATH) ?: '/';
    $request_path = wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $is_home_path = trailingslashit($request_path) === trailingslashit($home_path);

    $is_home_context = (function_exists('is_front_page') && is_front_page())
        || (function_exists('is_home') && is_home())
        || (function_exists('is_shop') && is_shop())
        || (function_exists('is_post_type_archive') && is_post_type_archive('product'));

    if ($is_home_path && $is_home_context) {
        return (string) get_bloginfo('name');
    }

    return $title;
}
add_filter('pre_get_document_title', 'mimoskorea_force_front_page_title', 9999);

function mimoskorea_disable_cache_on_search()
{
    if (is_admin()) {
        return;
    }

    $has_search_param = isset($_GET['s']) && trim((string) $_GET['s']) !== '';
    if ((function_exists('is_search') && is_search()) || $has_search_param) {
        if (!defined('DONOTCACHEPAGE')) {
            define('DONOTCACHEPAGE', true);
        }
        nocache_headers();
    }
}
add_action('template_redirect', 'mimoskorea_disable_cache_on_search', 0);
