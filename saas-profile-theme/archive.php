<?php
/**
 * The template for displaying archive pages.
 */

get_header(); ?>

<main id="primary" class="site-main blog-archive-main">
    <div class="container-standard mx-auto">
        <header class="archive-header text-center mb-60">
            <h1 class="archive-title text-6xl font-black ls-neg-3 mb-20">
                <?php the_archive_title(); ?>
            </h1>
            <?php the_archive_description( '<div class="archive-description color-light text-xl">', '</div>' ); ?>
        </header>

        <div class="blog-layout">
            <div class="posts-feed">
                <?php if ( have_posts() ) : ?>
                    <div class="grid-archive gap-40">
                        <?php while ( have_posts() ) : the_post(); ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('archive-post-card bg-white radius-24 overflow-hidden border-light shadow-sm hover-lift'); ?>>
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <a href="<?php the_permalink(); ?>" class="archive-post-thumb-link">
                                        <div class="archive-post-thumb h-220 overflow-hidden">
                                            <?php the_post_thumbnail('large', ['class' => 'full-width full-height object-cover', 'loading' => 'lazy']); ?>
                                        </div>
                                    </a>
                                <?php endif; ?>

                                <div class="p-30">
                                    <div class="post-meta mb-15 text-xs text-uppercase ls-1 font-bold color-primary flex-between">
                                        <span><?php the_category(', '); ?></span>
                                        <span class="color-lighter"><?php echo get_the_date(); ?></span>
                                    </div>
                                    <h2 class="post-title text-2xl font-black mb-15">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    <div class="post-excerpt color-light mb-24 text-sm lh-1-6">
                                        <?php echo wp_trim_words(get_the_excerpt(), 25); ?>
                                    </div>
                                    <div class="flex-between flex-center mt-auto pt-20 border-light-top">
                                        <a href="<?php the_permalink(); ?>" class="font-black text-sm color-dark">READ STRATEGY →</a>
                                        <span class="text-xs color-lighter"><?php echo saas_get_read_time(get_the_content()); ?> MIN READ</span>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>

                    <div class="pagination-wrapper mt-80">
                        <?php
                        the_posts_pagination( array(
                            'mid_size'  => 2,
                            'prev_text' => '← Newer',
                            'next_text' => 'Older →',
                        ) );
                        ?>
                    </div>

                <?php else : ?>
                    <p>No strategy guides found.</p>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <?php get_sidebar(); ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
