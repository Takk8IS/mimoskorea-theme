<?php

defined('ABSPATH') || exit;

$id = isset($args['id']) ? (string) $args['id'] : 'site-search';
$class = isset($args['class']) ? (string) $args['class'] : '';
$placeholder = isset($args['placeholder']) ? (string) $args['placeholder'] : 'Buscar...';

?>

<form class="site-search <?php echo esc_attr($class); ?>" role="search" aria-label="Buscar" onsubmit="return false;">
    <div class="site-search-inner">
        <span class="site-search-icon" aria-hidden="true">
            <i class="ph ph-magnifying-glass"></i>
        </span>
        <input
            id="<?php echo esc_attr($id); ?>"
            class="site-search-input"
            type="search"
            placeholder="<?php echo esc_attr($placeholder); ?>"
            autocomplete="off"
            inputmode="search"
        />
    </div>
</form>

