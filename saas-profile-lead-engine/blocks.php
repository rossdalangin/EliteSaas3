<?php
/**
 * Gutenberg Block Integration (SaaS Profile Embed)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function saas_register_gutenberg_blocks() {
    // Enqueue Block Editor Script
    wp_register_script(
        'saas-block-editor-js',
        plugin_dir_url( __FILE__ ) . 'block-editor.js',
        [ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components' ]
    );

    // Register the SaaS Profile Embed block
    register_block_type( 'saas/profile-embed', [
        'editor_script' => 'saas-block-editor-js',
        'render_callback' => 'saas_render_profile_block',
        'attributes' => [
            'profile_id' => [
                'type' => 'number',
                'default' => 0
            ]
        ]
    ]);

    // Register the Lead Capture Form block
    register_block_type( 'saas/lead-form-block', [
        'editor_script' => 'saas-block-editor-js',
        'render_callback' => 'saas_render_lead_form_block',
        'attributes' => [
            'profile_id' => [
                'type' => 'number',
                'default' => 0
            ],
            'title' => [
                'type' => 'string',
                'default' => 'Contact Me'
            ]
        ]
    ]);
}
add_action( 'init', 'saas_register_gutenberg_blocks' );

/**
 * Render Callback for the profile embed block
 */
function saas_render_profile_block( $attributes ) {
    $profile_id = $attributes['profile_id'];
    if ( ! $profile_id ) return '<p>Please select a SaaS Profile to embed.</p>';

    $profile = get_post( $profile_id );
    if ( ! $profile || $profile->post_type !== 'saas_profile' ) return '';

    // Simplified embed rendering (reuse theme logic if possible)
    ob_start();
    ?>
    <div class="saas-profile-embed card-white flex-between flex-center p-24">
        <div class="flex-center gap-20">
            <div class="radius-full overflow-hidden w-64 h-64 bg-light flex-center text-2xl">
                <?php echo has_post_thumbnail($profile_id) ? get_the_post_thumbnail($profile_id, 'thumbnail', ['class' => 'full-width full-height object-cover']) : '👤'; ?>
            </div>
            <div>
                <h3 class="mb-0 text-xl font-black"><?php echo esc_html($profile->post_title); ?></h3>
                <p class="mb-0 text-sm color-primary font-bold">Elite Digital Identity</p>
            </div>
        </div>
        <a href="<?php echo home_url('/' . $profile->post_name); ?>" class="saas-link-btn" style="width: auto !important; padding: 12px 24px !important;">View Profile →</a>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Render Callback for the lead form block
 */
function saas_render_lead_form_block( $attributes ) {
    $profile_id = $attributes['profile_id'];
    $title = $attributes['title'];
    if ( ! $profile_id ) return '<p>Please select a SaaS Profile to link this form to.</p>';

    ob_start();
    ?>
    <section class="saas-block block-lead-form saas-embedded-lead-form card-white p-48 text-center max-w-600 mx-auto">
        <h2 class="text-4xl font-black mb-10"><?php echo esc_html( $title ); ?></h2>
        <p class="color-light mb-32">Ready to scale? Send a direct inquiry below.</p>
        <form class="saas-dynamic-form text-left" data-block-id="embedded">
            <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
            <input type="hidden" name="security" value="<?php echo wp_create_nonce('saas_lead_nonce'); ?>">
            <div class="display-none"><input type="text" name="saas_honeypot"></div>
            <div class="mb-16">
                <input type="text" name="name" placeholder="Your Name" required class="saas-input">
            </div>
            <div class="mb-16">
                <input type="email" name="email" placeholder="Your Email" required class="saas-input">
            </div>
            <button type="submit" class="saas-link-btn style-featured font-black">Submit Inquiry →</button>
        </form>
        <div class="lead-feedback mt-20 font-bold color-success"></div>
    </section>
    <?php
    return ob_get_clean();
}
