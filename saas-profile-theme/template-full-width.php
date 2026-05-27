<?php
/**
 * Template Name: Full Width Page
 */

get_header(); ?>

	<main id="primary" class="site-main bg-color p-64">
        <div class="full-width-container mx-auto">
            <?php
            while ( have_posts() ) :
                the_post();
                the_content();
            endwhile; // End of the loop.
            ?>
        </div>
	</main><!-- #main -->

<?php
get_footer();
