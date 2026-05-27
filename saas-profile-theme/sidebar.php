<?php
/**
 * The sidebar containing the main widget area.
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
    // Default Sidebar if no widgets
    ?>
    <aside id="secondary" class="widget-area blog-sidebar">
        <section class="widget widget_search">
            <h3 class="widget-title">Search</h3>
            <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <label>
                    <span class="screen-reader-text">Search for:</span>
                    <input type="search" class="search-field" placeholder="Search &hellip;" value="" name="s" />
                </label>
                <button type="submit" class="search-submit"><span class="si-icon">🔍</span></button>
            </form>
        </section>

        <section class="widget knotbio-cta-widget">
            <div class="sidebar-cta-card">
                <h3 class="color-white">Own your link.</h3>
                <p class="color-white-70 text-sm">Join KnotBio and build your high-converting digital identity in seconds.</p>
                <a href="<?php echo home_url('/register'); ?>" class="saas-link-btn btn-elite-launch text-sm">Claim My Link →</a>
            </div>
        </section>

        <section class="widget widget_recent_entries">
            <h3 class="widget-title">Recent Strategy</h3>
            <ul>
                <?php
                $recent_posts = wp_get_recent_posts( array( 'numberposts' => 5, 'post_status' => 'publish' ) );
                foreach( $recent_posts as $post ) : ?>
                    <li>
                        <a href="<?php echo get_permalink($post['ID']); ?>"><?php echo esc_html($post['post_title']); ?></a>
                        <span class="post-date"><?php echo get_the_date('', $post['ID']); ?></span>
                    </li>
                <?php endforeach; wp_reset_query(); ?>
            </ul>
        </section>

        <section class="widget widget_categories">
            <h3 class="widget-title">Categories</h3>
            <ul>
                <?php wp_list_categories( array( 'title_li' => '' ) ); ?>
            </ul>
        </section>
    </aside>
    <?php
    return;
}
?>

<aside id="secondary" class="widget-area blog-sidebar">
    <?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
