<?php

/**
 * Nav Container - Barra de Navegação Principal
 *
 * Contém logomarca (esquerda) e ícones de navegação (direita)
 * Ícones: Pesquisar, Minha Conta, Carrinho WooCommerce
 *
 * @package MimosKoreaDesign
 */

// Verificar se WooCommerce está ativo
$woocommerce_active = class_exists('WooCommerce');
$cart_count = $woocommerce_active ? WC()->cart->get_cart_contents_count() : 0;
?>

<nav class="nav-container full-width-section">
    <div class="nav-content">
        <!-- Logomarca (Esquerda) -->
        <div class="nav-logo-wrapper">
            <?php
            get_template_part('template-parts/logo', null, [
                'class' => 'nav-logo-component',
                'size' => 'small',
                'show_text' => true,
                'link_home' => true
            ]);
            ?>
        </div>

        <!-- Ícones de Navegação (Direita) -->
        <div class="nav-icons">
            <!-- Ícone de Pesquisa -->
            <a href="#" class="nav-icon-link" aria-label="Pesquisar produtos" title="Pesquisar">
                <svg class="nav-icon" fill="currentColor" viewBox="0 0 256 256">
                    <path d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"></path>
                </svg>
            </a>

            <!-- Ícone Minha Conta -->
            <?php if (is_user_logged_in()): ?>
                <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"
                    class="nav-icon-link"
                    aria-label="Minha conta"
                    title="Minha Conta">
                    <svg class="nav-icon" fill="currentColor" viewBox="0 0 256 256">
                        <path d="M230.92,212c-15.23-26.33-38.7-45.21-66.09-54.16a72,72,0,1,0-73.66,0C63.78,166.78,40.31,185.66,25.08,212a8,8,0,1,0,13.85,8c18.84-32.56,52.14-52,89.07-52s70.23,19.44,89.07,52a8,8,0,1,0,13.85-8ZM72,96a56,56,0,1,1,56,56A56.06,56.06,0,0,1,72,96Z"></path>
                    </svg>
                </a>
            <?php else: ?>
                <a href="<?php echo esc_url(wp_login_url()); ?>"
                    class="nav-icon-link"
                    aria-label="Fazer login"
                    title="Login">
                    <svg class="nav-icon" fill="currentColor" viewBox="0 0 256 256">
                        <path d="M141.66,133.66l-40,40A8,8,0,0,1,90.34,162.34L124.69,128,90.34,93.66a8,8,0,0,1,11.32-11.32l40,40A8,8,0,0,1,141.66,133.66ZM192,32H136a8,8,0,0,0,0,16h56V208H136a8,8,0,0,0,0,16h56a16,16,0,0,0,16-16V48A16,16,0,0,0,192,32Z"></path>
                    </svg>
                </a>
            <?php endif; ?>

            <!-- Ícone Carrinho WooCommerce -->
            <?php if ($woocommerce_active): ?>
                <a href="<?php echo esc_url(wc_get_cart_url()); ?>"
                    class="nav-icon-link cart-link"
                    aria-label="Carrinho de compras (<?php echo esc_attr($cart_count); ?> itens)"
                    title="Carrinho">
                    <div class="relative">
                        <svg class="nav-icon" fill="currentColor" viewBox="0 0 256 256">
                            <path d="M222.14,58.87A8,8,0,0,0,216,56H54.68L49.79,29.14A16,16,0,0,0,34.05,16H16a8,8,0,0,0,0,16H34.05l31.1,165.14A16,16,0,0,0,80.9,208H208a8,8,0,0,0,0-16H80.9L77.22,172H188.1a16,16,0,0,0,15.75-13.14L216.73,64.48A8,8,0,0,0,222.14,58.87ZM188.1,156H73.84l-6.22-33.14L185.22,122.86a8,8,0,0,0,7.65-6.14L206.37,72H59.18l14.81,78.86A8,8,0,0,0,81.79,156Z"></path>
                            <circle cx="80" cy="216" r="16"></circle>
                            <circle cx="184" cy="216" r="16"></circle>
                        </svg>
                        <?php if ($cart_count > 0): ?>
                            <span class="cart-count absolute -top-2 -right-2 text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
                                <?php echo esc_html($cart_count > 9 ? '9+' : $cart_count); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
