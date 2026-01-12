<?php

/**
 * Template principal do tema Mimos Korea Design
 *
 * @package MimosKorea
 * @since 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <?php
    if (function_exists('is_search') && is_search()) {
        $search_query = trim((string) get_search_query());
    ?>
        <section class="search-results-section full-width-section">
            <div class="content-container">
                <h1 class="search-results-title">
                    <?php echo esc_html($search_query !== '' ? 'Resultados para: ' . $search_query : 'Resultados'); ?>
                </h1>
                <?php
                if (!class_exists('WooCommerce')) {
                    echo '<p class="search-results-empty">Busca indisponível.</p>';
                } elseif ($search_query === '') {
                    echo '<p class="search-results-empty">Digite algo para buscar.</p>';
                } else {
                    $normalized_query = strtolower(remove_accents($search_query));
                    $raw_tokens = preg_split('/\s+/', $normalized_query) ?: [];
                    $tokens = array_values(array_filter(array_unique(array_map('trim', $raw_tokens)), static fn($t) => strlen($t) >= 3));

                    $taxonomy_term_ids = [];
                    $taxonomy_sources = ['product_cat', 'product_tag'];
                    foreach ($taxonomy_sources as $taxonomy) {
                        $terms = get_terms([
                            'taxonomy' => $taxonomy,
                            'hide_empty' => false,
                            'search' => $normalized_query,
                            'fields' => 'ids',
                            'number' => 20,
                        ]);
                        if (!is_wp_error($terms) && !empty($terms)) {
                            $taxonomy_term_ids[$taxonomy] = array_map('intval', $terms);
                        }
                    }

                    foreach ($tokens as $token) {
                        foreach ($taxonomy_sources as $taxonomy) {
                            $terms = get_terms([
                                'taxonomy' => $taxonomy,
                                'hide_empty' => false,
                                'search' => $token,
                                'fields' => 'ids',
                                'number' => 20,
                            ]);
                            if (!is_wp_error($terms) && !empty($terms)) {
                                $taxonomy_term_ids[$taxonomy] = array_values(array_unique(array_merge($taxonomy_term_ids[$taxonomy] ?? [], array_map('intval', $terms))));
                            }
                        }
                    }

                    $limit = 24;

                    $like_tokens = array_values(array_unique(array_filter(array_merge([$normalized_query], $tokens), static fn($t) => is_string($t) && trim($t) !== '')));
                    global $wpdb;
                    $title_where_filter = static function ($where, $query) use ($wpdb, $like_tokens) {
                        if (!$query instanceof WP_Query || !$query->get('mimoskorea_title_like')) {
                            return $where;
                        }

                        $parts = [];
                        foreach ($like_tokens as $token) {
                            $parts[] = $wpdb->prepare("{$wpdb->posts}.post_title LIKE %s", '%' . $wpdb->esc_like($token) . '%');
                        }

                        if (!empty($parts)) {
                            $where .= ' AND (' . implode(' AND ', $parts) . ')';
                        }

                        return $where;
                    };

                    add_filter('posts_where', $title_where_filter, 10, 2);
                    $title_query = new WP_Query([
                        'post_type' => 'product',
                        'post_status' => 'publish',
                        'posts_per_page' => $limit,
                        'fields' => 'ids',
                        'no_found_rows' => true,
                        'mimoskorea_title_like' => true,
                    ]);
                    remove_filter('posts_where', $title_where_filter, 10);

                    $text_ids = array_map('intval', $title_query->posts ?: []);

                    $tax_ids = [];
                    $tax_query_parts = [];
                    foreach ($taxonomy_term_ids as $taxonomy => $term_ids) {
                        if (!empty($term_ids)) {
                            $tax_query_parts[] = [
                                'taxonomy' => $taxonomy,
                                'field' => 'term_id',
                                'terms' => $term_ids,
                                'operator' => 'IN',
                            ];
                        }
                    }

                    if (!empty($tax_query_parts)) {
                        $tax_query = ['relation' => 'OR'];
                        foreach ($tax_query_parts as $part) {
                            $tax_query[] = $part;
                        }

                        $tax_products = new WP_Query([
                            'post_type' => 'product',
                            'post_status' => 'publish',
                            'posts_per_page' => $limit,
                            'fields' => 'ids',
                            'no_found_rows' => true,
                            'tax_query' => $tax_query,
                        ]);
                        $tax_ids = array_map('intval', $tax_products->posts ?: []);
                    }

                    $candidate_ids = array_values(array_unique(array_merge($text_ids, $tax_ids)));

                    if (empty($candidate_ids)) {
                        echo '<p class="search-results-empty">Nenhum produto encontrado.</p>';
                    } else {
                        $candidate_terms_index = [];
                        if (!empty($taxonomy_term_ids['product_cat'])) {
                            $candidate_terms_index['product_cat'] = array_fill_keys($taxonomy_term_ids['product_cat'], true);
                        }
                        if (!empty($taxonomy_term_ids['product_tag'])) {
                            $candidate_terms_index['product_tag'] = array_fill_keys($taxonomy_term_ids['product_tag'], true);
                        }

                        $scores = [];
                        foreach ($candidate_ids as $product_id) {
                            $title = strtolower(remove_accents((string) get_the_title($product_id)));
                            $content = strtolower(remove_accents((string) get_post_field('post_content', $product_id)));
                            $excerpt = strtolower(remove_accents((string) get_post_field('post_excerpt', $product_id)));

                            $score = 0;
                            if ($title !== '' && str_contains($title, $normalized_query)) {
                                $score += 120;
                            }
                            foreach ($tokens as $token) {
                                if ($title !== '' && str_contains($title, $token)) {
                                    $score += 25;
                                } elseif ($excerpt !== '' && str_contains($excerpt, $token)) {
                                    $score += 10;
                                } elseif ($content !== '' && str_contains($content, $token)) {
                                    $score += 6;
                                }
                            }

                            if (!empty($candidate_terms_index)) {
                                foreach ($candidate_terms_index as $taxonomy => $term_map) {
                                    $product_terms = get_the_terms($product_id, $taxonomy);
                                    if (is_array($product_terms)) {
                                        foreach ($product_terms as $term) {
                                            if (isset($term_map[(int) $term->term_id])) {
                                                $score += ($taxonomy === 'product_cat') ? 30 : 18;
                                                break;
                                            }
                                        }
                                    }
                                }
                            }

                            if ($score > 0) {
                                $scores[$product_id] = $score;
                            }
                        }

                        if (empty($scores)) {
                            echo '<p class="search-results-empty">Nenhum produto encontrado.</p>';
                        } else {
                            arsort($scores);
                            $sorted_ids = array_slice(array_keys($scores), 0, $limit);

                            $final = new WP_Query([
                                'post_type' => 'product',
                                'post_status' => 'publish',
                                'posts_per_page' => $limit,
                                'post__in' => $sorted_ids,
                                'orderby' => 'post__in',
                            ]);

                            if ($final->have_posts()) {
                                echo '<div class="search-results-grid">';
                                while ($final->have_posts()) {
                                    $final->the_post();
                                    wc_get_template_part('content', 'product');
                                }
                                echo '</div>';
                                wp_reset_postdata();
                            } else {
                                echo '<p class="search-results-empty">Nenhum produto encontrado.</p>';
                            }
                        }
                    }
                }
                ?>
            </div>
        </section>
    <?php
    }

    // Incluir o carrossel hero na homepage
    $home_path = wp_parse_url(home_url('/'), PHP_URL_PATH) ?: '/';
    $request_path = wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $is_home_path = trailingslashit($request_path) === trailingslashit($home_path);

    $is_home_context = (function_exists('is_front_page') && is_front_page())
        || (function_exists('is_home') && is_home())
        || (function_exists('is_shop') && is_shop())
        || (function_exists('is_post_type_archive') && is_post_type_archive('product'));

    if (!(function_exists('is_search') && is_search()) && $is_home_path && $is_home_context) {
        get_template_part('banner-carousel');

        // Categorias em círculos abaixo do hero-carousel
        get_template_part('template-parts/categories-circles');

        // Banner promocional abaixo das categorias
        get_template_part('template-parts/banner-50off');

        // Seção Mais Vendidos abaixo do banner
        get_template_part('template-parts/best-sellers');

        // Marquee de texto abaixo da seção Mais Vendidos
        get_template_part('template-parts/text-marquee');

        // Seção Comida e Bebida abaixo do marquee
        get_template_part('template-parts/food-drinks');

        // Banner Biscoitos Orion abaixo da seção Comida e Bebida
        get_template_part('template-parts/banner-biscoitos-orion');

        // Seção Papelaria abaixo do banner
        get_template_part('template-parts/papelaria');

        // Banner Black Friday 2025 abaixo da seção Papelaria
        // get_template_part('template-parts/banner-black-friday-2025');

        // Banner Lojas abaixo da seção Pelúcias
        get_template_part('template-parts/banner-lojas');

        // Seção Pelúcias abaixo do banner
        get_template_part('template-parts/pelucias');

        // Banner Lojas abaixo da seção Pelúcias
        // get_template_part('template-parts/banner-lojas');
    }
    ?>

    <!-- Conteúdo adicional será implementado conforme direção de arte -->
</main>

<?php
get_sidebar();
get_footer();
