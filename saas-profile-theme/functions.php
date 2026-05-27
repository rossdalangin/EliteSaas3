<?php
/**
 * Theme Functions
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function saas_theme_scripts() {
    wp_enqueue_style( 'saas-main-style', get_stylesheet_uri() );
    wp_enqueue_script( 'jquery' ); // Ensure jQuery is loaded

    // Pass AJAX and REST URLs to theme
    wp_localize_script( 'jquery', 'saas_data', [
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'rest_url' => get_rest_url( null, '/saas/v1' )
    ]);
}
add_action( 'wp_enqueue_scripts', 'saas_theme_scripts' );

// Add support for background customization
add_theme_support( 'custom-background' );

// Add support for theme logo
add_theme_support( 'custom-logo' );

// Register Menus
function saas_register_menus() {
    register_nav_menus([
        'primary' => 'Primary Menu (Header)',
        'footer'  => 'Footer Menu',
    ]);
}
add_action( 'init', 'saas_register_menus' );

/**
 * Customizer Enhancements for Global Branding
 */
function saas_customize_register( $wp_customize ) {
    $wp_customize->add_section( 'saas_branding_section' , [
        'title'      => 'SaaS Global Branding',
        'priority'   => 30,
    ]);

    $wp_customize->add_setting( 'saas_primary_color' , [
        'default'   => '#4f46e5',
        'transport' => 'refresh',
    ]);
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'saas_primary_color', [
        'label'      => 'Global Primary Color',
        'section'    => 'saas_branding_section',
        'settings'   => 'saas_primary_color',
    ]));

    $wp_customize->add_setting( 'saas_accent_color' , [
        'default'   => '#10b981',
        'transport' => 'refresh',
    ]);
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'saas_accent_color', [
        'label'      => 'Global Accent Color',
        'section'    => 'saas_branding_section',
        'settings'   => 'saas_accent_color',
    ]));
}
add_action( 'customize_register', 'saas_customize_register' );

/**
 * Output Customizer CSS
 */
function saas_customizer_css() {
    ?>
    <style type="text/css">
        :root {
            --primary-color: <?php echo get_theme_mod( 'saas_primary_color', '#4f46e5' ); ?>;
            --accent-color: <?php echo get_theme_mod( 'saas_accent_color', '#10b981' ); ?>;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'saas_customizer_css' );

/**
 * Data Helpers (Robustness check)
 */
if ( ! function_exists( 'saas_get_profile_by_slug' ) ) {
    function saas_get_profile_by_slug( $slug ) {
        $posts = get_posts([
            'name'        => $slug,
            'post_type'   => 'saas_profile',
            'post_status' => 'publish',
            'numberposts' => 1
        ]);
        return $posts ? $posts[0] : null;
    }
}

if ( ! function_exists( 'saas_get_effective_url' ) ) {
    function saas_get_effective_url( $block_id, $default_url ) {
        return $default_url; // Default if plugin is inactive
    }
}

/**
 * Register Sidebar
 */
function saas_widgets_init() {
    register_sidebar( array(
        'name'          => 'Blog Sidebar',
        'id'            => 'sidebar-1',
        'description'   => 'Add widgets here to appear in your blog sidebar.',
        'before_widget' => '<section id="%1" class="widget %2">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'saas_widgets_init' );

// Post support
add_theme_support( 'post-thumbnails' );

/**
 * Calculate reading time
 */
function saas_get_read_time($content) {
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200);
    return $reading_time;
}
