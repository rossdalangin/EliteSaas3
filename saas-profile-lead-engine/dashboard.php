<?php
/**
 * User Dashboard UI and Logic
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Saas_Dashboard {
    public function __construct() {
        add_shortcode( 'saas_dashboard', [ $this, 'render_dashboard' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_dashboard_scripts' ] );
    }

    public function enqueue_dashboard_scripts() {
        wp_enqueue_media();
        wp_enqueue_style( 'saas-dashboard-css', plugin_dir_url( __FILE__ ) . 'dashboard.css', [], '2.7' );
        wp_enqueue_script( 'sortable-js', 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js', [], '1.15.0', true );
        wp_enqueue_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], '4.0.0', true );
        wp_enqueue_script( 'saas-dashboard-js', plugin_dir_url( __FILE__ ) . 'dashboard.js', [ 'jquery' ], '2.7', true );
        wp_localize_script( 'saas-dashboard-js', 'saas_dashboard_data', [
            'ajax_url'  => admin_url( 'admin-ajax.php' ),
            'nonce'     => wp_create_nonce( 'saas_dashboard_nonce' ),
            'templates' => get_option('saas_templates') ?: []
        ]);
    }

    public function render_dashboard() {
        if ( ! is_user_logged_in() ) {
            return '<p>Please log in to manage your profile.</p>';
        }

        $user_id = get_current_user_id();
        $all_user_profiles = get_posts([
            'post_type'   => 'saas_profile',
            'author'      => $user_id,
            'numberposts' => -1,
        ]);

        $active_profile_id = isset($_GET['profile_id']) ? intval($_GET['profile_id']) : 0;
        $payments = new Saas_Payments();

        // Explicit ownership check for security
        $profile_obj = null;
        if ($active_profile_id) {
            $test_post = get_post($active_profile_id);
            if ($test_post && $test_post->post_author == $user_id && $test_post->post_type === 'saas_profile') {
                $profile_obj = $test_post;
            }
        }

        if ( ! $profile_obj ) {
            if ( ! empty( $all_user_profiles ) ) {
                $profile_obj = $all_user_profiles[0];
            } else {
                $user = wp_get_current_user();
                $new_id = wp_insert_post([
                    'post_type'   => 'saas_profile',
                    'post_title'  => $user->display_name,
                    'post_name'   => $user->user_login,
                    'post_status' => 'publish',
                    'post_author' => $user_id,
                ]);
                $profile_obj = get_post($new_id);
            }
        }
        $profile_id = $profile_obj->ID;

        $meta = saas_get_profile_meta( $profile_id );
        $is_pro = saas_is_profile_licensed($profile_id);

        // Nudge for intended Pro/Agency users
        $target_plan = get_user_meta($user_id, '_saas_registration_target_plan', true);
        if ( ! $is_pro && in_array($target_plan, ['pro', 'agency']) ) {
            $plan_label = ($target_plan === 'agency') ? 'Agency Unlimited' : 'Elite Pro';
            echo '<div class="saas-onboarding-card dashboard-card bg-accent mb-20 border-none">
                <div class="flex-between flex-center">
                    <p class="mb-0 font-bold color-white">🌟 Ready to complete your '. $plan_label .' upgrade? Unlock all features now.</p>
                    <button class="button" onclick="window.location.search=\'?tab=billing\'">Complete Upgrade</button>
                </div>
            </div>';
        }

        $analytics = new Saas_Analytics();
        $link_stats = $analytics->get_user_link_stats($user_id);

        // Auto-launch Wizard for new profiles
        $show_wizard = empty($meta['headline']) && empty($links);

        $links = get_posts([
            'post_type'   => 'saas_link',
            'author'      => $user_id,
            'meta_query' => [['key' => '_saas_profile_id', 'value' => $profile_id]],
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
            'numberposts' => -1,
        ]);

        $profile_bg_type = get_post_meta($profile_id, '_saas_bg_type', true) ?: 'flat';
        $profile_bg_val  = ($profile_bg_type === 'gradient') ? get_post_meta($profile_id, '_saas_bg_gradient', true) : get_post_meta($profile_id, '_saas_bg_color', true);

        ob_start();
        ?>
        <div id="saas-dashboard">
            <div class="saas-top-utility-nav flex-between flex-center mb-10">
                <div class="flex gap-20">
                    <a href="<?php echo home_url('/'); ?>" class="color-muted inherit-link font-bold text-xs">← Back to Website</a>
                    <a href="<?php echo home_url('/' . $profile_obj->post_name); ?>" target="_blank" class="color-primary inherit-link font-bold text-xs">🌍 View Live Profile</a>
                </div>
                <a href="<?php echo wp_logout_url(home_url()); ?>" class="color-danger inherit-link font-bold text-xs">Logout 👋</a>
            </div>
            <div class="dashboard-main-area relative">
                <div class="animated-mesh-bg opacity-30">
                    <div class="mesh-circle-1"></div>
                    <div class="mesh-circle-2"></div>
                </div>
                <!-- Onboarding Checklist -->
                <div class="grid-2 gap-20 mb-20">
                    <div class="saas-onboarding-card dashboard-card mb-0">
                        <div class="flex-between flex-center flex-wrap gap-15">
                            <div>
                                <h4 class="mb-0">🚀 Get Started Checklist</h4>
                                <div class="flex gap-15 mt-8 text-sm flex-wrap">
                                    <span><?php echo $meta['headline'] ? '[✓]' : '[ ]'; ?> Bio</span>
                                    <span><?php echo count($links) > 0 ? '[✓]' : '[ ]'; ?> Blocks</span>
                                    <span><?php echo $is_pro ? '[✓]' : '[ ]'; ?> Pro Upgrade</span>
                                </div>
                            </div>
                            <button class="button" onclick="document.getElementById('saas-wizard-modal').style.display='flex'">Launch Setup Wizard</button>
                        </div>
                    </div>

                    <div class="dashboard-card mb-0 p-15 border-secondary-left">
                        <div class="flex-between flex-center mb-10">
                            <h4 class="mb-0 text-sm color-muted">Pulse: <?php echo date('F'); ?> Activity</h4>
                            <span class="pulse-dot"></span>
                        </div>
                        <div class="recent-activity-list text-xs">
                            <?php
                            global $wpdb;
                            $table = $wpdb->prefix . 'saas_analytics';
                            $recent_activity = [];

                            $events = $wpdb->get_results($wpdb->prepare("SELECT event_type as type, target_id, created_at FROM $table WHERE user_id = %d ORDER BY id DESC LIMIT 5", $user_id));
                            foreach($events as $e) {
                                $recent_activity[] = [
                                    'type' => $e->type,
                                    'target' => ($e->type === 'view') ? 'Profile' : get_the_title($e->target_id),
                                    'time' => strtotime($e->created_at),
                                    'icon' => ($e->type === 'view') ? '👁️' : '🖱️'
                                ];
                            }

                            $recent_leads = get_posts(['post_type' => 'saas_lead', 'author' => $user_id, 'numberposts' => 3]);
                            foreach($recent_leads as $rl) {
                                $recent_activity[] = [
                                    'type' => 'lead',
                                    'target' => get_post_meta($rl->ID, '_saas_lead_name', true),
                                    'time' => get_post_time('U', true, $rl),
                                    'icon' => '🚀'
                                ];
                            }

                            usort($recent_activity, function($a, $b) { return $b['time'] - $a['time']; });
                            $recent_activity = array_slice($recent_activity, 0, 4);

                            if ($recent_activity) :
                                foreach($recent_activity as $act) :
                                    ?>
                                    <div class="mb-8 pb-8 border-light flex gap-10 flex-start">
                                        <span class="text-base"><?php echo $act['icon']; ?></span>
                                        <div class="flex-1">
                                            <strong><?php echo esc_html(ucfirst($act['type'])); ?></strong>: <?php echo esc_html($act['target']); ?>
                                            <br><span class="color-muted text-xs"><?php echo human_time_diff($act['time'], current_time('timestamp')); ?> ago</span>
                                        </div>
                                    </div>
                                <?php endforeach;
                            else : ?>
                                <p class="color-muted mb-0">Waiting for first visitor...</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="saas-dashboard-header">
                    <div class="profile-switcher-wrapper">
                        <h2 class="profile-title"><?php echo esc_html($profile_obj->post_title); ?> ▾</h2>
                        <div class="profile-dropdown">
                            <?php foreach($all_user_profiles as $up) : ?>
                                <div class="dropdown-item-wrapper <?php echo ($up->ID == $active_profile_id) ? 'active' : ''; ?>">
                                    <a href="?profile_id=<?php echo $up->ID; ?>" class="dropdown-item"><?php echo esc_html($up->post_title); ?></a>
                                    <div class="flex gap-5">
                                        <button class="clone-profile-btn" data-id="<?php echo $up->ID; ?>" title="Clone Profile">📋</button>
                                        <button class="delete-profile-btn color-danger" data-id="<?php echo $up->ID; ?>" title="Delete Profile">🗑️</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="dropdown-divider"></div>
                            <button id="saas-add-profile-trigger" class="add-profile-btn">+ New Profile</button>
                        </div>
                    </div>
                    <div class="saas-share-bar">
                        <?php
                        $new_leads_count = get_posts(['post_type' => 'saas_lead', 'author' => $user_id, 'meta_key' => '_saas_lead_status', 'meta_value' => 'New', 'fields' => 'ids', 'numberposts' => -1]);
                        $count = count($new_leads_count);
                        ?>
                        <div class="saas-notif-bell" onclick="document.getElementById('saas-notif-modal').style.display='flex'">🔔<?php if($count > 0) echo '<span class="notif-count">'.$count.'</span>'; ?></div>
                        <input type="text" id="saas-my-link" value="<?php echo home_url('/' . $profile_obj->post_name); ?>" readonly>
                        <button id="saas-copy-btn" class="btn-primary">Copy Link</button>
                        <button id="saas-preview-trigger" class="btn-primary bg-secondary">👁️ Preview</button>
                    </div>
                </div>

                <nav class="saas-tabs">
                    <button class="active" data-tab="links">🔗 Blocks</button>
                    <button data-tab="profile">👤 Profile</button>
                    <button data-tab="branding">🎨 Vibe</button>
                    <button data-tab="custom_css">✨ Custom CSS</button>
                    <button data-tab="leads">👥 Leads</button>
                    <button data-tab="analytics">📈 Stats</button>
                    <button data-tab="integrations">🔌 Sync</button>
                    <button data-tab="referrals">💸 Earn</button>
                    <button data-tab="automation">⚙️ Settings</button>
                    <button data-tab="share">📱 Share</button>
                    <button data-tab="templates">🎨 Templates</button>
                    <button data-tab="billing">💳 Pro</button>
                    <button data-tab="seo">🔍 SEO</button>
                    <button data-tab="tracking">📊 Tracking</button>
                    <?php
                    $unread_msgs = get_posts([
                        'post_type' => 'saas_message',
                        'meta_query' => [
                            ['key' => '_saas_msg_recipient', 'value' => $user_id],
                            ['key' => '_saas_msg_status', 'value' => 'unread']
                        ],
                        'fields' => 'ids',
                        'numberposts' => -1
                    ]);
                    $msg_count = count($unread_msgs);
                    ?>
                    <button data-tab="inbox" class="relative">📩 Inbox <?php if($msg_count > 0) echo '<span class="notif-count pos-absolute-tr">'.$msg_count.'</span>'; ?></button>
                    <button data-tab="training">🎓 Training</button>
                    <button data-tab="account">👤 Account</button>
                </nav>

                <div id="tab-links" class="saas-tab-content active">
                    <div class="link-tab-grid">
                        <div class="block-picker-sidebar">
                            <div class="dashboard-card">
                                <h3>Manage Blocks</h3>
                                <p class="field-hint">Blocks are the building blocks of your funnel. Use them to share links, capture leads, or showcase testimonials.</p>
                                <div class="saas-block-picker">
                                    <div class="picker-item active" data-type="button"><span>🔗</span> Button</div>
                                    <div class="picker-item" data-type="video"><span>🎬</span> Video</div>
                                    <div class="picker-item" data-type="testimonial"><span>⭐</span> Testim</div>
                                    <div class="picker-item" data-type="faq"><span>❓</span> FAQ</div>
                                    <div class="picker-item" data-type="pricing"><span>💰</span> Price</div>
                                    <div class="picker-item <?php echo $is_pro ? '' : 'pro-locked'; ?>" data-type="image_gallery"><span>🖼️</span> Gal <span class="pro-badge">Pro</span></div>
                                    <div class="picker-item" data-type="social_icons"><span>📱</span> Social</div>
                                    <div class="picker-item <?php echo $is_pro ? '' : 'pro-locked'; ?>" data-type="newsletter"><span>📧</span> Mail <span class="pro-badge">Pro</span></div>
                                    <div class="picker-item" data-type="lead_form"><span>🎯</span> Form</div>
                                    <div class="picker-item <?php echo $is_pro ? '' : 'pro-locked'; ?>" data-type="calendar"><span>📅</span> Cal <span class="pro-badge">Pro</span></div>
                                </div>

                                <form id="saas-add-link-form">
                                    <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                                    <input type="hidden" name="block_type" id="saas-block-type-hidden" value="button">

                                    <div id="saas-block-guidance" class="bg-primary-soft p-15 radius-12 mb-20 border-primary text-xs lh-1-4">
                                        <strong>💡 How to use this block:</strong><br>
                                        <span id="guidance-text">Standard Button: Enter a label and the destination URL.</span>
                                    </div>

                                    <div class="field">
                                        <label id="label-title">Button Label</label>
                                        <input type="text" name="title" placeholder="e.g. Schedule a Call" required>
                                    </div>
                                    <div class="field">
                                        <label id="label-url">Destination URL</label>
                                        <input type="url" name="url" placeholder="https://calendly.com/yourname">
                                    </div>
                                    <div class="field">
                                        <label id="label-extra">Description (Optional)</label>
                                        <textarea name="extra" id="saas-add-extra-field" placeholder="Brief sub-text to appear below the label." rows="2"></textarea>
                                    </div>

                                    <div id="saas-gallery-selector-wrap" class="display-none mb-20 p-15 bg-main border-light radius-12">
                                        <div class="flex-between mb-10">
                                            <label class="mb-0">Gallery Images</label>
                                            <button type="button" class="clear-gallery button text-xs p-2-8 color-danger border-none bg-transparent" data-target="saas-gallery-previews">Clear All</button>
                                        </div>
                                        <div id="saas-gallery-previews" class="grid-gallery mb-10"></div>
                                        <p class="text-xs color-muted mb-10 text-center">💡 Drag images to reorder them.</p>
                                        <button type="button" class="button select-media full-width" data-target="gallery-add">📸 Select Gallery Images</button>
                                    </div>

                                    <button type="submit" class="btn-primary full-width">Add Block</button>
                                </form>
                            </div>
                        </div>

                        <div class="links-display-area">
                            <ul id="saas-links-list" class="sortable">
                            <?php foreach ( $links as $link ) :
                                $type = get_post_meta($link->ID, '_saas_block_type', true);
                                $extra = get_post_meta($link->ID, '_saas_link_desc', true) ?: (get_post_meta($link->ID, '_saas_testimonial_text', true) ?: get_post_meta($link->ID, '_saas_faq_answer', true));
                                if (!$extra) {
                                    if ($type === 'pricing' || $type === 'product') {
                                        $price = get_post_meta($link->ID, '_saas_price', true);
                                        $feats = get_post_meta($link->ID, '_saas_features', true);
                                        if ($price) $extra = $price . ($feats ? "\n" . (is_array($feats) ? implode("\n", $feats) : $feats) : "");
                                    } elseif ($type === 'image_gallery') {
                                        $imgs = get_post_meta($link->ID, '_saas_gallery_images', true);
                                        if ($imgs) $extra = is_array($imgs) ? implode("\n", $imgs) : $imgs;
                                    } elseif ($type === 'social_icons') {
                                        $socials = get_post_meta($link->ID, '_saas_social_data', true);
                                        if ($socials && is_array($socials)) {
                                            $lines = [];
                                            foreach($socials as $p => $u) $lines[] = "$p:$u";
                                            $extra = implode("\n", $lines);
                                        }
                                    } elseif ($type === 'countdown') {
                                        $extra = get_post_meta($link->ID, '_saas_expiry', true);
                                    } elseif ($type === 'milestone') {
                                        $lbl = get_post_meta($link->ID, '_saas_ms_label', true);
                                        $per = get_post_meta($link->ID, '_saas_ms_percent', true);
                                        if ($lbl) $extra = "$lbl:$per";
                                    }
                                }
                                ?>
                                <li data-id="<?php echo $link->ID; ?>"
                                    data-type="<?php echo esc_attr($type); ?>"
                                    data-style="<?php echo esc_attr(get_post_meta($link->ID, '_saas_block_style', true)); ?>"
                                    data-animation="<?php echo esc_attr(get_post_meta($link->ID, '_saas_block_animation', true)); ?>"
                                    data-extra="<?php echo esc_attr($extra); ?>"
                                    data-start="<?php echo esc_attr(get_post_meta($link->ID, '_saas_start_date', true)); ?>"
                                    data-end="<?php echo esc_attr(get_post_meta($link->ID, '_saas_end_date', true)); ?>"
                                    data-url-mobile="<?php echo esc_attr(get_post_meta($link->ID, '_saas_url_mobile', true)); ?>"
                                    data-url-geo="<?php echo esc_attr(get_post_meta($link->ID, '_saas_url_geo', true)); ?>"
                                    data-geo-country="<?php echo esc_attr(get_post_meta($link->ID, '_saas_url_geo_country', true)); ?>"
                                    data-ab-title="<?php echo esc_attr(get_post_meta($link->ID, '_saas_ab_title_b', true)); ?>"
                                    data-ab-url="<?php echo esc_attr(get_post_meta($link->ID, '_saas_ab_url_b', true)); ?>"
                                    data-hour-from="<?php echo esc_attr(get_post_meta($link->ID, '_saas_hour_from', true)); ?>"
                                    data-hour-to="<?php echo esc_attr(get_post_meta($link->ID, '_saas_hour_to', true)); ?>"
                                    data-custom-bg="<?php echo esc_attr(get_post_meta($link->ID, '_saas_custom_bg', true)); ?>"
                                    data-custom-text="<?php echo esc_attr(get_post_meta($link->ID, '_saas_custom_text', true)); ?>"
                                    data-link-image-id="<?php echo esc_attr(get_post_meta($link->ID, '_saas_link_image_id', true)); ?>"
                                    data-password="<?php echo esc_attr(get_post_meta($link->ID, '_saas_link_password', true)); ?>">
                                    <span class="handle">⠿</span>
                                    <div class="link-info">
                                        <strong class="link-title"><?php echo esc_html( $link->post_title ); ?></strong>
                                        <span class="link-url"><?php echo esc_url( get_post_meta( $link->ID, '_saas_link_url', true ) ?: '#' ); ?></span>
                                    </div>
                                    <div class="block-actions">
                                        <button class="edit-link button" title="Edit">✏️</button>
                                        <button class="clone-link button" title="Duplicate">📋</button>
                                        <button class="delete-link button color-danger" title="Delete">🗑️</button>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                            </ul>

                            <!-- Inline Edit Block Content -->
                            <div id="saas-edit-inline" class="dashboard-card saas-inline-container display-none mt-20 border-primary-2">
                                <div class="flex-between flex-center mb-20">
                                    <div class="flex-center gap-15">
                                        <h3 class="mb-0">✏️ Edit Block</h3>
                                        <span id="edit-block-type-badge" class="pro-badge bg-muted text-xs p-4-10 radius-8">BUTTON</span>
                                    </div>
                                    <button type="button" class="close-inline button" data-target="saas-edit-inline">&times;</button>
                                </div>
                                <p class="field-hint mb-20">Optimize this block for maximum conversion. Use the advanced options to add A/B testing or device-specific routing.</p>
                                <form id="saas-edit-link-form">
                                    <input type="hidden" name="link_id" id="edit-link-id">

                                    <div id="saas-edit-block-guidance" class="bg-primary-soft p-15 radius-12 mb-20 border-primary text-xs lh-1-4">
                                        <strong>💡 Editing this block:</strong><br>
                                        <span id="edit-guidance-text">Standard Button: Perfect for links to your website, scheduler, or social profiles.</span>
                                    </div>

                                    <div class="field"><label id="edit-label-title">Block Label</label><input type="text" name="title" id="edit-link-title" required></div>
                                    <div class="field"><label id="edit-label-url">URL / Destination</label><input type="url" name="url" id="edit-link-url"></div>
                                    <div class="field"><label id="edit-label-extra">Description / Extra Content</label><textarea name="extra" id="edit-link-extra" rows="3"></textarea></div>

                                    <div id="saas-edit-gallery-selector-wrap" class="display-none mb-20 p-15 bg-main border-light radius-12">
                                        <div class="flex-between mb-10">
                                            <label class="mb-0">Gallery Images</label>
                                            <button type="button" class="clear-gallery button text-xs p-2-8 color-danger border-none bg-transparent" data-target="saas-edit-gallery-previews">Clear All</button>
                                        </div>
                                        <div id="saas-edit-gallery-previews" class="grid-gallery mb-10"></div>
                                        <p class="text-xs color-muted mb-10 text-center">💡 Drag images to reorder them.</p>
                                        <button type="button" class="button select-media full-width" data-target="gallery-edit">📸 Select Gallery Images</button>
                                    </div>

                                    <button type="button" class="button toggle-advanced full-width mb-20 bg-light color-muted font-bold">⚙️ Advanced Options</button>

                                    <div id="edit-advanced-fields" class="display-none p-20 bg-light radius-12 border-light mb-20">
                                        <div class="field">
                                            <label>Style & Animation</label>
                                            <div class="flex gap-10">
                                                <select name="block_style" id="edit-link-style" class="flex-1">
                                                    <option value="regular">Regular</option>
                                                    <option value="featured">Featured (Pulse)</option>
                                                    <option value="outline">Outline</option>
                                                    <option value="glow">Glow</option>
                                                </select>
                                                <select name="block_animation" id="edit-link-animation" class="flex-1">
                                                    <option value="none">No Animation</option>
                                                    <option value="fadeinup">Fade In Up</option>
                                                    <option value="bouncein">Bounce In</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="field <?php echo $is_pro ? '' : 'pro-gated-inline'; ?>">
                                            <label>A/B Testing (Pro)</label>
                                            <div class="flex gap-10">
                                                <input type="text" name="ab_title_b" id="edit-link-ab-title" placeholder="Variant B Title" class="flex-1">
                                                <input type="url" name="ab_url_b" id="edit-link-ab-url" placeholder="Variant B URL" class="flex-1">
                                            </div>
                                            <p class="field-hint">Variant B is served to 50% of your visitors. Measure which version converts better in the Stats tab.</p>
                                        </div>

                                        <div class="field <?php echo $is_pro ? '' : 'pro-gated-inline'; ?>">
                                            <label>Conditional Routing (Pro)</label>
                                            <div class="flex-column gap-10">
                                                <input type="url" name="url_mobile" id="edit-link-url-mobile" placeholder="Mobile-only URL">
                                                <div class="flex gap-10">
                                                    <input type="text" name="url_geo_country" id="edit-link-geo-country" placeholder="Country Code (e.g. US)" class="flex-1">
                                                    <input type="url" name="url_geo" id="edit-link-url-geo" placeholder="Geo-specific URL" class="flex-1">
                                                </div>
                                            </div>
                                            <p class="field-hint">Send visitors to different destinations based on their device or country (e.g. US, GB, CA).</p>
                                        </div>

                                        <div class="field">
                                            <label>Custom Design</label>
                                            <div class="flex gap-10">
                                                <div class="flex-1">
                                                    <small>Background</small>
                                                    <input type="color" name="custom_bg" id="edit-link-custom-bg" class="h-40 p-2">
                                                </div>
                                                <div class="flex-1">
                                                    <small>Text</small>
                                                    <input type="color" name="custom_text" id="edit-link-custom-text" class="h-40 p-2">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="field">
                                            <label>Visibility Scheduling</label>
                                            <div class="flex gap-10">
                                                <input type="date" name="start_date" id="edit-link-start" class="flex-1" title="Start Date">
                                                <input type="date" name="end_date" id="edit-link-end" class="flex-1" title="End Date">
                                            </div>
                                            <p class="field-hint">Automate your promotions. This block will only be visible between these dates.</p>
                                        </div>

                                        <div class="field">
                                            <label>Hour-Based Visibility (0-23)</label>
                                            <div class="flex gap-10">
                                                <input type="number" name="hour_from" id="edit-link-hour-from" placeholder="From (e.g. 9)" min="0" max="23" class="flex-1">
                                                <input type="number" name="hour_to" id="edit-link-hour-to" placeholder="To (e.g. 17)" min="0" max="23" class="flex-1">
                                            </div>
                                            <p class="field-hint">Only show this block during specific hours of the day (24-hour format).</p>
                                        </div>

                                            <div class="field">
                                                <label>Icon/Thumb Image</label>
                                                <div id="edit-link-image-preview" class="icon-preview-box mb-10 overflow-hidden border-light"></div>
                                                <input type="hidden" name="link_image_id" id="edit-link-image-id">
                                                <button type="button" class="button select-media" data-target="link-image">Select Icon</button>
                                            </div>

                                        <div class="field">
                                            <label>Password Unlock</label>
                                            <input type="text" name="link_password" id="edit-link-pass" placeholder="Block password">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn-primary full-width">Save All Changes</button>
                                </form>
                            </div>

                            <!-- Inline Real-Time Preview -->
                            <div id="saas-preview-inline" class="dashboard-card saas-inline-container display-none mt-20 border-secondary-2">
                                <div class="preview-header full-width color-dark mb-20">
                                    <h3 class="mb-0">📱 Real-Time Preview</h3>
                                    <div class="flex gap-10">
                                        <button onclick="document.getElementById('saas-preview-frame').contentWindow.location.reload();" class="button">🔄 Refresh</button>
                                        <button type="button" class="close-inline button" data-target="saas-preview-inline">&times;</button>
                                    </div>
                                </div>
                                <div class="preview-frame-container mx-auto">
                                    <iframe id="saas-preview-frame" src="<?php echo home_url('/' . $profile_obj->post_name . '?preview=1'); ?>"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="tab-profile" class="saas-tab-content">
                    <div class="dashboard-card">
                        <h3>Identity Settings</h3>
                        <form id="saas-profile-form">
                            <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                            <input type="hidden" name="form_context" value="profile">

                            <div class="flex gap-30 mb-30 flex-center">
                                <div class="image-select-wrapper text-center">
                                    <label>Profile Image</label>
                                    <div id="profile-image-preview" class="image-preview-circle avatar-fixed-100 mx-auto mb-10 overflow-hidden border-light cursor-pointer">
                                        <?php if ( has_post_thumbnail( $profile_id ) ) : ?>
                                            <?php echo get_the_post_thumbnail( $profile_id, 'thumbnail', ['class' => 'full-width full-height object-cover'] ); ?>
                                        <?php else : ?>
                                            <span class="lh-100 color-muted text-3xl">+</span>
                                        <?php endif; ?>
                                    </div>
                                    <input type="hidden" name="profile_image_id" id="profile-image-id" value="<?php echo get_post_thumbnail_id($profile_id); ?>">
                                    <button type="button" class="button select-media" data-target="profile-image">Change</button>
                                </div>

                                <div class="image-select-wrapper flex-1">
                                    <label>Cover Banner</label>
                                    <?php $cover_id = get_post_meta($profile_id, '_saas_cover_id', true); ?>
                                    <div id="cover-image-preview" class="image-preview-rect full-width h-100 radius-12 bg-light mb-10 overflow-hidden border-light cursor-pointer">
                                        <?php if ( $cover_id ) : ?>
                                            <?php echo wp_get_attachment_image( $cover_id, 'medium', false, ['class' => 'full-width full-height object-cover'] ); ?>
                                        <?php else : ?>
                                            <span class="display-block text-center lh-100 color-muted">Upload Banner</span>
                                        <?php endif; ?>
                                    </div>
                                    <input type="hidden" name="cover_image_id" id="cover-image-id" value="<?php echo $cover_id; ?>">
                                    <button type="button" class="button select-media" data-target="cover-image">Select Banner</button>
                                </div>
                            </div>

                            <div class="field">
                                <label>Vanity URL (Username)</label>
                                <div class="flex flex-center bg-main border-light radius-12 p-0-15">
                                    <span class="color-muted font-bold"><?php echo parse_url(home_url(), PHP_URL_HOST); ?>/</span>
                                    <input type="text" name="profile_slug" value="<?php echo esc_attr($profile_obj->post_name); ?>" class="border-none bg-transparent p-12-5 flex-1 font-bold">
                                </div>
                                <p class="text-xs color-muted mt-5">Changing this will break your old links. Use with caution.</p>
                            </div>

                            <div class="field">
                                <label>Profile Headline</label>
                                <div class="flex gap-10">
                                    <input type="text" name="headline" value="<?php echo esc_attr( $meta['headline'] ); ?>" class="flex-1" placeholder="e.g. Scaling Founders from 6 to 7 Figures">
                                    <button type="button" class="ai-assist-btn button" data-target="headline" title="AI Generate Headline">✨ AI Assist</button>
                                </div>
                                <p class="field-hint"><strong>Pro Tip:</strong> Focus on the <em>transformation</em> you provide. Use AI Assist to generate ideas based on your niche.</p>
                            </div>
                            <div class="field">
                                <label>Short Biography</label>
                                <textarea name="bio" rows="4" placeholder="Briefly describe your expertise and how you help clients..."><?php echo esc_textarea( $meta['bio'] ); ?></textarea>
                                <p class="field-hint">Use 2-3 sentences to build authority. Sample: "Ex-Google Exec turned Strategic Coach. I help high-ticket service providers automate their acquisition."</p>
                            </div>

                            <div class="field">
                                <label>Social Links (for vCard & Discovery)</label>
                                <div class="grid-2 gap-10">
                                    <input type="url" name="social_links[twitter]" value="<?php echo esc_url($meta['social_links']['twitter'] ?? ''); ?>" placeholder="𝕏 / Twitter URL">
                                    <input type="url" name="social_links[linkedin]" value="<?php echo esc_url($meta['social_links']['linkedin'] ?? ''); ?>" placeholder="LinkedIn URL">
                                    <input type="url" name="social_links[instagram]" value="<?php echo esc_url($meta['social_links']['instagram'] ?? ''); ?>" placeholder="Instagram URL">
                                    <input type="url" name="social_links[youtube]" value="<?php echo esc_url($meta['social_links']['youtube'] ?? ''); ?>" placeholder="YouTube URL">
                                </div>
                            </div>
                            <div class="field">
                                <label>Your Niche / Category</label>
                                <select name="niche" id="profile-niche">
                                    <?php
                                    $niche = get_post_meta($profile_id, '_saas_niche', true);
                                    $niches = [
                                        'servant' => 'Public Servant / Official',
                                        'coach' => 'Business Coach',
                                        'creator' => 'Digital Creator',
                                        'realtor' => 'Real Estate Pro',
                                        'business' => 'Corporate Entity',
                                        'speaker' => 'Public Speaker',
                                        'author' => 'Author / Writer',
                                        'consultant' => 'Strategy Consultant',
                                        'lawyer' => 'Lawyer / Legal',
                                        'doctor' => 'Doctor / Healthcare',
                                        'artist' => 'Artist / Designer',
                                        'agency' => 'Agency Owner',
                                        'freelancer' => 'Creative Freelancer',
                                        'tiktok' => 'Influencer / TikTok',
                                        'luxury' => 'Luxury Advisory'
                                    ];
                                    foreach($niches as $k => $v) : ?>
                                        <option value="<?php echo $k; ?>" <?php selected($niche, $k); ?>><?php echo $v; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="grid-2 gap-20">
                                <div class="field">
                                    <label>Company / Organization</label>
                                    <input type="text" name="company" value="<?php echo esc_attr(get_post_meta($profile_id, '_saas_company', true)); ?>">
                                </div>
                                <div class="field">
                                    <label>Public Phone (for vCard)</label>
                                    <input type="text" name="phone" value="<?php echo esc_attr(get_post_meta($profile_id, '_saas_phone', true)); ?>" placeholder="+1 234 567 890">
                                </div>
                            </div>
                            <div class="field <?php echo $is_pro ? '' : 'pro-gated-inline'; ?>">
                                <div class="flex flex-between flex-center mb-10">
                                    <label class="mb-0">Custom Domain / Subdomain (Pro)</label>
                                    <button type="button" id="saas-domain-guide-trigger" class="button text-xs p-4-10 bg-primary-soft color-primary border-primary">❓ How to setup?</button>
                                </div>
                                <input type="text" name="custom_domain" value="<?php echo esc_attr(get_post_meta($profile_id, '_saas_custom_domain', true)); ?>" placeholder="profile.yourdomain.com">
                                <div class="bg-faint border-primary-left p-12 radius-8 mt-10 text-sm lh-1-5">
                                    <strong>🚀 Quick Start:</strong>
                                    <ol class="m-8-0-0-18 p-0">
                                        <li>Login to your domain provider (e.g. GoDaddy, Namecheap).</li>
                                        <li>Add a <strong>CNAME</strong> record pointing your subdomain (e.g. <em>bio</em>) to <code><?php echo parse_url(home_url(), PHP_URL_HOST); ?></code></li>
                                        <li>Enter your full domain above and click Update Profile.</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="grid-2 gap-20">
                                <div class="field">
                                    <label><input type="checkbox" name="show_in_directory" value="1" <?php checked(get_post_meta($profile_id, '_saas_show_in_directory', true), 1); ?>> Show in Directory</label>
                                </div>
                                <div class="field <?php echo $is_pro ? '' : 'pro-gated-inline'; ?>">
                                    <label><input type="checkbox" name="verified_badge" value="1" <?php checked(get_post_meta($profile_id, '_saas_verified_badge', true), 1); ?>> Verified Badge (Pro)</label>
                                </div>
                            </div>

                            <div class="field <?php echo $is_pro ? '' : 'pro-gated-inline'; ?>">
                                <label>Profile Password Protection (Pro)</label>
                                <input type="text" name="profile_password" value="<?php echo esc_attr(get_post_meta($profile_id, '_saas_profile_password', true)); ?>" placeholder="Leave blank for public access">
                            </div>

                            <button type="submit" class="btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>

                <div id="tab-branding" class="saas-tab-content">
                    <div class="dashboard-card">
                        <h3>Style & Identity</h3>
                        <form id="saas-branding-form">
                            <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                            <input type="hidden" name="form_context" value="branding">

                            <div class="field">
                                <label>Base Theme</label>
                                <select name="profile_theme" id="profile-theme-select">
                                    <option value="light" <?php selected(get_post_meta($profile_id, '_saas_profile_theme', true), 'light'); ?>>Light Mode (Clean & Minimal)</option>
                                    <option value="dark" <?php selected(get_post_meta($profile_id, '_saas_profile_theme', true), 'dark'); ?>>Dark Mode (Modern & Bold)</option>
                                    <option value="vibrant" <?php selected(get_post_meta($profile_id, '_saas_profile_theme', true), 'vibrant'); ?>>Vibrant (Creative & Energetic)</option>
                                    <option value="luxury" <?php selected(get_post_meta($profile_id, '_saas_profile_theme', true), 'luxury'); ?>>Luxury (Elite & Premium)</option>
                                    <option value="modern-glass" <?php selected(get_post_meta($profile_id, '_saas_profile_theme', true), 'modern-glass'); ?>>Modern Glass (Sleek & Transparent)</option>
                                    <option value="midnight-neon" <?php selected(get_post_meta($profile_id, '_saas_profile_theme', true), 'midnight-neon'); ?>>Midnight Neon (Bold & Futuristic)</option>
                                </select>
                                <p class="field-hint"><strong>Recommendation:</strong> Use "Luxury" if you sell high-ticket services ($2,000+).</p>
                            </div>

                            <div class="field" id="saas-bg-value-wrapper">
                                <label id="saas-bg-value-label">Background Value</label>
                                <input type="text" name="bg_value" id="saas-bg-value-input" value="<?php echo esc_attr($profile_bg_val ?: '#f3f3f1'); ?>">
                                <p class="field-hint">Flat: #f3f3f1 | Gradient: linear-gradient(135deg, #4f46e5 0%, #a29bfe 100%)</p>
                            </div>

                            <div class="field">
                                <label>Card Elevation (Shadow)</label>
                                <select name="container_shadow">
                                    <option value="soft" <?php selected(get_post_meta($profile_id, '_saas_container_shadow', true), 'soft'); ?>>Soft Glow</option>
                                    <option value="hard" <?php selected(get_post_meta($profile_id, '_saas_container_shadow', true), 'hard'); ?>>Hard Edge (Brutalism)</option>
                                    <option value="none" <?php selected(get_post_meta($profile_id, '_saas_container_shadow', true), 'none'); ?>>Flat (Minimalist)</option>
                                </select>
                            </div>

                            <div class="field">
                                <label>Typography</label>
                                <select name="font_family">
                                    <option value="'Inter', sans-serif" <?php selected(get_post_meta($profile_id, '_saas_font_family', true), "'Inter', sans-serif"); ?>>Inter (Modern)</option>
                                    <option value="'Montserrat', sans-serif" <?php selected(get_post_meta($profile_id, '_saas_font_family', true), "'Montserrat', sans-serif"); ?>>Montserrat (Bold)</option>
                                    <option value="'Playfair Display', serif" <?php selected(get_post_meta($profile_id, '_saas_font_family', true), "'Playfair Display', serif"); ?>>Playfair (Elegant)</option>
                                </select>
                            </div>

                            <div class="field">
                                <label><input type="checkbox" name="social_proof" value="1" <?php checked(get_post_meta($profile_id, '_saas_social_proof', true), 1); ?>> Show Social Proof Bubble (e.g. "100 people viewed")</label>
                            </div>

                            <div class="field <?php echo $is_pro ? '' : 'pro-gated-inline'; ?>">
                                <label><input type="checkbox" name="hide_branding" value="1" <?php checked(get_post_meta($profile_id, '_saas_hide_branding', true), 1); ?>> Hide "Powered by" Branding (Pro)</label>
                            </div>

                            <div class="field">
                                <label>Theme Accent Color</label>
                                <input type="color" name="theme_color" value="<?php echo esc_attr( $meta['theme_color'] ); ?>">
                            </div>

                            <div class="field">
                                <label>Background Engine</label>
                                <select name="bg_type" id="profile-bg-type">
                                    <option value="flat" <?php selected(get_post_meta($profile_id, '_saas_bg_type', true), 'flat'); ?>>Clean Flat</option>
                                    <option value="gradient" <?php selected(get_post_meta($profile_id, '_saas_bg_type', true), 'gradient'); ?>>Modern Gradient</option>
                                    <option value="mesh" <?php selected(get_post_meta($profile_id, '_saas_bg_type', true), 'mesh'); ?>>Elite Mesh (Pro)</option>
                                    <option value="particles" <?php selected(get_post_meta($profile_id, '_saas_bg_type', true), 'particles'); ?>>Interactive Particles (Pro)</option>
                                </select>
                            </div>

                            <div class="field">
                                <label>Button Aesthetics</label>
                                <select name="btn_shape">
                                    <option value="pill" <?php selected(get_post_meta($profile_id, '_saas_btn_shape', true), 'pill'); ?>>Pill (Max Rounded)</option>
                                    <option value="rounded" <?php selected(get_post_meta($profile_id, '_saas_btn_shape', true), 'rounded'); ?>>Rounded Corners</option>
                                    <option value="square" <?php selected(get_post_meta($profile_id, '_saas_btn_shape', true), 'square'); ?>>Sharp Square</option>
                                </select>
                            </div>

                            <div class="field">
                                <label>Quick Style Presets</label>
                                <div class="grid-presets gap-10">
                                    <button type="button" class="preset-btn button" data-preset="midnight">🌑 Midnight</button>
                                    <button type="button" class="preset-btn button" data-preset="glassy">💎 Glassy</button>
                                    <button type="button" class="preset-btn button" data-preset="vibrant">🌈 Vibrant</button>
                                    <button type="button" class="preset-btn button" data-preset="minimal">⚪ Minimal</button>
                                    <button type="button" class="preset-btn button" data-preset="luxury">⚜️ Luxury</button>
                                </div>
                            </div>
                            <button type="submit" class="btn-primary">Apply Styles</button>
                        </form>
                    </div>
                </div>

                <div id="tab-custom_css" class="saas-tab-content">
                    <div class="dashboard-card">
                        <h3>✨ Custom CSS</h3>
                        <p class="field-hint">Add your own CSS to override any part of the theme. This feature allows for 100% brand alignment and is exclusive to Elite Pro users.</p>

                        <form id="saas-custom-css-form">
                            <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                            <input type="hidden" name="form_context" value="custom_css">

                            <div class="field <?php echo $is_pro ? '' : 'pro-gated-inline'; ?>">
                                <label>Your Custom CSS</label>
                                <textarea name="custom_css" rows="15" placeholder="/* Custom styles for your profile */" class="font-mono text-xs"><?php echo esc_textarea(get_post_meta($profile_id, '_saas_custom_css', true)); ?></textarea>
                            </div>

                            <button type="submit" class="btn-primary">Save CSS Changes</button>
                        </form>
                    </div>
                </div>

                <div id="tab-leads" class="saas-tab-content">
                    <div class="dashboard-card">
                        <div class="flex-between flex-center mb-20">
                            <h3>Captured Leads</h3>
                            <div class="flex gap-10">
                                <input type="text" id="lead-search" placeholder="Search leads..." class="button bg-white text-left">
                                <a href="<?php echo admin_url('admin-ajax.php?action=saas_export_leads&security='.wp_create_nonce('saas_export_nonce')); ?>" class="button">📥 Export CSV</a>
                            </div>
                        </div>
                        <div class="saas-table-wrapper">
                            <?php
                            $leads = get_posts(['post_type' => 'saas_lead', 'author' => $user_id, 'numberposts' => 50]);
                            if ($leads) : ?>
                                <button id="saas-bulk-delete-leads" class="button mb-10 color-danger display-none">🗑️ Delete Selected</button>
                                <table class="saas-table">
                                    <thead><tr><th><input type="checkbox" id="leads-select-all"></th><th>Name</th><th>Email</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($leads as $lead) :
                                            $status = get_post_meta($lead->ID, '_saas_lead_status', true) ?: 'New';
                                            ?>
                                            <tr>
                                                <td><input type="checkbox" class="lead-checkbox" value="<?php echo $lead->ID; ?>"></td>
                                                <td><?php echo esc_html(get_post_meta($lead->ID, '_saas_lead_name', true)); ?></td>
                                                <td><?php echo esc_html(get_post_meta($lead->ID, '_saas_lead_email', true)); ?></td>
                                                <td><span class="pro-badge" style="background:<?php echo ($status==='New') ? 'var(--primary)' : 'var(--secondary)'; ?>"><?php echo esc_html($status); ?></span></td>
                                                <td><?php echo get_the_date('M j', $lead->ID); ?></td>
                                                <td><button class="view-lead button" data-id="<?php echo $lead->ID; ?>">View</button></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else : ?>
                                <p class="color-muted">No leads captured yet. Your funnel is ready to go!</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div id="tab-analytics" class="saas-tab-content">
                    <div class="dashboard-card">
                        <div class="flex-between flex-center mb-20">
                            <h3>Performance</h3>
                            <a href="<?php echo admin_url('admin-ajax.php?action=saas_export_analytics&security='.wp_create_nonce('saas_export_nonce')); ?>" class="button">📥 Export Stats</a>
                        </div>
                        <div class="h-280 mb-24"><canvas id="saas-analytics-chart"></canvas></div>
                        <?php
                        $user_activity = $analytics->get_user_activity_over_time($user_id);
                        $u_labels = array_column($user_activity, 'date');
                        $u_views = array_column($user_activity, 'views');
                        $u_clicks = array_column($user_activity, 'clicks');
                        ?>
                        <script>
                            var saas_chart_data = {
                                labels: <?php echo json_encode($u_labels); ?>,
                                views: <?php echo json_encode($u_views); ?>,
                                clicks: <?php echo json_encode($u_clicks); ?>
                            };
                        </script>

                        <div class="saas-ab-testing-results mb-40">
                            <h4>A/B Testing Insights</h4>
                            <div class="h-200"><canvas id="saas-ab-chart"></canvas></div>
                            <?php
                            $total_a = 0; $total_b = 0;
                            foreach($link_stats as $ls) {
                                $total_a += $ls->clicks;
                                $total_b += $ls->clicks_b;
                            }
                            ?>
                            <script>
                                var saas_ab_data = { a: <?php echo $total_a; ?>, b: <?php echo $total_b; ?> };
                            </script>
                        </div>

                        <?php $stats = $analytics->get_user_summary($user_id); ?>
                        <div class="stats-grid">
                            <div class="stat-card"><small>VIEWS</small><div class="value"><?php echo number_format($stats['views']); ?></div></div>
                            <div class="stat-card"><small>CLICKS</small><div class="value"><?php echo number_format($stats['clicks']); ?></div></div>
                            <div class="stat-card"><small>NFC TAPS</small><div class="value color-primary"><?php echo number_format($stats['nfc']); ?></div></div>
                            <div class="stat-card"><small>CONV. RATE</small><div class="value color-secondary"><?php echo ($stats['views'] > 0) ? round(($stats['leads'] / $stats['views']) * 100, 1) : 0; ?>%</div></div>
                            <div class="stat-card"><small>LEADS</small><div class="value color-accent"><?php echo number_format($stats['leads']); ?></div></div>
                        </div>

                        <div class="saas-insights-row grid-insights gap-20 mt-40">
                            <div class="insight-card">
                                <h5>Traffic Sources</h5>
                                <ul class="insight-list">
                                    <?php foreach($stats['referrers'] as $ref): ?>
                                        <li><span class="flex-between"><span><?php echo esc_html($ref->referrer ?: 'Direct'); ?></span> <strong><?php echo $ref->count; ?></strong></span></li>
                                    <?php endforeach; ?>
                                    <?php if(empty($stats['referrers'])) echo '<li><small>No data yet</small></li>'; ?>
                                </ul>
                            </div>
                            <div class="insight-card">
                                <h5>Top Countries</h5>
                                <ul class="insight-list">
                                    <?php foreach($stats['countries'] as $c): ?>
                                        <li><span class="flex-between"><span><?php echo esc_html($c->country_code); ?></span> <strong><?php echo $c->count; ?></strong></span></li>
                                    <?php endforeach; ?>
                                    <?php if(empty($stats['countries'])) echo '<li><small>No data yet</small></li>'; ?>
                                </ul>
                            </div>
                            <div class="insight-card">
                                <h5>Device Types</h5>
                                <ul class="insight-list">
                                    <?php foreach($stats['devices'] as $d): ?>
                                        <li><span class="flex-between"><span><?php echo esc_html($d->label); ?></span> <strong><?php echo $d->count; ?></strong></span></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-40">
                            <h4>Top Performing Blocks</h4>
                            <div class="saas-table-wrapper">
                                <table class="saas-table">
                                    <thead><tr><th>Block</th><th>Type</th><th>Clicks</th><th>AB Result</th></tr></thead>
                                    <tbody>
                                        <?php
                                        $block_stats = $analytics->get_user_link_stats($user_id);
                                        foreach($links as $l) :
                                            $sid = $l->ID;
                                            $ca = isset($block_stats[$sid]) ? $block_stats[$sid]->clicks : 0;
                                            $cb = isset($block_stats[$sid]) ? $block_stats[$sid]->clicks_b : 0;
                                            ?>
                                            <tr>
                                                <td><?php echo esc_html($l->post_title); ?></td>
                                                <td><small><?php echo get_post_meta($sid, '_saas_block_type', true); ?></small></td>
                                                <td><strong><?php echo $ca + $cb; ?></strong></td>
                                                <td><?php echo $cb > 0 ? "<small>A:$ca B:$cb</small>" : '-'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-40">
                            <h4>Revenue & Orders</h4>
                            <div class="stats-grid mb-20">
                                <div class="stat-card bg-secondary-soft">
                                    <small>TOTAL REVENUE</small>
                                    <?php
                                    $total_rev = 0;
                                    $all_orders = get_posts(['post_type' => 'saas_order', 'author' => $user_id, 'meta_key' => '_saas_order_status', 'meta_value' => 'completed', 'numberposts' => -1]);
                                    foreach($all_orders as $o) $total_rev += floatval(get_post_meta($o->ID, '_saas_order_amount', true));
                                    ?>
                                    <div class="value color-secondary">$<?php echo number_format($total_rev, 2); ?></div>
                                </div>
                                <div class="stat-card">
                                    <small>COMPLETED SALES</small>
                                    <div class="value"><?php echo count($all_orders); ?></div>
                                </div>
                            </div>
                            <div class="saas-table-wrapper">
                                <table class="saas-table">
                                    <thead><tr><th>Date</th><th>Description</th><th>Amount</th><th>Status</th></tr></thead>
                                    <tbody>
                                        <?php
                                        $recent_orders = get_posts(['post_type' => 'saas_order', 'author' => $user_id, 'numberposts' => 10]);
                                        if ($recent_orders) :
                                            foreach($recent_orders as $o) :
                                                $amt = get_post_meta($o->ID, '_saas_order_amount', true);
                                                $st = get_post_meta($o->ID, '_saas_order_status', true);
                                                ?>
                                                <tr>
                                                    <td><?php echo get_the_date('M j', $o->ID); ?></td>
                                                    <td><?php echo esc_html($o->post_title); ?></td>
                                                    <td>$<?php echo number_format($amt, 2); ?></td>
                                                    <td><span class="pro-badge" style="background:<?php echo ($st==='completed') ? 'var(--secondary)' : '#94a3b8'; ?>"><?php echo ucfirst($st); ?></span></td>
                                                </tr>
                                            <?php endforeach;
                                        else : ?>
                                            <tr><td colspan="4" class="text-center color-lighter">No sales recorded yet.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="tab-share" class="saas-tab-content">
                    <div class="dashboard-card text-center">
                        <h3>Share Your Identity</h3>
                        <div class="mv-20">
                            <img src="<?php echo saas_get_profile_qr_url($profile_obj->post_name, get_post_meta($profile_id, '_saas_qr_color', true) ?: '#000000'); ?>" class="qr-code-img border-white-5 shadow-sm">
                        </div>
                        <p>Download your custom QR code for business cards and marketing materials.</p>

                        <form id="saas-qr-form" class="max-w-300 mx-auto mb-20">
                            <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                            <input type="hidden" name="form_context" value="qr">
                            <div class="field">
                                <label>QR Code Color</label>
                                <input type="color" name="qr_color" value="<?php echo esc_attr(get_post_meta($profile_id, '_saas_qr_color', true) ?: '#000000'); ?>" onchange="$(this).closest('form').submit()">
                            </div>
                        </form>

                        <div class="grid-2 gap-10 max-w-400 mx-auto mb-20">
                            <a href="<?php echo home_url('/?saas_action=vcard&profile='.$profile_id); ?>" class="button full-width">📥 Get vCard</a>
                            <button class="button" onclick="window.print()">🖨️ Print Card</button>
                        </div>
                        <hr>
                        <h4>Social Story Card (Elite Pro)</h4>
                        <div id="saas-story-card-preview" class="story-card-preview mx-auto mb-20 relative p-30 color-white overflow-hidden flex-column" style="background:linear-gradient(135deg, <?php echo $meta['theme_color']; ?> 0%, #000 100%);">
                             <div class="text-center">
                                 <?php if ( has_post_thumbnail( $profile_id ) ) : ?>
                                    <?php echo get_the_post_thumbnail( $profile_id, 'thumbnail', ['class' => 'avatar-fixed-80 radius-50p border-white-3']); ?>
                                 <?php endif; ?>
                                 <h3 class="m-10-0-5 text-lg"><?php echo esc_html($profile_obj->post_title); ?></h3>
                                 <p class="text-xs opacity-80"><?php echo esc_html($meta['headline']); ?></p>
                             </div>
                             <div class="mt-auto pb-40 text-center">
                                <img src="<?php echo saas_get_profile_qr_url($profile_obj->post_name, '#ffffff'); ?>" class="w-100 radius-10">
                                <p class="text-sm mt-10 font-bold">Scan to Connect</p>
                             </div>
                        </div>
                        <a href="<?php echo home_url('/story-card?profile_id='.$profile_id); ?>" target="_blank" class="button">📥 View & Download Story Card</a>

                        <hr>
                        <h4>NFC Configuration</h4>
                        <p class="text-sm color-muted">Program your NFC tag to point to: <br><code><?php echo home_url('/' . $profile_obj->post_name . '?src=nfc'); ?></code></p>
                    </div>
                </div>

                <div id="tab-tracking" class="saas-tab-content">
                    <div class="dashboard-card">
                        <h3>Tracking & Pixels</h3>
                        <p class="text-sm color-muted mb-20">Add Google Analytics, Facebook Pixel, or custom tracking scripts. (Elite Pro Feature)</p>
                        <form id="saas-tracking-form">
                            <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                            <input type="hidden" name="form_context" value="tracking">
                            <div class="field <?php echo $is_pro ? '' : 'pro-gated-inline'; ?>">
                                <label>Header Scripts (e.g. Google Tag Manager)</label>
                                <textarea name="header_scripts" rows="5" placeholder="<script async src='https://www.googletagmanager.com/gtag/js?id=G-XXXXXX'></script>..."><?php echo esc_textarea(get_post_meta($profile_id, '_saas_header_scripts', true)); ?></textarea>
                                <p class="field-hint">Paste your tracking code here to have it included in the &lt;head&gt; of your profile.</p>
                            </div>
                            <div class="field <?php echo $is_pro ? '' : 'pro-gated-inline'; ?>">
                                <label>Footer Scripts (e.g. Conversion Pixels)</label>
                                <textarea name="footer_scripts" rows="5" placeholder="<script>fbq('track', 'PageView');</script>"><?php echo esc_textarea(get_post_meta($profile_id, '_saas_footer_scripts', true)); ?></textarea>
                                <p class="field-hint">Scripts placed here will be loaded just before the closing &lt;/body&gt; tag.</p>
                            </div>
                            <button type="submit" class="btn-primary">Save Scripts</button>
                        </form>
                    </div>
                </div>

                <div id="tab-seo" class="saas-tab-content">
                    <div class="dashboard-card">
                        <h3>Search Engine Optimization</h3>
                        <form id="saas-seo-form">
                            <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                            <input type="hidden" name="form_context" value="seo">
                            <div class="field">
                                <label>Meta Title</label>
                                <input type="text" name="meta_title" value="<?php echo esc_attr(get_post_meta($profile_id, '_saas_seo_title', true)); ?>" placeholder="Example: John Doe | Digital Marketing Consultant">
                            </div>
                            <div class="field">
                                <label>Meta Description</label>
                                <textarea name="meta_desc" rows="3" placeholder="A short summary of your profile for search engines."><?php echo esc_textarea(get_post_meta($profile_id, '_saas_seo_desc', true)); ?></textarea>
                            </div>
                            <div class="field <?php echo $is_pro ? '' : 'pro-gated-inline'; ?>">
                                <label>Custom Favicon URL (Pro)</label>
                                <input type="url" name="favicon" value="<?php echo esc_url(get_post_meta($profile_id, '_saas_favicon', true)); ?>" placeholder="https://yoursite.com/favicon.ico">
                            </div>

                            <div class="bg-light p-20 radius-15 border-light mb-20">
                                <h4 class="m-0-0-10 text-sm color-muted">Google Search Preview</h4>
                                <div class="color-link text-lg mb-2"><?php echo get_post_meta($profile_id, '_saas_seo_title', true) ?: $profile_obj->post_title; ?></div>
                                <div class="color-success text-sm mb-5"><?php echo home_url('/' . $profile_obj->post_name); ?></div>
                                <div class="color-dark-alt text-xs lh-1-4"><?php echo get_post_meta($profile_id, '_saas_seo_desc', true) ?: 'Check out my digital identity and conversion funnel.'; ?></div>
                            </div>

                            <button type="submit" class="btn-primary">Save SEO Settings</button>
                        </form>
                    </div>
                </div>

                <div id="tab-automation" class="saas-tab-content">
                    <div class="dashboard-card">
                        <h3>Settings & Rules</h3>
                        <form id="saas-automation-form">
                            <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                            <input type="hidden" name="form_context" value="automation">

                            <h4>Lead Form Customization</h4>
                            <div class="grid-2 gap-20 mb-20">
                                <div>
                                    <div class="field">
                                        <label><input type="checkbox" name="form_field_phone" value="1" <?php checked(get_post_meta($profile_id, '_saas_form_phone', true), 1); ?>> Enable Phone Field</label>
                                    </div>
                                    <div class="field">
                                        <label>Phone Label</label>
                                        <input type="text" name="form_label_phone" value="<?php echo esc_attr(get_post_meta($profile_id, '_saas_form_label_phone', true) ?: 'Phone Number'); ?>">
                                    </div>
                                    <div class="field">
                                        <label><input type="checkbox" name="form_req_phone" value="1" <?php checked(get_post_meta($profile_id, '_saas_form_req_phone', true), 1); ?>> Phone Required</label>
                                    </div>
                                </div>
                                <div>
                                    <div class="field">
                                        <label><input type="checkbox" name="form_field_msg" value="1" <?php checked(get_post_meta($profile_id, '_saas_form_msg', true), 1); ?>> Enable Message Field</label>
                                    </div>
                                    <div class="field">
                                        <label>Message Label</label>
                                        <input type="text" name="form_label_msg" value="<?php echo esc_attr(get_post_meta($profile_id, '_saas_form_label_msg', true) ?: 'Your Message'); ?>">
                                    </div>
                                    <div class="field">
                                        <label><input type="checkbox" name="form_req_msg" value="1" <?php checked(get_post_meta($profile_id, '_saas_form_req_msg', true), 1); ?>> Message Required</label>
                                    </div>
                                </div>
                            </div>

                            <div class="field">
                                <label>Success Message</label>
                                <input type="text" name="lead_success_msg" value="<?php echo esc_attr(get_post_meta($profile_id, '_saas_lead_success_msg', true) ?: 'Thank you! We will be in touch soon.'); ?>">
                            </div>

                            <hr>
                            <h4>Advanced Triggers</h4>
                            <div class="field <?php echo $payments->is_agency_user($user_id) ? '' : 'pro-gated-inline'; ?>" data-tier="agency">
                                <label>Webhook URL (Zapier/Make) <span class="pro-badge bg-dark color-gold border-gold">Agency</span></label>
                                <input type="url" name="lead_webhook" value="<?php echo esc_url(get_post_meta($profile_id, '_saas_lead_webhook', true)); ?>" <?php echo $payments->is_agency_user($user_id) ? '' : 'readonly'; ?> placeholder="https://hooks.zapier.com/v1/event/...">
                                <p class="field-hint">Automatically send new leads to Zapier, Make, or your own API. Test with a sample payload using the "Test Webhook" button.</p>
                            </div>
                            <div class="field">
                                <label>Redirect after Submission</label>
                                <input type="url" name="lead_redirect" value="<?php echo esc_url(get_post_meta($profile_id, '_saas_lead_redirect', true)); ?>">
                            </div>
                            <div class="field">
                                <label>Lead Magnet URL (Auto-download)</label>
                                <input type="url" name="lead_magnet_url" value="<?php echo esc_url(get_post_meta($profile_id, '_saas_lead_magnet_url', true)); ?>">
                            </div>
                            <div class="field">
                                <label><input type="checkbox" name="lead_auto_respond" value="1" <?php checked(get_post_meta($profile_id, '_saas_lead_auto_respond', true), 1); ?>> Enable Email Auto-responder</label>
                            </div>
                            <div class="field">
                                <label>Auto-reply Message</label>
                                <textarea name="lead_auto_msg" rows="3"><?php echo esc_textarea(get_post_meta($profile_id, '_saas_lead_auto_msg', true)); ?></textarea>
                            </div>
                            <div class="flex gap-10 mb-20">
                                <button type="submit" class="btn-primary flex-2">Save Rules</button>
                                <button type="button" id="saas-test-webhook-btn" class="button flex-1">Test Webhook</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="tab-integrations" class="saas-tab-content">
                    <div class="dashboard-card">
                        <h3>Third-Party Sync</h3>
                        <form id="saas-integrations-form">
                            <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>">
                            <input type="hidden" name="form_context" value="integrations">
                            <div class="field">
                                <label>Mailchimp API Key</label>
                                <div class="flex gap-10">
                                    <input type="password" name="mailchimp_api" value="<?php echo esc_attr(get_post_meta($profile_id, '_saas_mailchimp_api', true)); ?>" class="flex-1" placeholder="Paste your API key here">
                                    <button type="button" class="button check-integration" data-platform="mailchimp">Test Connection</button>
                                </div>
                                <p class="field-hint">Automatically sync new leads to your Mailchimp audience. Found in Account > Extras > API keys.</p>
                            </div>
                            <div class="field">
                                <label>Mailchimp Audience ID (List ID)</label>
                                <input type="text" name="mailchimp_list" value="<?php echo esc_attr(get_post_meta($profile_id, '_saas_mailchimp_list', true)); ?>" placeholder="e.g. 1a2b3c4d5e">
                                <p class="field-hint">The unique ID for your subscriber list.</p>
                            </div>
                            <div class="field">
                                <label>HubSpot Access Token</label>
                                <div class="flex gap-10">
                                    <input type="password" name="hubspot_token" value="<?php echo esc_attr(get_post_meta($profile_id, '_saas_hubspot_token', true)); ?>" class="flex-1">
                                    <button type="button" class="button check-integration" data-platform="hubspot">Test</button>
                                </div>
                            </div>
                            <button type="submit" class="btn-primary">Save API Settings</button>
                        </form>
                        <p class="text-sm color-muted mt-20">Connect your favorite CRM to sync leads automatically. Webhooks are also available in Settings. (Pro Feature)</p>
                    </div>
                </div>

                <div id="tab-referrals" class="saas-tab-content">
                    <div class="dashboard-card bg-secondary-soft border-secondary">
                        <div class="flex-between flex-center">
                            <h3 class="color-secondary mb-0">Affiliate Program</h3>
                            <span class="pro-badge bg-secondary">ACTIVE</span>
                        </div>
                        <p class="mt-15 lh-1-6">I built this tool to help consultants, and I want to reward you for spreading the word. Share your unique referral link and earn <strong><?php echo get_option('saas_affiliate_percentage') ?: 30; ?>% recurring commission</strong> for the lifetime of every user you refer.</p>

                        <?php
                        $earned = get_user_meta($user_id, '_saas_affiliate_earned', true) ?: 0;
                        $refs_count = count(get_users(['meta_key' => '_saas_referred_by', 'meta_value' => $user_id, 'fields' => 'ID']));
                        $marketing_materials = get_option('saas_marketing_materials') ?: [
                            ['name' => 'Standard Banner', 'img' => 'https://via.placeholder.com/300x100?text=Claim+Your+Elite+Bio', 'size' => '300x100'],
                            ['name' => 'Sidebar Ad', 'img' => 'https://via.placeholder.com/150x150?text=Stop+Losing+Leads', 'size' => '150x150']
                        ];
                        ?>
                        <div class="stats-grid mb-20 mt-20">
                            <div class="stat-card bg-white"><small>TOTAL EARNED</small><div class="value color-secondary">$<?php echo number_format($earned, 2); ?></div></div>
                            <div class="stat-card bg-white"><small>ACTIVE REFS</small><div class="value"><?php echo $refs_count; ?></div></div>
                        </div>

                        <div class="bg-white p-15 radius-10 border-secondary border-dashed mb-20">
                            <label class="display-block text-xs color-muted mb-5">YOUR UNIQUE LINK</label>
                            <input type="text" id="saas-ref-link" value="<?php echo home_url('/?ref=' . wp_get_current_user()->user_login); ?>" readonly class="full-width border-none font-bold bg-transparent">
                        </div>
                        <button id="saas-copy-ref-btn" class="btn-primary bg-secondary full-width">Copy Referral Link</button>

                        <div class="mt-30 pt-20 border-secondary-top">
                            <h4 class="color-secondary mb-15">Your Assigned Discount Codes</h4>
                            <?php
                            $all_coupons = get_option('saas_affiliate_coupons') ?: [];
                            $my_coupons = array_filter($all_coupons, function($c) use ($user_id) { return $c['user_id'] == $user_id; });
                            if ($my_coupons) : ?>
                                <div class="grid-presets gap-10">
                                    <?php foreach($my_coupons as $mc) : ?>
                                        <div class="bg-white p-12 radius-12 text-center shadow-sm">
                                            <code class="display-block text-lg color-secondary font-black mb-5"><?php echo esc_html($mc['code']); ?></code>
                                            <span class="text-xs font-bold color-muted"><?php echo $mc['discount']; ?>% Discount</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <p class="text-base color-muted">Ask the admin to assign you a custom coupon code to share with your audience!</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <h4 class="color-secondary mb-15">Affiliate Earning History</h4>
                        <?php
                        $commissions = get_posts(['post_type' => 'saas_commission', 'author' => $user_id, 'numberposts' => 20]);
                        if($commissions) : ?>
                            <table class="saas-table">
                                <thead><tr><th>Date</th><th>Type</th><th>Order Amount</th><th>Your Commission</th></tr></thead>
                                <tbody>
                                    <?php foreach($commissions as $c) :
                                        $order_amt = get_post_meta($c->ID, '_saas_order_amount', true);
                                        $comm_amt  = get_post_meta($c->ID, '_saas_commission_amount', true);
                                        $perc      = get_post_meta($c->ID, '_saas_percentage', true);
                                        ?>
                                        <tr>
                                            <td><?php echo get_the_date('M j, Y', $c->ID); ?></td>
                                            <td><small>Recurring (<?php echo $perc; ?>%)</small></td>
                                            <td>$<?php echo number_format($order_amt, 2); ?></td>
                                            <td class="color-secondary font-black">+$<?php echo number_format($comm_amt, 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="color-muted text-sm">No commissions earned yet. Share your link to start earning!</p>
                        <?php endif; ?>
                    </div>

                    <div class="dashboard-card">
                        <h4>Recent Payouts</h4>
                        <?php
                        $payouts = get_posts(['post_type' => 'saas_payout', 'author' => $user_id, 'numberposts' => 10]);
                        if($payouts) : ?>
                            <table class="saas-table">
                                <thead><tr><th>Date</th><th>Amount</th><th>Status</th></tr></thead>
                                <tbody>
                                    <?php foreach($payouts as $p) :
                                        $p_status = get_post_meta($p->ID, '_status', true) ?: 'pending';
                                        ?>
                                        <tr>
                                            <td><?php echo get_the_date('', $p->ID); ?></td>
                                            <td>$<?php echo number_format(get_post_meta($p->ID, '_amount', true), 2); ?></td>
                                            <td><span class="pro-badge" style="background:<?php echo ($p_status==='paid') ? 'var(--secondary)' : '#94a3b8'; ?>"><?php echo strtoupper($p_status); ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="color-muted text-sm">No payouts recorded yet.</p>
                        <?php endif; ?>
                    </div>

                    <div class="dashboard-card">
                        <h4>Referred Users</h4>
                        <?php
                        $referred_users = get_users(['meta_key' => '_saas_referred_by', 'meta_value' => $user_id, 'number' => 10]);
                        if($referred_users) : ?>
                            <table class="saas-table">
                                <thead><tr><th>User</th><th>Joined</th><th>Plan</th></tr></thead>
                                <tbody>
                                    <?php foreach($referred_users as $ru) :
                                        $u_plan = get_user_meta($ru->ID, '_saas_subscription_plan', true) ?: 'Free';
                                        ?>
                                        <tr><td><?php echo esc_html($ru->display_name); ?></td><td><?php echo date('M j, Y', strtotime($ru->user_registered)); ?></td><td><?php echo ucfirst($u_plan); ?></td></tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="color-muted text-sm">No referrals yet. Time to share your link!</p>
                        <?php endif; ?>
                    </div>


                    <div class="dashboard-card">
                        <h4>Request Payout</h4>
                        <p class="text-xs color-muted">Minimum $50.00</p>
                        <form id="saas-payout-request-form">
                            <div class="field"><label>Amount ($)</label><input type="number" name="amount" min="50" step="0.01" required></div>
                            <div class="field">
                                <label>Method</label>
                                <select name="method">
                                    <option value="paypal">PayPal</option>
                                    <option value="bank">Bank Transfer</option>
                                </select>
                            </div>
                            <div class="field"><label>Payment Email/Account</label><input type="text" name="email" required></div>
                            <button type="submit" class="btn-primary full-width bg-secondary">Submit Request</button>
                        </form>
                    </div>

                    <div class="dashboard-card">
                        <h4>Marketing Materials</h4>
                        <p class="text-sm color-muted mb-15">Use these elite assets to boost your referrals.</p>
                        <div class="grid-marketing gap-15">
                            <?php foreach($marketing_materials as $mm) : ?>
                                <div class="p-15 bg-main radius-10 border-light">
                                    <small class="font-bold display-block mb-5"><?php echo esc_html($mm['name']); ?> (<?php echo esc_html($mm['size']); ?>)</small>
                                    <img src="<?php echo esc_url($mm['img']); ?>" class="full-width radius-5 mb-10">
                                    <button class="button copy-html-btn full-width">Copy HTML</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php
                        $affiliate_kit = get_option('saas_affiliate_marketing_kit') ?: [];
                        if($affiliate_kit) : ?>
                            <div class="mt-30 pt-20 border-light-top">
                                <h4>Pro Marketing Strategy Kit</h4>
                                <div class="grid-stack gap-15">
                                    <?php foreach($affiliate_kit as $kit) : ?>
                                        <div class="p-20 bg-white radius-12 border-light">
                                            <h5 class="m-0-0-10 font-black"><?php echo esc_html($kit['title']); ?></h5>
                                            <div class="text-sm color-muted"><?php echo wp_kses_post($kit['content']); ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="dashboard-card">
                        <h4>Elite Sales Scripts</h4>
                        <p class="text-sm color-muted mb-15">Copy and paste these high-converting scripts to your favorite platforms.</p>
                        <?php
                        $scripts = get_option('saas_sales_scripts') ?: [
                            ['title' => 'Sample Outreach', 'content' => 'Hey [Name], I noticed your bio...']
                        ];
                        ?>
                        <div class="grid-stack gap-15">
                            <?php
                            $ref_link = home_url('/?ref=' . wp_get_current_user()->user_login);
                            foreach($scripts as $script) :
                                $parsed_content = str_replace(['[Link]', '[My Link]'], $ref_link, $script['content']);
                            ?>
                                <div class="p-20 bg-main radius-12 border-light">
                                    <h5 class="m-0-0-10 font-black"><?php echo esc_html($script['title']); ?></h5>
                                    <pre class="pre-wrap text-sm color-dark bg-white p-15 radius-8 border-light-dark"><?php echo esc_html($parsed_content); ?></pre>
                                    <button class="button" onclick="const p = this.previousElementSibling; const t = document.createElement('textarea'); t.value = p.innerText; document.body.appendChild(t); t.select(); document.execCommand('copy'); document.body.removeChild(t); this.innerText='Copied! ✅'; setTimeout(() => this.innerText='Copy Script', 2000);">Copy Script</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="dashboard-card">
                        <h4>Contact Affiliate Manager</h4>
                        <form id="saas-support-msg-form">
                            <input type="hidden" name="subject" value="Affiliate Inquiry">
                            <textarea name="message" rows="3" placeholder="Questions about payouts or materials?" required></textarea>
                            <button type="submit" class="button mt-10">Send Message</button>
                        </form>
                    </div>
                </div>

                <div id="tab-billing" class="saas-tab-content">
                    <div class="dashboard-card">
                        <div class="bg-primary-soft p-30 radius-20 border-primary mb-40 flex gap-30 flex-center flex-wrap">
                            <div class="text-5xl">👑</div>
                            <div class="flex-1 min-w-300">
                                <h3 class="mb-0 color-primary">Ready to Join the Elite 1%?</h3>
                                <p class="mt-10-0-0 color-dark lh-1-6">As a consultant, your time is your most valuable asset. Stop wasting it managing fragmented links. Upgrade to <strong>Elite Pro</strong> to unlock advanced lead capture, whitelabeling, and smart routing.</p>
                            </div>
                            <div class="flex-shrink-0">
                                <button class="btn-primary" onclick="window.scrollTo({top: document.getElementById('plans-anchor').offsetTop, behavior: 'smooth'})">See Pro Benefits ↓</button>
                            </div>
                        </div>

                        <h3 id="plans-anchor" class="text-center">Choose Your Path to Growth</h3>

                        <div class="max-w-500 mx-auto mb-40 text-center dashboard-card">
                            <h4 class="mt-0">Apply Elite License</h4>
                            <p class="text-sm color-muted mb-15">Have a promotional code or license key? Activate it here to upgrade your account instantly.</p>
                            <form id="saas-license-activate-form">
                                <div class="field flex gap-10">
                                    <input type="text" name="license_key" placeholder="ELITE-XXXX-XXXX-XXXX" required class="flex-1">
                                    <button type="submit" class="button">Activate Key</button>
                                </div>
                            </form>
                            <script>
                            jQuery('#saas-license-activate-form').on('submit', function(e) {
                                e.preventDefault();
                                var $btn = jQuery(this).find('button');
                                $btn.prop('disabled', true).text('Verifying...');
                                jQuery.post(saas_dashboard_data.ajax_url, jQuery(this).serialize() + '&action=saas_validate_license&security=' + saas_dashboard_data.nonce, function(res) {
                                    if(res.success) {
                                        alert(res.data);
                                        location.reload();
                                    } else {
                                        alert('Error: ' + res.data);
                                    }
                                    $btn.prop('disabled', false).text('Activate Key');
                                });
                            });
                            </script>
                        </div>

                        <div class="grid-3 gap-30 mt-30">
                            <?php
                            $pricing_json = get_option('saas_home_pricing_json');
                            $plans = json_decode($pricing_json, true) ?: [];
                            foreach ($plans as $p) :
                                $p_slug = $p['slug'] ?? 'free';
                                $is_pro_plan = ($p_slug === 'pro');
                                $is_agency_plan = ($p_slug === 'agency');
                                $is_free_plan = ($p_slug === 'free');

                                $card_class = "plan-card p-32 radius-24 relative overflow-hidden";
                                if ($is_pro_plan) $card_class .= " bg-primary-soft border-primary-2";
                                elseif ($is_agency_plan) $card_class .= " bg-dark-inner border-slate-800 color-white";
                                else $card_class .= " bg-white border-light";

                                $current_user_plan = get_user_meta($user_id, '_saas_subscription_plan', true) ?: 'free';
                                $is_current = ($current_user_plan === $p_slug);
                            ?>
                            <div class="<?php echo $card_class; ?>">
                                <?php if (isset($p['badge'])) : ?>
                                    <div class="pos-absolute-tr-rotate <?php echo $is_agency_plan ? 'bg-accent color-dark' : 'bg-primary color-white'; ?> p-5-40 text-xs font-bold"><?php echo esc_html($p['badge']); ?></div>
                                <?php endif; ?>
                                <h4 class="text-2xl m-0 <?php echo $is_pro_plan ? 'color-primary' : ($is_agency_plan ? 'color-accent' : 'color-muted'); ?>"><?php echo esc_html($p['name']); ?></h4>
                                <div class="text-4xl font-black m-16-0"><?php echo esc_html($p['price']); ?><small class="text-base"><?php echo esc_html($p['period']); ?></small></div>
                                <ul class="benefit-list mb-24 lh-2-2 text-sm text-left <?php echo $is_agency_plan ? 'color-white-70' : ''; ?>">
                                    <?php foreach ($p['features'] as $f) : ?>
                                        <li>✓ <?php echo esc_html($f); ?></li>
                                    <?php endforeach; ?>
                                </ul>

                                <?php if ($is_current) : ?>
                                    <div class="color-secondary font-bold mb-15">✓ Your <?php echo esc_html($p['name']); ?> subscription is active</div>
                                    <?php if (!$is_free_plan) : ?>
                                        <button id="saas-cancel-sub" class="button full-width color-danger <?php echo $is_agency_plan ? 'bg-transparent' : ''; ?>">Cancel Subscription</button>
                                    <?php else: ?>
                                        <button class="button full-width pointer-events-none opacity-60">Current Plan</button>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <div class="payment-options flex flex-column gap-10">
                                        <?php
                                        $gateway_mode = $payments->get_active_gateway();
                                        $btn_color_class = $is_agency_plan ? 'bg-accent color-dark' : ($is_pro_plan ? '' : 'bg-secondary');
                                        if ($gateway_mode === 'stripe' || $gateway_mode === 'user_select') : ?>
                                            <button class="btn-primary saas-checkout-btn full-width <?php echo $btn_color_class; ?>" data-gateway="stripe" data-plan="<?php echo esc_attr($p_slug); ?>">Upgrade with Stripe</button>
                                        <?php endif; ?>
                                        <?php if ($gateway_mode === 'paypal' || $gateway_mode === 'user_select') : ?>
                                            <button class="btn-primary saas-checkout-btn full-width bg-paypal" data-gateway="paypal" data-plan="<?php echo esc_attr($p_slug); ?>">Upgrade with PayPal</button>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="max-w-600 mx-auto mt-40 dashboard-card">
                        <div class="field">
                            <label>Have an affiliate coupon?</label>
                            <div class="flex gap-10">
                                <input type="text" id="saas-checkout-coupon" placeholder="Enter coupon code" class="flex-1">
                                <button type="button" class="button" id="saas-apply-checkout-coupon">Apply</button>
                            </div>
                            <div id="coupon-status" class="text-xs mt-5 font-bold"></div>
                        </div>

                        <div class="grid-2 gap-10 mt-20">
                            <button id="saas-demo-upgrade-btn" class="button bg-accent-soft border-accent color-accent">⚡ Instant Demo Upgrade</button>
                            <button id="saas-simulate-payment-btn" class="button bg-secondary-soft border-secondary color-secondary">💸 Simulate Stripe Success</button>
                        </div>

                        <?php if ($is_pro) :
                            $expiry = get_user_meta($user_id, '_saas_subscription_expiry', true);
                            $u_plan = get_user_meta($user_id, '_saas_subscription_plan', true);
                            ?>
                            <div class="mt-20 pt-20 border-light-top text-sm color-muted">
                                <strong>Plan:</strong> <?php echo strtoupper($u_plan); ?><br>
                                <strong>Next Billing:</strong> <?php echo $expiry ? date('M j, Y', $expiry) : 'Never'; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    </div>

                    <div class="dashboard-card">
                        <h4>Payment History</h4>
                        <?php
                        $orders = get_posts(['post_type' => 'saas_order', 'author' => $user_id, 'numberposts' => 10]);
                        if($orders) : ?>
                            <table class="saas-table">
                                <thead><tr><th>Date</th><th>Amount</th><th>Status</th></tr></thead>
                                <tbody>
                                    <?php foreach($orders as $o) : ?>
                                        <tr>
                                            <td><?php echo get_the_date('', $o->ID); ?></td>
                                            <td>$<?php echo get_post_meta($o->ID, '_saas_order_amount', true); ?></td>
                                            <td><?php echo ucfirst(get_post_meta($o->ID, '_saas_order_status', true)); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="color-muted">No transactions found.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="tab-templates" class="saas-tab-content">
                    <div class="dashboard-card">
                        <div class="flex-between flex-center mb-20 flex-wrap gap-15">
                            <h3 class="mb-0">Template Library</h3>
                            <input type="text" id="tpl-search" placeholder="🔍 Search niches (e.g. coach, gym)..." class="max-w-300 bg-white radius-12 border-light p-10-15">
                        </div>
                        <p class="mb-20 color-muted">Choose a high-converting template to jumpstart your profile. ⚠️ Warning: Applying a template will replace your current blocks.</p>

                        <div id="templates-grid" class="grid-templates gap-20">
                            <?php
                            $all_tpls = array_merge(saas_get_default_templates(), get_option('saas_templates') ?: []);
                            $tpl_icons = [
                                'coach' => '🚀', 'business' => '🏢', 'luxury' => '⚜️', 'freelancer' => '🎨',
                                'realtor' => '🏡', 'politician' => '🏛️', 'tiktok' => '📱', 'consultant' => '🧠',
                                'author' => '📘', 'lawyer' => '⚖️', 'doctor' => '🩺', 'influencer' => '📸', 'servant' => '🏛️', 'artist' => '🎨',
                                'podcast' => '🎙️', 'course' => '🎓', 'shop' => '🛒', 'charity' => '❤️',
                                'saas' => '💻', 'fitness' => '🏋️', 'medical' => '🏥', 'startup' => '🚀',
                                'wellness' => '🌿', 'photography' => '📷', 'restaurant' => '🍴', 'event_planner' => '✨', 'therapist' => '🧠',
                                'trainer' => '🏋️‍♀️', 'interior_design' => '🛋️', 'yoga' => '🧘', 'coffee_shop' => '☕', 'non_profit' => '🤝',
                                'travel' => '✈️', 'chef' => '👨‍🍳', 'makeup' => '💄', 'web3' => '🌐', 'gaming' => '🎮',
                                'personal' => '✨', 'mobile_app' => '📱', 'webinar' => '🎤', 'musician' => '🎵',
                                'model' => '👗', 'dentist' => '🦷', 'gym' => '💪', 'architecture' => '📐'
                            ];

                            $categories = [
                                'Business & Strategy' => ['coach', 'business', 'consultant', 'agency', 'lawyer', 'realtor', 'course', 'saas', 'startup', 'web3', 'architecture'],
                                'Creative & Social'   => ['influencer', 'tiktok', 'artist', 'freelancer', 'author', 'podcast', 'musician', 'model', 'gaming', 'personal'],
                                'Lifestyle & Wellness' => ['fitness', 'trainer', 'yoga', 'wellness', 'gym', 'chef', 'makeup', 'travel', 'photography', 'interior_design'],
                                'Public & Professional' => ['servant', 'politician', 'doctor', 'dentist', 'therapist', 'speaker', 'luxury', 'charity', 'medical', 'non_profit', 'restaurant', 'coffee_shop', 'event_planner', 'mobile_app', 'webinar']
                            ];

                            foreach ($categories as $cat_title => $tpl_ids) : ?>
                                <div class="templates-category-header grid-column-all mt-30 border-light-bottom pb-10">
                                    <h4 class="m-0 text-uppercase ls-1 color-muted text-xs"><?php echo $cat_title; ?></h4>
                                </div>
                                <?php foreach($tpl_ids as $id) :
                                    if (!isset($all_tpls[$id])) continue;
                                    $icon = $tpl_icons[$id] ?? '✨';
                                ?>
                                    <div class="template-card border-light p-20 radius-15 text-center transition-base bg-white cursor-pointer hover-lift">
                                        <div class="text-4xl mb-10"><?php echo $icon; ?></div>
                                        <h4 class="m-0-0-15 text-base"><?php echo esc_html(ucfirst(str_replace('_', ' ', $id))); ?></h4>
                                        <button class="button apply-template-btn full-width bg-primary color-white border-none radius-8 p-10 font-bold cursor-pointer transition-opacity-2">Apply Template</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div id="tab-training" class="saas-tab-content">
                    <div class="dashboard-card">
                        <h3>Elite Training Academy 🎓</h3>
                        <p>I want you to succeed. That's why I've put together these short, high-impact tutorials to help you master your new digital salesman.</p>

                        <?php
                        $training_vids = get_option('saas_training_academy') ?: [
                            ['title' => 'The 60-Second Setup', 'desc' => 'Go from zero to a live funnel in under a minute.', 'video_id' => 'setup'],
                            ['title' => 'Lead Magnet Magic', 'desc' => 'Capture contact info and build your email list.', 'video_id' => 'leads']
                        ];
                        $kb_articles = get_option('saas_knowledge_base') ?: [
                            ['title' => 'How to connect my own domain?', 'url' => '#'],
                            ['title' => 'Setting up Stripe for product sales', 'url' => '#']
                        ];
                        ?>

                        <div class="grid-training gap-20 mt-30">
                            <?php foreach($training_vids as $v) : ?>
                                <div class="bg-main p-20 radius-15 border-light">
                                    <div class="h-150 bg-black-gradient radius-10 mb-15 flex-center color-white text-4xl cursor-pointer" onclick="alert('Tutorial [<?php echo esc_js($v['video_id']); ?>] loading...')">▶️</div>
                                    <h4><?php echo esc_html($v['title']); ?></h4>
                                    <p class="field-hint"><?php echo esc_html($v['desc']); ?></p>
                                    <button class="button full-width">Watch Now</button>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr class="mv-40">
                        <h4>Knowledge Base</h4>
                        <ul class="list-none p-0">
                            <?php foreach($kb_articles as $art) : ?>
                                <li class="p-15-0 border-light-bottom"><a href="<?php echo esc_url($art['url']); ?>" class="inherit-link color-primary font-bold"><?php echo esc_html($art['title']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div id="tab-account" class="saas-tab-content">
                    <div class="dashboard-card">
                        <h3>Account Settings</h3>
                        <p class="field-hint">Manage your personal information and security.</p>

                        <form id="saas-account-form">
                            <div class="field">
                                <label>Display Name</label>
                                <input type="text" name="display_name" value="<?php echo esc_attr(wp_get_current_user()->display_name); ?>">
                            </div>
                            <div class="field">
                                <label>Email Address</label>
                                <input type="email" name="user_email" value="<?php echo esc_attr(wp_get_current_user()->user_email); ?>">
                            </div>
                            <div class="field">
                                <label>New Password (Leave blank to keep current)</label>
                                <input type="password" name="new_password">
                            </div>
                            <button type="submit" class="btn-primary">Save Account Details</button>
                        </form>
                    </div>
                </div>

                <div id="tab-inbox" class="saas-tab-content">
                    <div class="dashboard-card">
                        <div class="flex-between flex-center mb-20">
                            <h3>System Messages</h3>
                            <button class="button" onclick="document.getElementById('saas-new-msg-modal').style.display='flex'">Compose</button>
                        </div>
                        <div id="saas-message-list">
                            <?php
                            $messages = get_posts([
                                'post_type' => 'saas_message',
                                'meta_key' => '_saas_msg_recipient',
                                'meta_value' => $user_id,
                                'numberposts' => 20
                            ]);
                            if($messages) :
                                foreach($messages as $m) :
                                    $status = get_post_meta($m->ID, '_saas_msg_status', true);
                                    $item_class = 'message-item ' . $status . ' p-15 border-light-bottom' . ($status == 'unread' ? ' bg-primary-soft' : '');
                                    ?>
                                    <div class="<?php echo $item_class; ?>">
                                        <div class="flex-between">
                                            <strong><?php echo esc_html($m->post_title); ?></strong>
                                            <small><?php echo get_the_date('M j', $m->ID); ?></small>
                                        </div>
                                        <p class="m-10-0 text-sm"><?php echo wp_trim_words($m->post_content, 20); ?></p>
                                    <button class="button view-message" data-id="<?php echo $m->ID; ?>">Read Full Message</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="color-muted">No messages yet. Check back later for system updates.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div> <!-- End Main Area -->

        </div>

        <!-- Modals -->
        <div id="saas-notif-modal" class="saas-modal">
            <div class="saas-modal-content max-w-400">
                <span class="close-modal">&times;</span>
                <h3>Recent Activity</h3>
                <div id="notif-list" class="max-h-300 overflow-y-auto">
                    <div class="p-12 border-light-bottom">🚀 Welcome to your new dashboard!</div>
                    <?php
                    $recent = get_posts(['post_type' => 'saas_lead', 'author' => $user_id, 'numberposts' => 5]);
                    foreach($recent as $r) : ?>
                        <div class="p-12 border-light-bottom text-sm">
                            <strong>New Lead:</strong> <?php echo esc_html(get_post_meta($r->ID, '_saas_lead_name', true)); ?>
                            <br><small class="color-muted-alt"><?php echo get_the_date('', $r->ID); ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div id="saas-wizard-modal" class="saas-modal" style="<?php echo $show_wizard ? 'display:flex;' : ''; ?>">
            <div class="saas-modal-content max-w-600">
                <span class="close-modal">&times;</span>
                <div class="wizard-step active" data-step="1">
                    <h3>Welcome to the Elite Circle 🚀</h3>
                    <p>To optimize your Authority Engine, let's start with your professional niche.</p>
                    <select id="wizard-niche" class="field">
                        <option value="servant">🏛️ Public Servant / Official</option>
                        <option value="coach">🚀 Business Coach</option>
                        <option value="creator">🎬 Digital Creator</option>
                        <option value="realtor">🏡 Real Estate Pro</option>
                        <option value="business">🏢 Corporate Entity</option>
                        <option value="speaker">🎙️ Public Speaker</option>
                        <option value="author">✍️ Author / Writer</option>
                        <option value="consultant">🧠 Strategy Consultant</option>
                        <option value="lawyer">⚖️ Lawyer / Legal</option>
                        <option value="doctor">🩺 Doctor / Healthcare</option>
                        <option value="artist">🎨 Artist / Designer</option>
                        <option value="agency">🏢 Agency Owner</option>
                        <option value="freelancer">🎨 Creative Freelancer</option>
                        <option value="tiktok">📱 Influencer / TikTok</option>
                        <option value="luxury">⚜️ Luxury Advisory</option>
                    </select>
                    <button class="btn-primary next-step full-width">Next Step</button>
                </div>
                <div class="wizard-step" data-step="2">
                    <h3>Your Strategic Identity</h3>
                    <div class="field"><label>Professional Headline (The Hook)</label><input type="text" id="wizard-headline" placeholder="e.g. Scaling 7-Figure Brands with Elite Logic"></div>
                    <div class="field"><label>Short Biography (The Authority)</label><textarea id="wizard-bio" rows="3" placeholder="Briefly describe your transformation..."></textarea></div>
                    <div class="flex gap-10">
                        <button class="button prev-step flex-1">Back</button>
                        <button class="btn-primary next-step flex-2">Proceed</button>
                    </div>
                </div>
                <div class="wizard-step" data-step="3">
                    <h3>Strategic Alignment Complete!</h3>
                    <p>We are ready to generate your high-conversion assets. Your dashboard will be pre-configured with industry-standard blocks for your niche.</p>
                    <button id="wizard-finish" class="btn-primary full-width">Generate My Elite Profile</button>
                </div>
                <div class="wizard-progress"><div class="progress-bar-fill"></div></div>
            </div>
        </div>

        <!-- Modals -->
        <div id="saas-message-modal" class="saas-modal">
            <div class="saas-modal-content">
                <span class="close-modal">&times;</span>
                <h3 id="msg-modal-title">Message Details</h3>
                <div id="msg-modal-content" class="lh-1-6 mb-20"></div>
                <hr>
                <h4>Reply</h4>
                <form id="saas-reply-msg-form">
                    <input type="hidden" name="to_user" id="msg-reply-to">
                    <textarea name="message" rows="3" required placeholder="Type your reply here..."></textarea>
                    <button type="submit" class="btn-primary mt-10">Send Reply</button>
                </form>
            </div>
        </div>

        <div id="saas-new-msg-modal" class="saas-modal">
            <div class="saas-modal-content">
                <span class="close-modal">&times;</span>
                <h3>New Message to Admin</h3>
                <form id="saas-new-support-msg">
                    <div class="field"><label>Subject</label><input type="text" name="subject" required></div>
                    <div class="field"><label>Message</label><textarea name="message" rows="5" required></textarea></div>
                    <button type="submit" class="btn-primary full-width">Send Message</button>
                </form>
            </div>
        </div>

        <div id="saas-lead-modal" class="saas-modal">
            <div class="saas-modal-content">
                <span class="close-modal">&times;</span>
                <h3>Lead Details</h3>
                <div id="lead-details-content" class="lh-1-8"></div>
            </div>
        </div>

        <div id="saas-domain-modal" class="saas-modal">
            <div class="saas-modal-content max-w-700">
                <span class="close-modal">&times;</span>
                <div class="text-center mb-30">
                    <div class="text-5xl mb-10">🌐</div>
                    <h2 class="m-0">Custom Domain Setup Guide</h2>
                    <p class="color-muted">Transform your profile into a professional branded asset.</p>
                </div>

                <div class="domain-guide-steps grid-stack gap-25">
                    <div class="flex gap-20 flex-start">
                        <div class="w-40 h-40 bg-primary color-white radius-50p flex-center flex-shrink-0 font-bold">1</div>
                        <div>
                            <h4 class="m-0-0-5">Define Your Command Center</h4>
                            <p class="m-0 text-sm color-dark">Secure your professional real estate. Elite consultants typically use <code>connect.yourbrand.com</code> or <code>portal.yourbrand.com</code> to establish instant authority.</p>
                        </div>
                    </div>

                    <div class="flex gap-20 flex-start">
                        <div class="w-40 h-40 bg-primary color-white radius-50p flex-center flex-shrink-0 font-bold">2</div>
                        <div>
                            <h4 class="m-0-0-5">Deploy DNS Protocol (CNAME)</h4>
                            <p class="m-0 text-sm color-dark">Login to your registrar (Cloudflare, GoDaddy, etc.) and navigate to the **DNS Management** interface. You are mapping your subdomain to our high-performance edge network.</p>
                            <div class="bg-light p-15 radius-12 mt-10 border-light text-xs">
                                <div class="mb-10"><strong>Type:</strong> CNAME</div>
                                <div class="mb-10"><strong>Host/Name:</strong> <code>connect</code> (or your chosen subdomain)</div>
                                <div><strong>Points To:</strong> <code><?php echo parse_url(home_url(), PHP_URL_HOST); ?></code></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-20 flex-start">
                        <div class="w-40 h-40 bg-primary color-white radius-50p flex-center flex-shrink-0 font-bold">3</div>
                        <div>
                            <h4 class="m-0-0-5">Link Your Profile</h4>
                            <p class="m-0 text-sm color-dark">Once you've saved the DNS record, come back here and enter your full domain (e.g. <code>bio.yourdomain.com</code>) into the box below and click <strong>Update Profile</strong>.</p>
                        </div>
                    </div>

                    <div class="flex gap-20 flex-start">
                        <div class="w-40 h-40 bg-secondary color-white radius-50p flex-center flex-shrink-0 font-bold">✓</div>
                        <div>
                            <h4 class="m-0-0-5">Verification & SSL</h4>
                            <p class="m-0 text-sm color-dark">Our system will automatically detect the connection and provision a secure SSL certificate within 24-48 hours.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-40 p-20 bg-primary-soft radius-15 border-primary">
                    <p class="m-0 text-xs font-bold color-primary">💡 Pro Tip: Need a naked domain (yourdomain.com)?</p>
                    <p class="m-5-0-0 text-sm color-dark">Add an <strong>A Record</strong> pointing to our server IP: <code>(Contact Support for IP)</code> and set up a redirect from WWW to non-WWW.</p>
                </div>

                <button class="button full-width mt-30 bg-primary color-white border-none p-15" onclick="this.closest('.saas-modal').style.display='none'">Got it, thanks!</button>
            </div>
        </div>

        <?php
        return ob_get_clean();
    }
}
new Saas_Dashboard();
