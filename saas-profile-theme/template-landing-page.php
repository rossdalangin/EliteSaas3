<?php
/**
 * Template Name: Landing Page (Clean)
 */

get_header(); ?>

<?php
$h_title = get_option('saas_home_title') ?: get_the_title();
$h_hero  = get_option('saas_home_hero');
$h_cta   = get_option('saas_home_cta') ?: 'Get Started Free';
$h_img   = get_option('saas_home_image');
?>
<main id="landing-page" class="landing-main bg-color">
    <!-- Animated Gradient Background -->
    <div class="animated-mesh-bg">
        <div class="mesh-circle-1"></div>
    </div>

    <div class="sticky-buy" id="landing-sticky-cta">
        <p class="mb-0 font-bold color-dark">Join 12,000+ elite creators today.</p>
        <a href="<?php echo home_url('/register'); ?>" class="btn-sticky-cta">Get Started Free →</a>
    </div>

    <script>
        window.addEventListener('scroll', function() {
            var cta = document.getElementById('landing-sticky-cta');
            if (window.scrollY > 800) cta.style.display = 'flex';
            else cta.style.display = 'none';
        });
    </script>

    <div class="landing-content mx-auto">
        <div class="hero-grid-layout">
            <div class="hero-text-content">
                <div class="badge-ui mb-24">The Elite Standard 1%</div>
                <h1 class="landing-title">
                    <?php echo esc_html($h_title); ?>
                </h1>
                <?php if ($h_hero) : ?>
                    <p class="landing-hero-text"><?php echo esc_html($h_hero); ?></p>
                <?php endif; ?>

                <div class="hero-claim-wrapper mt-40">
                    <form action="<?php echo home_url('/register'); ?>" method="GET" class="hero-claim-form">
                        <span class="hero-claim-prefix"><?php echo parse_url(home_url(), PHP_URL_HOST); ?>/</span>
                        <input type="text" name="username" id="saas-home-username" placeholder="yourname" class="hero-claim-input">
                        <button type="submit" class="hero-claim-btn"><?php echo esc_html($h_cta); ?></button>
                    </form>
                    <div id="username-status" class="status-message font-bold mt-10"></div>
                    <p class="text-xs mt-15 color-lighter">⚡️ It takes less than 60 seconds to launch.</p>
                </div>

                <!-- Social Proof Logos -->
                <div class="trusted-by-section mt-60">
                    <p class="trusted-by-title">Trusted by innovators at</p>
                    <div class="trusted-logos-container">
                        <?php
                        $logos_json = get_option('saas_home_trusted_logos');
                        $logos = json_decode($logos_json, true) ?: [
                            'https://upload.wikimedia.org/wikipedia/commons/a/a9/Amazon_logo.svg',
                            'https://upload.wikimedia.org/wikipedia/commons/2/2f/Google_2015_logo.svg',
                            'https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg'
                        ];
                        foreach ($logos as $logo_url) : ?>
                            <img src="<?php echo esc_url($logo_url); ?>" class="global-logo-img max-h-24" alt="Trusted Logo">
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="hero-visual-content">
                <?php if ($h_img) : ?>
                    <div class="hero-image-perspective">
                        <img src="<?php echo esc_url($h_img); ?>" alt="SaaS Preview" class="radius-40 shadow-preview">
                    </div>
                <?php else : ?>
                    <div class="hero-image-perspective">
                        <div class="card-white flex gap-30 text-left relative overflow-visible">
                            <div class="absolute -top-30 -right-30 z-10">
                                <div class="bg-vibrant-gradient p-24 radius-20 shadow-xl color-white text-center">
                                    <div class="text-3xl font-black mb-5">4.8x</div>
                                    <div class="text-xs font-bold opacity-80">CONVERSION LIFT</div>
                                </div>
                            </div>
                            <div class="flex-1 bg-light radius-20 p-20">
                                <div class="mb-20 w-40 h-10 bg-grey-medium"></div>
                                <div class="bg-white mb-20 shadow-sm radius-12 full-width h-200"></div>
                                <div class="h-10 bg-grey-medium w-80p"></div>
                            </div>
                            <div class="flex-2">
                                <div class="mb-20 h-40 bg-primary radius-10 w-60p"></div>
                                <div class="mb-10 h-15 bg-grey-light radius-full"></div>
                                <div class="mb-10 h-15 bg-grey-light radius-full w-80p"></div>
                                <div class="grid-2 mt-40 gap-15">
                                    <div class="bg-light h-80 radius-15"></div>
                                    <div class="bg-light h-80 radius-15 shadow-accent"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="landing-body text-xl color-light">
            <?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
        </div>
    </div>
</main>

<!-- Benefits Section -->
<section class="section-padding bg-subtle">
    <div class="container-standard flex-wrap flex-center gap-60 mx-auto">
        <div class="flex-1 min-w-320">
            <h2 class="text-5xl mb-30">Stop losing traffic. Start building your list.</h2>
            <ul class="benefit-list">
                <?php
                $benefits = json_decode(get_option('saas_home_benefits'), true) ?: [
                    'One link to rule them all',
                    'Capture leads even while you sleep',
                    'Instant vCard exchange for networking',
                    'Beautiful, mobile-first design'
                ];
                foreach ($benefits as $b) : ?>
                    <li class="mb-15 flex-center gap-15">
                        <span class="color-primary font-black">✓</span> <?php echo esc_html($b); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="flex-1 min-w-320 iphone-preview-wrapper bg-dark-inner radius-60 shadow-xl-neg iphone-inner-border">
            <?php
            $latest_profile = get_posts(['post_type' => 'saas_profile', 'post_status' => 'publish', 'numberposts' => 1]);
            if ($latest_profile) : ?>
                <iframe src="<?php echo home_url('/' . $latest_profile[0]->post_name); ?>" class="iphone-inner-iframe"></iframe>
                <div class="iphone-inner-overlay" onclick="window.location.href='<?php echo home_url('/directory'); ?>'"></div>
            <?php else : ?>
                <div class="text-center color-white mb-30">
                    <div class="iphone-mockup-avatar-small"></div>
                    <h4 class="mb-0 text-2xl font-black">@yourname</h4>
                    <p class="text-sm opacity-60">Digital Architect & Creator</p>
                </div>
                <div class="flex-column gap-12">
                    <div class="iphone-mockup-btn-white">🚀 Work with Me</div>
                    <div class="iphone-mockup-btn-glass">🎬 Latest Masterclass</div>
                    <div class="iphone-mockup-btn-glass">📦 My Products</div>
                    <div class="iphone-mockup-lead-badge">
                        <p class="font-black text-xs color-vibrant mb-10 text-uppercase">New Lead Captured!</p>
                        <div class="iphone-mockup-progress-vibrant"></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Comparison Section -->
<section class="section-padding bg-color">
    <div class="container-standard text-center mx-auto">
        <h2 class="section-title-large">Why elite creators choose us</h2>
        <div class="comparison-table-wrapper">
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th class="p-32 text-xl">Feature</th>
                        <th class="p-32 text-xl color-lighter">Basic Link Hubs</th>
                        <th class="p-32 text-xl color-primary font-black">Elite SaaS Funnel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $comparison_json = get_option('saas_home_comparison_json');
                    $rows = json_decode($comparison_json, true) ?: [
                        ['label' => 'Lead Generation Forms', 'basic' => '✗ No', 'elite' => '✓ Integrated'],
                        ['label' => 'A/B Split Testing', 'basic' => '✗ No', 'elite' => '✓ Automated'],
                        ['label' => 'NFC Digital Business Card', 'basic' => '✗ No', 'elite' => '✓ Native Sync'],
                        ['label' => 'CRM & Email Integrations', 'basic' => 'Limited', 'elite' => '✓ Full Suite']
                    ];
                    foreach ($rows as $row) : ?>
                        <tr>
                            <td class="p-32 font-bold"><?php echo esc_html($row['label']); ?></td>
                            <td class="pricing-table-td-p32 status-basic"><?php echo esc_html($row['basic']); ?></td>
                            <td class="pricing-table-td-p32 color-vibrant font-bold"><?php echo esc_html($row['elite']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Growth Stats Section (The Undeniable Math) -->
<section class="section-padding bg-dark relative overflow-hidden">
    <div class="inset-0 full-width full-height opacity-30 bg-primary-overlay absolute bg-radial-gradient-primary"></div>
    <?php
    $count_profiles = wp_count_posts('saas_profile')->publish;
    $count_leads = wp_count_posts('saas_lead')->publish;
    global $wpdb;
    $total_rev = (float)$wpdb->get_var("SELECT SUM(meta_value) FROM $wpdb->postmeta WHERE meta_key = '_saas_order_amount'");

    $p_offset = (int) get_option('saas_home_profile_offset') ?: 1250;
    $l_offset = (int) get_option('saas_home_lead_offset') ?: 8500;
    $r_offset = (float) get_option('saas_home_rev_offset') ?: 42.5;
    ?>
    <div class="container-wide relative z-1 mx-auto">
        <h2 class="text-center text-4xl font-black mb-80 tracking-tight color-white">The Undeniable Math of Elite Growth</h2>
        <div class="grid-3 text-center">
            <div class="stat-card-glass">
                <div class="stat-value-large text-gradient-primary"><?php echo number_format($count_profiles + $p_offset); ?>+</div>
                <p class="stat-label-elite">Elite Profiles Launched</p>
                <p class="stat-desc-muted">Authority established globally.</p>
            </div>
            <div class="stat-card-glass translate-y-neg-20">
                <div class="stat-value-large text-gradient-accent">$<?php echo number_format(($total_rev / 1000000) + $r_offset, 1); ?>M+</div>
                <p class="stat-label-elite">Revenue Processed</p>
                <p class="stat-desc-muted">By our users, through our system.</p>
            </div>
            <div class="stat-card-glass">
                <div class="stat-value-large text-gradient-orange"><?php echo number_format($count_leads + $l_offset); ?>+</div>
                <p class="stat-label-elite">Leads Captured</p>
                <p class="stat-desc-muted">High-intent inquiries delivered.</p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="section-padding bg-subtle">
    <div class="container-wide text-center mx-auto">
        <h2 class="section-title-large">Your elite presence in 3 simple steps</h2>
        <div class="grid-3">
            <?php
            $how_it_works_json = get_option('saas_home_how_it_works_json');
            $steps = json_decode($how_it_works_json, true) ?: [
                ['title' => 'Claim Your Link', 'desc' => 'Register your unique URL and customize your digital identity.'],
                ['title' => 'Build Your Funnel', 'desc' => 'Drag and drop links, forms, and galleries to showcase your best work.'],
                ['title' => 'Launch & Grow', 'desc' => 'Share your link everywhere and watch your conversion rates skyrocket.']
            ];
            $step_num = 1;
            foreach ($steps as $s) : ?>
                <div class="card-light">
                    <div class="step-number"><?php echo $step_num++; ?></div>
                    <h3 class="text-2xl mb-15"><?php echo esc_html($s['title']); ?></h3>
                    <p><?php echo esc_html($s['desc']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features Grid -->
<section class="section-padding-large bg-light border-t">
    <div class="container-wide text-center mx-auto">
        <h2 class="section-title-large">Everything you need to grow online</h2>

        <!-- Interactive Tech Preview -->
        <div class="grid-2 mb-80 text-left flex-center">
            <div class="card-white p-40 shadow-xl">
                <div class="flex-wrap gap-10 mb-20">
                    <span class="badge-ui">Smart Routing</span>
                    <span class="badge-ui badge-vibrant">A/B Testing</span>
                </div>
                <h3 class="text-4xl mb-20">The only link hub with an IQ.</h3>
                <p class="color-light text-lg lh-1-6">Our system automatically detects your visitor's location and device. Send iPhone users to the App Store and Android users to Play Store—automatically. Run split tests on your CTAs to see which version converts better.</p>
            </div>
            <div class="bg-dark p-40 radius-40 relative overflow-hidden">
                <div class="mb-15 p-20 bg-glass-card">
                    <div class="flex-between mb-10">
                        <strong class="font-bold color-white">Variant A: "Buy Now"</strong>
                        <span class="color-red">12.5%</span>
                    </div>
                    <div class="progress-bar-container"><div class="progress-bar-fill-red h-6 radius-100 progress-fill-12"></div></div>
                </div>
                <div class="p-20 bg-glass-card">
                    <div class="flex-between mb-10">
                        <strong class="font-bold color-white">Variant B: "Get Started"</strong>
                        <span class="color-vibrant">24.8% (Winner)</span>
                    </div>
                    <div class="progress-bar-container"><div class="progress-bar-fill-vibrant h-6 radius-100 progress-fill-24"></div></div>
                </div>
            </div>
        </div>

        <div class="grid-3">
            <?php
            $features_json = get_option('saas_home_features');
            $features = json_decode($features_json, true);

            if (!$features) {
                $features = [
                    ['icon' => '🚀', 'title' => 'Fast Setup', 'desc' => 'Launch your profile in under 60 seconds.'],
                    ['icon' => '📊', 'title' => 'Smart Analytics', 'desc' => 'Track every click and view with high-performance tracking.'],
                    ['icon' => '🎯', 'title' => 'Lead Capture', 'desc' => 'Convert traffic into real customers with built-in forms.']
                ];
            }

            foreach ($features as $f) : ?>
                <div class="card-white text-left hover-lift">
                    <div class="text-5xl mb-20"><?php echo esc_html($f['icon']); ?></div>
                    <h3 class="text-2xl mb-15"><?php echo esc_html($f['title']); ?></h3>
                    <p><?php echo esc_html($f['desc']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Theme Showcase Section -->
<section class="section-padding bg-subtle">
    <div class="container-wide text-center mx-auto">
        <h2 class="section-title-large">Bespoke themes for elite brands</h2>
        <div class="grid-4">
            <div class="p-32 bg-light radius-24 border-light">
                <div class="bg-white mb-20 flex-center font-black color-light shadow-sm radius-12 h-200">Light Mode</div>
                <h4 class="mb-0">Clean & Professional</h4>
            </div>
            <div class="p-32 bg-dark radius-24 border-slate-800">
                <div class="mb-20 flex-center font-black color-lighter shadow-lg radius-12 h-200 bg-dark-preview">Dark Mode</div>
                <h4 class="mb-0">Modern & Bold</h4>
            </div>
            <div class="p-32 color-white radius-24 border-glass bg-primary-gradient">
                <div class="mb-20 flex-center font-black color-white shadow-md radius-12 h-200 bg-vibrant-preview">Vibrant</div>
                <h4 class="mb-0">Energetic & Fun</h4>
            </div>
            <div class="p-32 radius-24 bg-dark border-gold color-gold">
                <div class="mb-20 flex-center font-black shadow-md radius-12 h-200 bg-luxury-preview color-gold">Luxury</div>
                <h4 class="mb-0">Premium & Elite</h4>
            </div>
        </div>
    </div>
</section>

<!-- Featured Profiles Section -->
<section class="section-padding bg-subtle">
    <div class="container-wide text-center mx-auto">
        <h2 class="text-4xl mb-20">Join thousands of elite professionals</h2>
        <p class="color-light text-xl mb-60">See how others are using our platform to scale their digital identity.</p>

        <div class="grid-3">
            <?php
            $featured_slugs = get_option('saas_featured_profiles') ?: [];
            $featured_profiles = [];

            if ($featured_slugs) {
                $featured_profiles = get_posts([
                    'post_type' => 'saas_profile',
                    'post_name__in' => $featured_slugs,
                    'post_status' => 'publish',
                    'orderby' => 'post_name__in'
                ]);
            }

            if (empty($featured_profiles)) {
                $featured_profiles = get_posts([
                    'post_type' => 'saas_profile',
                    'post_status' => 'publish',
                    'meta_key' => '_saas_show_in_directory',
                    'meta_value' => '1',
                    'numberposts' => 3,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ]);
            }

            if ($featured_profiles) :
                foreach ($featured_profiles as $fp) :
                    $fp_meta = saas_get_profile_meta($fp->ID);
                    $fp_url = home_url('/' . $fp->post_name);
                    ?>
                    <div class="card-white text-center hover-lift border-light">
                        <div class="mx-auto mb-20 radius-full border-light avatar-fixed-100 overflow-hidden avatar-border-primary avatar-shadow-md">
                            <?php if (has_post_thumbnail($fp->ID)) : ?>
                                <?php echo get_the_post_thumbnail($fp->ID, 'thumbnail', ['class' => 'full-size-cover']); ?>
                            <?php else : ?>
                                <div class="bg-light flex-center text-4xl full-width full-height">👤</div>
                            <?php endif; ?>
                        </div>
                        <h3 class="mb-8"><?php echo esc_html($fp->post_title); ?></h3>
                        <p class="color-primary font-bold text-sm mb-15"><?php echo esc_html($fp_meta['headline']); ?></p>
                        <a href="<?php echo esc_url($fp_url); ?>" target="_blank" class="saas-link-btn text-xs font-bold btn-profile-view btn-profile-view-light">View Profile</a>
                    </div>
                <?php endforeach;
            else: ?>
                <div class="grid-column-all p-40 bg-light color-lighter radius-24">
                    Create the first profile to be featured here!
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="section-padding bg-subtle">
    <div class="container-wide text-center mx-auto">
        <h2 class="section-title-large"><?php echo get_option('saas_home_testimonials_title') ?: 'What elite creators are saying'; ?></h2>
        <div class="grid-3">
            <?php
            $t_json = get_option('saas_home_testimonials');
            $testimonials = json_decode($t_json, true);

            if (!$testimonials) {
                $testimonials = [
                    ['name' => 'Alex Rivera', 'role' => 'Strategic Coach', 'text' => 'I switched from Linktree and my consultation bookings increased by 40% in the first month.'],
                    ['name' => 'Jordan Smith', 'role' => 'Real Estate Mogul', 'text' => 'The NFC business card feature is the ultimate conversation starter at networking events.'],
                    ['name' => 'Elena Chen', 'role' => 'Digital Artist', 'text' => 'Finally, a link hub that actually looks high-end. The analytics helped me double my revenue.']
                ];
            }

            foreach ($testimonials as $t) : ?>
                <div class="testimonial-block bg-light p-50 radius-40 shadow-sm border-light">
                    <div class="mb-20 color-vibrant text-2xl star-rating">★★★★★</div>
                    <p class="quote text-xl color-light lh-1-7 mb-30">"<?php echo esc_html($t['text']); ?>"</p>
                    <div class="flex-center gap-15">
                        <div class="testimonial-avatar-placeholder flex-center font-black color-lighter radius-full avatar-fixed-80"><?php echo substr($t['name'], 0, 1); ?></div>
                        <div class="text-left">
                            <strong class="text-lg color-dark display-block"><?php echo esc_html($t['name']); ?></strong>
                            <small class="color-light font-bold"><?php echo esc_html($t['role']); ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="section-padding-large bg-light border-t">
    <div class="container-standard text-center mx-auto">
        <h2 class="section-title-large">Invest in Your Authority</h2>
        <p class="color-light text-xl mb-80 mx-auto max-w-600">Unlock the tools used by the world's most successful consultants. Risk-free. Cancel anytime.</p>

        <div class="grid-3 align-stretch">
            <?php
            $pricing_json = get_option('saas_home_pricing_json');
            $plans = json_decode($pricing_json, true) ?: [
                [
                    'name' => 'Free', 'price' => '$0', 'period' => 'forever', 'cta' => 'Join for Free', 'link' => '/register', 'style' => 'light',
                    'features' => ['1 Profile', 'Standard Blocks', 'Basic Analytics', 'Community Support']
                ],
                [
                    'name' => 'Elite Pro', 'price' => '$19', 'period' => '/mo', 'cta' => 'Upgrade to Pro', 'link' => '/register?plan=pro', 'style' => 'featured', 'badge' => 'FOR THE ELITE 1%',
                    'features' => ['Unlimited Premium Blocks', 'Lead Generation CRM', 'Custom Domain Mapping', 'Whitelabel Branding', 'Priority Support']
                ],
                [
                    'name' => 'Agency Unlimited', 'price' => '$49', 'period' => '/mo', 'cta' => 'Go Unlimited', 'link' => '/register?plan=agency', 'style' => 'dark',
                    'features' => ['Everything in Pro', 'Unlimited Sub-accounts', 'API & Webhook Access', 'White-label Client Funnels', 'Dedicated Account Manager']
                ]
            ];
            foreach ($plans as $p) :
                $style = $p['style'] ?? 'light';
                $is_featured = ($style === 'featured');
                $is_dark = ($style === 'dark');

                $card_class = 'card-white';
                if ($is_featured) $card_class = 'pricing-plan-card is-featured';
                elseif ($is_dark) $card_class = 'pricing-plan-card is-dark';
                else $card_class = 'pricing-plan-card is-light';

                $badge_bg_class = $is_dark ? 'badge-dark-gold' : 'badge-vibrant-green';
                $check_color_class = ($is_featured || $is_dark) ? 'check-white' : 'check-green';
                $cta_style_class = '';
                if ($is_featured) $cta_style_class = 'cta-pro-featured';
                elseif ($is_dark) $cta_style_class = 'cta-pro-dark-vibrant';
                else $cta_style_class = 'cta-standard-transparent';
            ?>
                <div class="<?php echo $card_class; ?> relative">
                    <?php if (isset($p['badge'])) : ?>
                        <div class="demo-card-badge-static demo-card-badge-pos <?php echo $badge_bg_class; ?>"><?php echo esc_html($p['badge']); ?></div>
                    <?php endif; ?>

                    <h3 class="mb-0 text-2xl font-bold"><?php echo esc_html($p['name']); ?></h3>
                    <div class="text-5xl font-black mt-24 mb-24 lh-1-6"><?php echo esc_html($p['price']); ?><small class="text-lg opacity-70 font-bold"><?php echo esc_html($p['period']); ?></small></div>

                    <ul class="benefit-list mb-40 text-left flex-1 font-0-95">
                        <?php foreach ($p['features'] as $f) : ?>
                            <li class="mb-12 flex-center gap-12">
                                <span class="font-black <?php echo $check_color_class; ?>">✓</span>
                                <span class="opacity-90"><?php echo esc_html($f); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <a href="<?php echo home_url($p['link']); ?>" class="saas-link-btn font-black text-base btn-pricing-cta btn-p18 <?php echo $cta_style_class; ?>">
                        <?php echo esc_html($p['cta']); ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-60 flex-center gap-40 flex-wrap opacity-60">
            <div class="flex-center gap-10"><span class="text-2xl">🔒</span> 256-bit Secure SSL</div>
            <div class="flex-center gap-10"><span class="text-2xl">💳</span> Cancel Anytime</div>
            <div class="flex-center gap-10"><span class="text-2xl">⚡</span> Instant Activation</div>
        </div>
    </div>
</section>

<!-- Founder's Letter Section -->
<section class="section-padding bg-white">
    <div class="container-narrow bg-light p-40 radius-40-important mx-auto border-light">
        <div class="flex gap-30 mb-30 flex-center">
            <?php
            $founder_img = get_option('saas_home_founder_image');
            if ($founder_img) : ?>
                <img src="<?php echo esc_url($founder_img); ?>" class="mx-auto shadow-md radius-full full-size-cover avatar-fixed-80 border-white-4">
            <?php else : ?>
                <div class="mx-auto shadow-md bg-primary radius-full avatar-fixed-80 border-white-4"></div>
            <?php endif; ?>
            <div class="text-left">
                <h3 class="mb-0 text-2xl font-bold">A Message from the Founder</h3>
                <p class="mb-0 color-light">Consultant & Digital Architect</p>
            </div>
        </div>
        <p class="text-left font-italic text-xl color-light lh-1-6">
            "<?php echo get_option('saas_home_founder_letter') ?: 'I built this because I saw so many hard-working coaches losing leads to standard link trees. You deserve a system that converts your hard work into results.'; ?>"
        </p>
        <p class="text-left font-bold color-primary mt-20">— Let’s help more people, together.</p>
    </div>
</section>

<!-- Final CTA Section -->
<section class="section-padding-large bg-dark text-center color-white relative overflow-hidden footer-cta-section">
    <div class="container-narrow mx-auto relative z-1">
        <h2 class="text-5xl font-black mb-20 lh-1-6">Ready to scale your digital presence?</h2>
        <p class="text-2xl opacity-80 mb-40">Join thousands of elite creators who are building their future on our platform.</p>
        <a href="<?php echo home_url('/register'); ?>" class="font-black text-2xl shadow-xl btn-elite-launch btn-register-footer">Get Started for Free</a>
        <p class="mt-20 text-sm opacity-70">No credit card required. Cancel anytime.</p>
    </div>
</section>

<!-- FAQ Section -->
<section class="section-padding bg-light">
    <div class="container-narrow mx-auto">
        <h2 class="text-center text-4xl mb-60">Common Questions</h2>
        <?php
        $f_json = get_option('saas_home_faq');
        $faqs = json_decode($f_json, true) ?: [
            ['q' => 'Is it free?', 'a' => 'Yes, we have a generous free tier for everyone.'],
            ['q' => 'Can I use my own domain?', 'a' => 'Absolutely! Custom domain support is available on Pro plans.']
        ];
        foreach ($faqs as $f) : ?>
            <details class="faq-block bg-white mb-15 shadow-sm radius-12">
                <summary class="p-20 font-bold cursor-pointer"><?php echo esc_html($f['q']); ?></summary>
                <p class="p-20 color-light"><?php echo esc_html($f['a']); ?></p>
            </details>
        <?php endforeach; ?>
    </div>
</section>

<script>
jQuery(document).ready(function($) {
    var timer;
    $('#saas-home-username').on('keyup', function() {
        var user = $(this).val();
        var $status = $('#username-status');
        clearTimeout(timer);

        if (user.length < 3) {
            $status.text('').css('color', 'inherit');
            return;
        }

        $status.text('Checking availability...').css('color', '#666');

        timer = setTimeout(function() {
            $.post(saas_data.ajax_url, {
                action: 'saas_check_username',
                username: user
            }, function(res) {
                if (res.success) {
                    $status.text('✓ ' + user + ' is available!').css('color', '#10b981');
                } else {
                    $status.text('✗ ' + user + ' is already taken.').css('color', '#ef4444');
                }
            });
        }, 500);
    });
});
</script>

<?php get_footer(); ?>
