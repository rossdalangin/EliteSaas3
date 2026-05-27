<?php
/**
 * Template Name: Legal (Terms, Privacy)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<main id="legal-page" class="site-main bg-color p-64">
    <div class="legal-main-container mx-auto">
        <div class="legal-inner-card bg-subtle shadow-soft radius-lg p-64">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <header class="entry-header mb-60 text-center">
                    <?php the_title( '<h1 class="entry-title text-6xl font-black tracking-tight mb-20">', '</h1>' ); ?>
                    <p class="color-light font-bold">Last Updated: <?php echo get_the_modified_date(); ?></p>
                </header>

                <div class="entry-content lh-1-8 text-lg color-light">
                    <?php the_content(); ?>
                </div>
            <?php endwhile; endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
