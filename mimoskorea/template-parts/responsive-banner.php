<?php
/**
 * Template Part: Responsive Banner
 * 
 * Componente genérico para banners responsivos
 * Carrega imagem desktop (9516 × 507px) e mobile (3261 × 894px)
 * 
 * @param string $banner_name Nome base do banner (ex: 'lojas', '50off', etc.)
 * @param string $alt_text Texto alternativo para acessibilidade
 * @param string $link_url URL de destino (opcional)
 * @param string $css_class Classes CSS adicionais (opcional)
 */

// Parâmetros padrão
$banner_name = isset($args['banner_name']) ? $args['banner_name'] : '';
$alt_text = isset($args['alt_text']) ? $args['alt_text'] : 'Banner promocional';
$link_url = isset($args['link_url']) ? $args['link_url'] : '';
$css_class = isset($args['css_class']) ? $args['css_class'] : '';

// Validação do parâmetro obrigatório
if (empty($banner_name)) {
    return;
}

// Caminhos das imagens
$desktop_image = get_template_directory_uri() . '/assets/images/banner-' . $banner_name . '-desktop.webp';
$mobile_image = get_template_directory_uri() . '/assets/images/banner-' . $banner_name . '-mobile.webp';

// Classes CSS
$banner_classes = 'responsive-banner-section full-width-section ' . $css_class;
?>

<section class="<?php echo esc_attr($banner_classes); ?>">
    <div class="content-container">
        <?php if (!empty($link_url)) : ?>
            <a href="<?php echo esc_url($link_url); ?>" class="responsive-banner-link">
        <?php endif; ?>
        
        <picture class="responsive-banner-picture">
            <!-- Imagem para mobile (max-width: 768px) -->
            <source media="(max-width: 768px)" srcset="<?php echo esc_url($mobile_image); ?>">
            
            <!-- Imagem para desktop (padrão) -->
            <img 
                src="<?php echo esc_url($desktop_image); ?>" 
                alt="<?php echo esc_attr($alt_text); ?>"
                class="responsive-banner-image"
                loading="lazy"
            >
        </picture>
        
        <?php if (!empty($link_url)) : ?>
            </a>
        <?php endif; ?>
    </div>
</section>