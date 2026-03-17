<?php

/**
 * Tarja Promocional - Componente
 *
 * Exibe tarja preta fixa no topo de todas as páginas exceto checkout
 *
 * @package MimosKoreaDesign
 */

// Não exibir na página de checkout
if (is_page('checkout') || is_checkout()) {
    return;
}
?>

<div class="promo-banner full-width-section">
    <div class="promo-content content-container">
        <div class="promo-left">
            <a href="<?php echo esc_url(home_url('/lojas')); ?>">
                Visite nossas lojas físicas
            </a>
        </div>
        <div class="promo-right">
            ENVIOS GRÁTIS para pedidos acima de R$ 150
        </div>
    </div>
</div>
