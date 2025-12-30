<?php

/**
 * Template Part: Categories Circles
 *
 * Componente de categorias em círculos com design coreano
 * Responsivo com scroll horizontal no mobile
 *
 * @package MimosKorea
 * @since 1.0.0
 */

// Definir as categorias com suas respectivas cores e emojis
$categories = [
    [
        'name' => 'Soju',
        'emoji' => '🍶',
        'color' => '#169485', // Primary
        'url' => '/soju/'
    ],
    [
        'name' => 'Pelúcias',
        'emoji' => '🧸',
        'color' => '#1DC7D4', // Secondary
        'url' => '/pelucias/'
    ],
    [
        'name' => 'Lámen',
        'emoji' => '🍜',
        'color' => '#FFC313', // Yellow
        'url' => '/lamen/'
    ],
    [
        'name' => 'Beleza',
        'emoji' => '💄',
        'color' => '#F44000', // Red
        'url' => '/beleza/'
    ],
    [
        'name' => 'Refrigerantes',
        'emoji' => '🥤',
        'color' => '#0068FD', // Blue
        'url' => '/refrigerantes/'
    ],
    [
        'name' => 'Salgadinhos',
        'emoji' => '🍿',
        'color' => '#6BFF32', // Green
        'url' => '/salgadinhos/'
    ],
    [
        'name' => 'Chaveiros',
        'emoji' => '🔑',
        'color' => '#169485', // Primary
        'url' => '/chaveiros/'
    ],
    [
        'name' => 'Doces',
        'emoji' => '🍭',
        'color' => '#FFC313', // Yellow
        'url' => '/doces/'
    ],
    [
        'name' => 'Biscoitos',
        'emoji' => '🍪',
        'color' => '#F1F0EE', // Tertiary
        'url' => '/biscoitos/'
    ],
    [
        'name' => 'Bebidas',
        'emoji' => '🧃',
        'color' => '#1DC7D4', // Secondary
        'url' => '/bebidas/'
    ]
];
?>

<section class="categories-circles-section full-width-section">
    <div class="content-container">
        <div class="categories-circles-container">
            <div class="categories-circles-track">
                <?php foreach ($categories as $index => $category): ?>
                    <div class="category-circle-item">
                        <a href="<?php echo esc_url($category['url']); ?>" class="category-circle-link">
                            <div class="category-circle" style="background-color: <?php echo esc_attr($category['color']); ?>;">
                                <span class="category-emoji"><?php echo $category['emoji']; ?></span>
                            </div>
                            <span class="category-name"><?php echo esc_html($category['name']); ?></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
