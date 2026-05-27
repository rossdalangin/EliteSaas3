<?php
/**
 * Template Name: Discovery Directory
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<main id="directory-page" class="site-main bg-color">
    <section class="landing-content pt-100">
        <div class="text-center mb-60">
            <h1 class="landing-title">Discover Elite Creators</h1>
            <p class="landing-hero-text">Explore the best digital identities built with our platform.</p>

            <div class="directory-search mt-40 max-w-560 mx-auto">
                <form action="" method="GET" class="hero-claim-form p-6">
                    <input type="text" name="s" placeholder="Search creators by name..." value="<?php echo esc_attr($_GET['s'] ?? ''); ?>" class="hero-claim-input pl-24">
                    <?php if($active_niche = $_GET['niche'] ?? '') : ?><input type="hidden" name="niche" value="<?php echo esc_attr($active_niche); ?>"><?php endif; ?>
                    <button type="submit" class="hero-claim-btn btn-search">Search</button>
                </form>
            </div>

            <div class="directory-filters mt-40 flex-center gap-12 flex-wrap">
                <?php
                $active_niche = $_GET['niche'] ?? '';
                $niches = [
                    '' => 'All Experts',
                    'coach' => 'Coaches',
                    'consultant' => 'Consultants',
                    'realtor' => 'Real Estate',
                    'artist' => 'Artists',
                    'agency' => 'Agencies',
                    'creator' => 'Creators'
                ];
                foreach($niches as $slug => $label) :
                    $is_active = ($active_niche === $slug);
                    $url = $slug ? add_query_arg('niche', $slug) : remove_query_arg('niche');
                    $filter_class = 'filter-link' . ($is_active ? ' is-active' : '');
                ?>
                    <a href="<?php echo esc_url($url); ?>" class="<?php echo $filter_class; ?>">
                        <?php echo esc_html($label); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="directory-grid">
            <?php
            $args = [
                'post_type' => 'saas_profile',
                'post_status' => 'publish',
                'meta_query' => [
                    'relation' => 'AND',
                    ['key' => '_saas_show_in_directory', 'value' => '1']
                ],
                'numberposts' => 50
            ];

            if ($active_niche) {
                $args['meta_query'][] = ['key' => '_saas_niche', 'value' => $active_niche];
            }

            if ($search = $_GET['s'] ?? '') {
                $args['s'] = $search;
            }

            $profiles = get_posts($args);

            if($profiles) :
                foreach($profiles as $p) :
                    $p_meta = saas_get_profile_meta($p->ID);
                    $p_niche = get_post_meta($p->ID, '_saas_niche', true);
                    ?>
                    <a href="<?php echo home_url('/' . $p->post_name); ?>" class="profile-card-directory inherit-link">
                        <?php if($p_niche): ?>
                            <span class="badge-ui badge-directory"><?php echo $p_niche; ?></span>
                        <?php endif; ?>
                        <div class="mb-24">
                            <?php if (has_post_thumbnail($p->ID)) : ?>
                                <?php echo get_the_post_thumbnail($p->ID, 'thumbnail', ['class' => 'avatar-directory']); ?>
                            <?php else : ?>
                                <div class="directory-avatar-wrapper">👤</div>
                            <?php endif; ?>
                        </div>
                        <h3 class="mb-0 text-xl font-bold"><?php echo esc_html($p->post_title); ?></h3>
                        <p class="text-sm color-light mt-10 mb-10"><?php echo esc_html($p_meta['headline']); ?></p>
                        <div class="directory-view-link">View Profile →</div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results-box">
                    <p class="mb-0 font-bold text-xl">No public profiles found matching your search.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>
