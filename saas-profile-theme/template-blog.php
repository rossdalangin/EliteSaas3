<?php
/**
 * Template Name: Blog Page
 */

get_header(); ?>

<main id="primary" class="site-main blog-archive-main">
    <div class="container-standard mx-auto">
        <header class="archive-header text-center mb-60">
            <h1 class="archive-title text-6xl font-black ls-neg-3 mb-20">KnotBio Strategy Hub</h1>
            <p class="color-light text-xl">The latest playbooks for scaling your digital authority.</p>
        </header>

        <div class="blog-layout">
            <div class="posts-feed">
                <?php
                $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                $args = array(
                    'post_type' => 'post',
                    'posts_per_page' => 10,
                    'paged' => $paged
                );
                $query = new WP_Query( $args );

                if ( $query->have_posts() ) : ?>
                    <div class="grid-archive gap-40">
                        <?php while ( $query->have_posts() ) : $query->the_post(); ?>
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
                                        <a href="<?php the_permalink(); ?>" class="font-black text-sm color-dark">READ PLAYBOOK →</a>
                                        <span class="text-xs color-lighter"><?php echo saas_get_read_time(get_the_content()); ?> MIN READ</span>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>

                    <div class="pagination-wrapper mt-80">
                        <?php
                        echo paginate_links( array(
                            'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                            'total'        => $query->max_num_pages,
                            'current'      => max( 1, get_query_var( 'paged' ) ),
                            'format'       => '?paged=%#%',
                            'show_all'     => false,
                            'type'         => 'plain',
                            'end_size'     => 2,
                            'mid_size'     => 1,
                            'prev_next'    => true,
                            'prev_text'    => sprintf( '<i></i> %1$s', __( '← Newer', 'text-domain' ) ),
                            'next_text'    => sprintf( '%1$s <i></i>', __( 'Older →', 'text-domain' ) ),
                            'add_args'     => false,
                            'add_fragment' => '',
                        ) );
                        ?>
                    </div>

                <?php wp_reset_postdata(); else : ?>
                    <div class="no-results-box p-60 bg-light radius-24 text-center">
                        <h3 class="mb-0">Strategy is in development. Check back soon.</h3>
                    </div>
                <?php endif; ?>
            </div>

            <?php get_sidebar(); ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
