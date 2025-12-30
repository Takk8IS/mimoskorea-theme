<?php

/**
 * Cabeçalho do tema Mimos Korea Design
 *
 * @package MimosKorea
 * @since 1.0.0
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <div id="page" class="site">
        <!-- Tarja Promocional -->
        <?php get_template_part('template-parts/promo-banner'); ?>

        <!-- Nav Container -->
        <?php get_template_part('template-parts/nav-container'); ?>

        <header id="masthead" class="site-header bg-white border-b border-gray-100 sticky top-0 z-40">
            <!-- Navegação Principal -->
            <nav class="main-navigation full-width-section" role="navigation" aria-label="<?php esc_attr_e('Primary Navigation', 'mimoskorea'); ?>">
                <div class="content-container">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_class'     => 'main-menu',
                        'container'      => false,
                        'fallback_cb'    => false,
                    ));
                    ?>
                </div>
            </nav>

            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between py-4">
                    <!-- Menu Mobile Toggle -->
                    <button type="button" class="mobile-menu-toggle lg:hidden p-2 rounded-md hover:bg-gray-100 transition-colors" aria-label="Menu" aria-controls="mobile-menu" aria-expanded="false">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Menu Mobile -->
                <div id="mobile-menu" class="mobile-menu hidden lg:hidden border-t border-gray-100 py-4">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_class'     => 'mobile-menu-list space-y-2',
                        'container'      => false,
                        'fallback_cb'    => false,
                    ));
                    ?>
                </div>
            </div>
        </header>
