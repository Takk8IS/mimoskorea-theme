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
    // Incluir o carrossel hero na homepage
    $home_path = wp_parse_url(home_url('/'), PHP_URL_PATH) ?: '/';
    $request_path = wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $is_home_path = trailingslashit($request_path) === trailingslashit($home_path);

    $is_home_context = (function_exists('is_front_page') && is_front_page())
        || (function_exists('is_home') && is_home())
        || (function_exists('is_shop') && is_shop())
        || (function_exists('is_post_type_archive') && is_post_type_archive('product'));

    if ($is_home_path && $is_home_context) {
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
