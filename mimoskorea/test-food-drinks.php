<?php
/**
 * Teste isolado do componente food-drinks
 */

// Simular ambiente WordPress
define('WP_USE_THEMES', true);
require_once('../../../wp-blog-header.php');

echo "<h1>Teste do Componente Food & Drinks</h1>";

// Verificar se WooCommerce está ativo
if (!class_exists('WooCommerce')) {
    echo "<p style='color: red;'>❌ WooCommerce NÃO está ativo!</p>";
    exit;
} else {
    echo "<p style='color: green;'>✅ WooCommerce está ativo</p>";
}

// Testar a query
$args = array(
    'post_type' => 'product',
    'posts_per_page' => 10,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
    'tax_query' => array(
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => 'comida-e-bebida',
        ),
    ),
    'meta_query' => array(
        array(
            'key' => '_stock_status',
            'value' => 'instock',
            'compare' => '='
        )
    )
);

$food_drinks_query = new WP_Query($args);

echo "<h2>Resultado da Query:</h2>";
echo "<p>Produtos encontrados: " . $food_drinks_query->found_posts . "</p>";

if ($food_drinks_query->have_posts()) {
    echo "<p style='color: green;'>✅ Query funcionando - produtos encontrados!</p>";
    echo "<ul>";
    while ($food_drinks_query->have_posts()) {
        $food_drinks_query->the_post();
        echo "<li>" . get_the_title() . " (ID: " . get_the_ID() . ")</li>";
    }
    echo "</ul>";
    wp_reset_postdata();
} else {
    echo "<p style='color: red;'>❌ Nenhum produto encontrado na query!</p>";
    
    // Debug da query
    echo "<h3>Debug da Query:</h3>";
    echo "<pre>";
    print_r($args);
    echo "</pre>";
    
    // Verificar se a categoria existe
    $category = get_term_by('slug', 'comida-e-bebida', 'product_cat');
    if ($category) {
        echo "<p style='color: green;'>✅ Categoria 'comida-e-bebida' existe (ID: {$category->term_id})</p>";
    } else {
        echo "<p style='color: red;'>❌ Categoria 'comida-e-bebida' NÃO existe!</p>";
    }
}

echo "<hr>";
echo "<h2>Incluindo o componente:</h2>";

// Incluir o componente
get_template_part('template-parts/food-drinks');
?>