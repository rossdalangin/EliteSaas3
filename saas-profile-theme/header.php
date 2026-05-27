<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    $global_favicon = get_option('saas_global_favicon');
    $slug = get_query_var( 'saas_profile' );
    if ($slug) :
        $profile = saas_get_profile_by_slug($slug);
        if ($profile) :
            $p_id = $profile->ID;
            $p_meta = saas_get_profile_meta($p_id);
            $custom_title = get_post_meta($p_id, '_saas_seo_title', true);
            $custom_desc = get_post_meta($p_id, '_saas_seo_desc', true);
            $custom_favicon = get_post_meta($p_id, '_saas_favicon', true) ?: $global_favicon;
            ?>
            <title><?php echo esc_html($custom_title ?: $profile->post_title . ' | Digital Business Card'); ?></title>
            <meta name="description" content="<?php echo esc_attr($custom_desc ?: wp_trim_words($p_meta['bio'], 25)); ?>">
            <?php if($custom_favicon) : ?><link rel="icon" href="<?php echo esc_url($custom_favicon); ?>"><?php endif; ?>

            <!-- OpenGraph Meta Tags -->
            <meta property="og:title" content="<?php echo esc_html($profile->post_title); ?>">
            <meta property="og:description" content="<?php echo esc_attr($p_meta['headline']); ?>">
            <meta property="og:type" content="profile">
            <meta property="og:url" content="<?php echo home_url('/' . $slug); ?>">
            <meta property="og:site_name" content="<?php bloginfo('name'); ?>">

            <!-- Twitter Card Meta Tags -->
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="<?php echo esc_html($profile->post_title); ?>">
            <meta name="twitter:description" content="<?php echo esc_attr($p_meta['headline']); ?>">

            <link rel="canonical" href="<?php echo home_url('/' . $slug); ?>">
            <?php
            $og_image = get_the_post_thumbnail_url($profile->ID, 'full');
            if (!$og_image) {
                $cover_id = get_post_meta($profile->ID, '_saas_cover_id', true);
                if ($cover_id) $og_image = wp_get_attachment_url($cover_id);
            }
            if ($og_image) : ?>
                <meta property="og:image" content="<?php echo esc_url($og_image); ?>">
                <meta name="twitter:image" content="<?php echo esc_url($og_image); ?>">
            <?php endif; ?>
        <?php endif;
    endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Montserrat:wght@400;700;900&family=Playfair+Display:wght@400;700;900&display=swap" rel="stylesheet">

    <?php if (!get_query_var('saas_profile')) : ?>
        <title><?php wp_title('|', true, 'right'); ?></title>
        <meta name="description" content="<?php bloginfo('description'); ?>">
        <link rel="canonical" href="<?php echo esc_url(home_url(add_query_arg([], $GLOBALS['wp']->request))); ?>">
    <?php endif; ?>

    <?php wp_head(); ?>
    <?php if(!empty($global_favicon) && !get_query_var('saas_profile')) : ?>
        <link rel="icon" href="<?php echo esc_url($global_favicon); ?>">
    <?php endif; ?>
    <?php
    $global_css = get_option('saas_global_css');
    if ($global_css) : ?>
        <style id="saas-global-dynamic-css"><?php echo $global_css; ?></style>
    <?php endif; ?>
    <?php
    if ($slug) {
        $profile = saas_get_profile_by_slug($slug);
        if ($profile) {
            $p_meta = saas_get_profile_meta($profile->ID);
            $og_image = get_the_post_thumbnail_url($profile->ID, 'full');

            // Schema.org Structured Data
            $schema = [
                "@context" => "https://schema.org",
                "@type" => "Person",
                "name" => $profile->post_title,
                "jobTitle" => $p_meta['headline'],
                "description" => $p_meta['bio'],
                "url" => home_url('/' . $slug),
            ];
            if ($og_image) $schema["image"] = $og_image;

            echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';

            $payments = new Saas_Payments();
            if ($payments->is_pro_user($profile->post_author)) {
                echo get_post_meta($profile->ID, '_saas_header_scripts', true);
            }
        }
    }
    ?>
</head>
<body <?php body_class( $theme_class ?? '' ); ?> data-saas-theme="light">

<?php
// Only show the global header and site navigation if we're NOT on a user profile page.
if ( ! get_query_var( 'saas_profile' ) ) : ?>
    <?php
    $global_logo = get_option('saas_global_logo');
    if ($global_logo) : ?>
        <div class="saas-global-header">
            <div class="header-container py-0 flex-center">
                <img src="<?php echo esc_url($global_logo); ?>" alt="SaaS Logo" class="global-logo-img">
                <span class="ml-10">THE ELITE STANDARD</span>
            </div>
        </div>
    <?php endif; ?>

    <header id="masthead" class="site-header">
        <div class="header-container">
            <div class="site-branding">
                <?php
                if ( has_custom_logo() ) {
                    the_custom_logo();
                } else {
                    echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="site-branding-link">' . get_bloginfo( 'name' ) . '</a>';
                }
                ?>
            </div>

            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Toggle navigation menu">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>

            <nav id="site-navigation" class="main-navigation">
                <ul id="primary-menu" class="primary-menu-list">
                    <li><a href="<?php echo home_url('/'); ?>">Home</a></li>
                    <li><a href="<?php echo home_url('/directory'); ?>">Discovery</a></li>
                    <li><a href="<?php echo home_url('/pricing'); ?>">Pricing</a></li>
                    <li><a href="<?php echo home_url('/about'); ?>">About</a></li>
                    <li><a href="<?php echo home_url('/contact'); ?>">Contact</a></li>
                    <li class="mobile-only-cta">
                        <?php if ( is_user_logged_in() ) : ?>
                            <a href="<?php echo home_url('/dashboard'); ?>">Dashboard</a>
                        <?php else : ?>
                            <a href="<?php echo home_url('/login'); ?>">Login</a>
                            <a href="<?php echo home_url('/register'); ?>" class="saas-link-btn style-featured mt-10">Get Started</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </nav>

            <div class="header-cta">
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo home_url('/dashboard'); ?>" class="saas-link-btn style-featured btn-header-cta">Dashboard</a>
                <?php else : ?>
                    <a href="<?php echo home_url('/login'); ?>" class="header-cta-link">Login</a>
                    <a href="<?php echo home_url('/register'); ?>" class="saas-link-btn style-featured btn-header-cta">Get Started</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
<?php endif; ?>

<?php wp_body_open(); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const siteNavigation = document.getElementById('site-navigation');
    const siteHeader = document.querySelector('.site-header');

    if (menuToggle && siteNavigation) {
        menuToggle.addEventListener('click', function() {
            const expanded = this.getAttribute('aria-expanded') === 'true' || false;
            this.setAttribute('aria-expanded', !expanded);
            siteNavigation.classList.toggle('is-active');
            document.body.classList.toggle('menu-open');
        });

        // Close menu when clicking a link
        const navLinks = siteNavigation.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                menuToggle.setAttribute('aria-expanded', 'false');
                siteNavigation.classList.remove('is-active');
                document.body.classList.remove('menu-open');
            });
        });
    }

    // Header scroll effect
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            siteHeader?.classList.add('is-scrolled');
        } else {
            siteHeader?.classList.remove('is-scrolled');
        }
    });
});
</script>
