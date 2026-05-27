<?php
/**
 * Template Name: Clean Layout (Distraction-Free)
 */

get_header(); ?>

<main id="clean-layout" class="site-main bg-color p-64">
    <div class="clean-layout-container mx-auto">
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('clean-layout-article bg-subtle shadow-soft radius-lg p-64'); ?>>
                <header class="clean-header clean-layout-header mb-40 text-center">
                    <?php the_title( '<h1 class="clean-title clean-layout-title text-5xl font-black tracking-tight">', '</h1>' ); ?>
                </header>

                <div class="clean-content clean-layout-content lh-1-8 text-lg color-light">
                    <?php the_content(); ?>
                </div>
            </article>
            <?php
        endwhile;
        ?>
    </div>
</main>

<style>
    body { background-color: #f9f9f9; }
    .clean-content img { max-width: 100%; height: auto; border-radius: 8px; margin-bottom: 24px; }
</style>

<?php get_footer(); ?>
