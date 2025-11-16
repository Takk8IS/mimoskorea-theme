<?php

/**
 * Banner Carousel Hero - Carrossel horizontal para homepage
 *
 * @package MimosKorea
 * @since 1.0.0
 */

// Definir as imagens do carrossel com seus respectivos links e títulos
$carousel_items = [
    [
        'image' => 'hero-carousel-ofertas-relampago.webp',
        'title' => 'Ofertas Relâmpago',
        'link' => '/ofertas-mimos'
    ],
    [
        'image' => 'hero-carousel-pelucias.webp',
        'title' => 'Pelúcias Coreanas Fofas',
        'link' => '/pelucias'
    ],
    [
        'image' => 'hero-carousel-comida-coreana.webp',
        'title' => 'Comida Coreana Autêntica',
        'link' => '/comida-e-bebida'
    ],
    [
        'image' => 'hero-carousel-canecas.webp',
        'title' => 'Canecas Coreanas Exclusivas',
        'link' => '/lar'
    ],
    [
        'image' => 'hero-carousel-bebida-soju.webp',
        'title' => 'Bebidas Coreanas',
        'link' => '/comida-e-bebida'
    ],
    [
        'image' => 'hero-carousel-mochilas.webp',
        'title' => 'Mochilas e Acessórios',
        'link' => '/moda'
    ]
];
?>

<section id="hero-carousel" class="hero-carousel-section">
    <div class="hero-carousel-container" id="heroCarouselContainer">
        <div class="hero-carousel-track" id="heroCarouselTrack">
            <?php foreach ($carousel_items as $index => $item): ?>
                <div class="hero-carousel-item">
                    <a href="<?php echo esc_url($item['link']); ?>" class="hero-carousel-link" title="<?php echo esc_attr($item['title']); ?>">
                        <img
                            src="<?php echo get_template_directory_uri(); ?>/assets/images/<?php echo esc_attr($item['image']); ?>"
                            alt="<?php echo esc_attr($item['title']); ?>"
                            class="hero-carousel-image"
                            loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>">
                    </a>
                    <h2 class="hero-carousel-title"><?php echo esc_html($item['title']); ?></h2>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
