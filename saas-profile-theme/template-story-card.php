<?php
/**
 * Template Name: Story Card (Vertical 1080x1920)
 * Description: Special template for generating vertical social media assets.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Get Profile from Query Var (Support Slug or ID)
$slug = isset($_GET['profile']) ? $_GET['profile'] : get_query_var( 'saas_profile' );
$profile_id_param = isset($_GET['profile_id']) ? intval($_GET['profile_id']) : 0;
$profile = null;

if ($profile_id_param) {
    $profile = get_post($profile_id_param);
    if ($profile && $profile->post_type !== 'saas_profile') $profile = null;
} elseif ( $slug ) {
    $profile = get_posts([
        'name'        => $slug,
        'post_type'   => 'saas_profile',
        'post_status' => 'publish',
        'numberposts' => 1
    ]);
    $profile = ! empty($profile) ? $profile[0] : null;
}

if (!$profile) {
    echo "Please specify a valid profile.";
    exit;
}

$profile_id = $profile->ID;
$meta = saas_get_profile_meta( $profile_id );
$bg_color = get_post_meta( $profile_id, '_saas_bg_color', true ) ?: '#4f46e5';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Story Card - <?php echo esc_html($profile->post_title); ?></title>
    <style>
        body, html {
            margin: 0; padding: 0;
            width: 1080px; height: 1920px;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
            background: <?php echo esc_attr($bg_color); ?>;
            color: #fff;
        }
        #story-canvas {
            width: 1080px; height: 1920px;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            text-align: center;
            position: relative;
            background: linear-gradient(135deg, <?php echo $meta['theme_color']; ?> 0%, #000 100%);
        }
        .avatar {
            width: 300px; height: 300px;
            border-radius: 50%;
            border: 15px solid #fff;
            margin-bottom: 50px;
            object-fit: cover;
        }
        h1 { font-size: 80px; margin: 0 0 20px; font-weight: 900; }
        .headline { font-size: 45px; opacity: 0.9; margin-bottom: 60px; max-width: 800px; }
        .qr-placeholder {
            width: 400px; height: 400px;
            background: #fff;
            padding: 20px;
            border-radius: 40px;
            margin-top: 80px;
        }
        .footer-url {
            position: absolute;
            bottom: 100px;
            font-size: 40px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .branding {
            position: absolute;
            bottom: 40px;
            font-size: 25px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div id="story-canvas">
        <?php if ( has_post_thumbnail( $profile_id ) ) : ?>
            <?php echo get_the_post_thumbnail( $profile_id, 'large', ['class' => 'avatar'] ); ?>
        <?php else : ?>
            <div class="avatar" style="background:#eee;"></div>
        <?php endif; ?>

        <h1><?php echo esc_html($profile->post_title); ?></h1>
        <p class="headline"><?php echo esc_html($meta['headline']); ?></p>

        <div class="qr-placeholder" style="background:#fff; display:flex; align-items:center; justify-content:center;">
            <img src="<?php echo saas_get_profile_qr_url($profile->post_name, '#000000'); ?>" style="width:100%; height:100%;">
        </div>

        <div class="footer-url"><?php echo parse_url(home_url(), PHP_URL_HOST); ?>/<?php echo $slug; ?></div>
        <div class="branding">Powered by <?php bloginfo('name'); ?></div>
    </div>
</body>
</html>
