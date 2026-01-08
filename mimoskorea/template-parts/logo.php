<?php

/**
 * Logomarca - Componente Reutilizável
 *
 * Exibe a logomarca da Mimos Korea Design de forma responsiva
 *
 * @package MimosKoreaDesign
 */

// Definir parâmetros do componente
$logo_class = isset($args['class']) ? $args['class'] : 'logo-default';
$logo_size = isset($args['size']) ? $args['size'] : 'medium';
$show_text = isset($args['show_text']) ? $args['show_text'] : true;
$link_home = isset($args['link_home']) ? $args['link_home'] : true;

// Classes CSS baseadas no tamanho
$size_classes = [
    'small' => 'w-32 h-auto', // 128px width
    'medium' => 'w-40 h-auto', // 160px width
    'large' => 'w-48 h-auto', // 192px width
    'xlarge' => 'w-56 h-auto' // 224px width
];

$logo_classes = $size_classes[$logo_size] ?? $size_classes['medium'];
$logo_path = get_template_directory_uri() . '/assets/images/mimoskoreadesign.svg';
?>

<div class="logo-container <?php echo esc_attr($logo_class); ?>">
    <?php if ($link_home): ?>
        <a href="<?php echo esc_url(home_url('/')); ?>"
            class="logo-link inline-block transition-opacity duration-300 hover:opacity-90"
            title="<?php echo esc_attr(get_bloginfo('name')); ?> - Voltar ao início"
            aria-label="Logomarca <?php echo esc_attr(get_bloginfo('name')); ?>">
        <?php endif; ?>

        <img src="<?php echo esc_url($logo_path); ?>"
            alt="<?php echo esc_attr(get_bloginfo('name')); ?>"
            class="logo-image <?php echo esc_attr($logo_classes); ?> max-w-full"
            loading="eager"
            decoding="async">

        <?php if ($show_text && !$link_home): ?>
            <span class="sr-only"><?php echo esc_html(get_bloginfo('name')); ?></span>
        <?php endif; ?>

        <?php if ($link_home): ?>
        </a>
    <?php endif; ?>
</div>
