<?php
/**
 * Template for displaying search forms
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label>
        <span class="screen-reader-text">Pesquisar por:</span>
        <input type="search" class="search-field" placeholder="Pesquisar..." value="<?php echo get_search_query(); ?>" name="s" />
    </label>
    <input type="submit" class="search-submit" value="Pesquisar" />
</form>