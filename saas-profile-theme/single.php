<?php
/**
 * The template for displaying all single posts.
 */

get_header(); ?>

<main id="primary" class="site-main blog-main">
    <div class="container-standard mx-auto">
        <div class="blog-layout">
            <!-- Article Content -->
            <article id="post-<?php the_ID(); ?>" <?php post_class('blog-post-single'); ?>>
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

                    <header class="post-header mb-40">
                        <div class="post-meta mb-15">
                            <span class="meta-category"><?php the_category(', '); ?></span>
                            <span class="meta-divider">•</span>
                            <span class="meta-date"><?php echo get_the_date(); ?></span>
                        </div>
                        <h1 class="post-title text-5xl font-black ls-neg-2 mb-20"><?php the_title(); ?></h1>
                        <div class="post-author-bar flex flex-center gap-15">
                            <?php echo get_avatar( get_the_author_meta( 'ID' ), 40, '', '', array('class'=>'radius-full') ); ?>
                            <span class="author-name font-bold">By <?php the_author(); ?></span>
                        </div>
                    </header>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="post-thumbnail-wrapper mb-40 radius-24 overflow-hidden shadow-lg">
                            <?php the_post_thumbnail('large', ['class' => 'full-width object-cover', 'loading' => 'lazy']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="post-content entry-content">
                        <?php the_content(); ?>
                    </div>

                    <footer class="post-footer mt-60 pt-40 border-t-only">
                        <div class="post-tags mb-30">
                            <?php the_tags('<span class="tags-label font-bold">Tags: </span>', ' '); ?>
                        </div>

                        <!-- Post Navigation -->
                        <nav class="post-navigation flex-between gap-20">
                            <div class="nav-previous"><?php previous_post_link('%link', '← Previous Strategy'); ?></div>
                            <div class="nav-next"><?php next_post_link('%link', 'Next Strategy →'); ?></div>
                        </nav>

                        <!-- Comments Section -->
                        <?php if ( comments_open() || get_comments_number() ) :
                            comments_template();
                        endif; ?>
                    </footer>

                <?php endwhile; endif; ?>
            </article>

            <!-- Sidebar -->
            <?php get_sidebar(); ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
