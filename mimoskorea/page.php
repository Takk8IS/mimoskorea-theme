<?php
/**
 * Template para páginas estáticas
 *
 * @package MimosKorea
 * @since 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <div class="full-width-section">
        <div class="content-container">
            <?php
            while (have_posts()) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title page-title"><?php the_title(); ?></h1>
                    </header>

                    <div class="entry-content page-content-body">
                        <?php
                        the_content();

                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . esc_html__('Pages:', 'mimoskorea'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div>

                    <?php if (get_edit_post_link()) : ?>
                        <footer class="entry-footer">
                            <?php
                            edit_post_link(
                                sprintf(
                                    wp_kses(
                                        /* translators: %s: Name of current post. Only visible to screen readers */
                                        __('Edit <span class="screen-reader-text">%s</span>', 'mimoskorea'),
                                        array(
                                            'span' => array(
                                                'class' => array(),
                                            ),
                                        )
                                    ),
                                    wp_kses_post(get_the_title())
                                ),
                                '<span class="edit-link">',
                                '</span>'
                            );
                            ?>
                        </footer>
                    <?php endif; ?>
                </article>

                <?php
                // Se os comentários estão abertos ou temos pelo menos um comentário, carregue o template de comentários.
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;

            endwhile; // End of the loop.
            ?>
        </div>
    </div>
</main>

<?php
get_sidebar();
get_footer();