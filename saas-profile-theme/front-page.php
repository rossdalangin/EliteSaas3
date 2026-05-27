<?php
/**
 * The front page template file.
 */

get_header(); ?>

<?php
$h_title = get_option('saas_home_title') ?: 'Launch your digital identity in 60 seconds.';
$h_hero  = get_option('saas_home_hero') ?: 'Combine your link-in-bio, business card, and lead magnets into one high-performance page.';
$h_cta   = get_option('saas_home_cta') ?: 'Get Started Free';
$h_img   = get_option('saas_home_image');
?>

<main id="front-page" class="landing-main">
    <!-- Animated Mesh Background -->
    <div class="animated-mesh-bg">
        <div class="mesh-circle-1"></div>
        <div class="mesh-circle-2"></div>
    </div>

    <div class="landing-content mx-auto">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); the_content(); endwhile; endif; ?>

        <div class="hero-grid-layout">
            <div class="hero-text-content">
                <div class="badge-ui mb-24">The Elite Standard 1%</div>
                <h1 class="landing-title">
                    <?php echo esc_html($h_title); ?>
                </h1>
                <p class="landing-hero-text">
                    <?php echo esc_html($h_hero); ?>
                </p>

                <div class="cta-actions">
                    <div class="hero-claim-wrapper">
                        <form action="<?php echo home_url('/register'); ?>" method="GET" class="hero-claim-form">
                            <span class="hero-claim-prefix"><?php echo parse_url(home_url(), PHP_URL_HOST); ?>/</span>
                            <input type="text" name="username" id="saas-home-username" placeholder="yourname" class="hero-claim-input">
                            <button type="submit" class="hero-claim-btn"><?php echo esc_html($h_cta); ?></button>
                        </form>
                        <div id="username-status" class="status-message color-primary"></div>
                        <p class="hero-claim-subtext">No credit card required. Setup in minutes.</p>
                    </div>
                </div>
            </div>

            <div class="hero-visual-content">
                <?php if ($h_img) : ?>
                    <div class="hero-image-perspective">
                        <img src="<?php echo esc_url($h_img); ?>" alt="Product Preview" class="radius-40 shadow-preview" loading="lazy">
                    </div>
                <?php else : ?>
                    <!-- Default Dashboard Preview Mockup -->
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

        <!-- Social Proof Logos -->
        <div class="trusted-by-section">
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
                    <img src="<?php echo esc_url($logo_url); ?>" class="trusted-logo" alt="Partner Logo" loading="lazy">
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

<!-- Unified Conversion Sections -->
<?php include __DIR__ . '/template-parts/content-hero.php'; ?>

<!-- Growth Stats Section -->
<section class="stats-section">
    <?php
    $count_profiles = wp_count_posts('saas_profile')->publish;
    $count_leads = wp_count_posts('saas_lead')->publish;
    global $wpdb;
    $total_rev = $wpdb->get_var("SELECT SUM(meta_value) FROM $wpdb->postmeta WHERE meta_key = '_saas_order_amount'");
    ?>
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-value color-primary"><?php echo number_format($count_profiles + 1250); ?>+</div>
            <p class="stat-label">Elite Profiles</p>
        </div>
        <div class="stat-item">
            <div class="stat-value color-vibrant">$<?php echo number_format(($total_rev / 1000) + 42.5, 1); ?>M+</div>
            <p class="stat-label">Revenue Tracked</p>
        </div>
        <div class="stat-item">
            <div class="stat-value color-vibrant"><?php echo number_format($count_leads + 8500); ?>+</div>
            <p class="stat-label">Leads Captured</p>
        </div>
    </div>
</section>

<!-- Tech Preview Section -->
<section class="feature-highlight-section">
    <div class="container-wide text-center">
        <h2 class="section-title-large">The only link hub with an <span class="text-gradient-primary">IQ</span>.</h2>

        <div class="feature-grid-3 mb-80 text-left">
            <div class="feature-card-light hover-lift">
                <div class="flex gap-12 mb-24">
                    <span class="badge-ui">Smart Routing</span>
                    <span class="badge-ui badge-vibrant">A/B Testing</span>
                </div>
                <h3 class="mb-24 text-4xl">Automate your growth.</h3>
                <p class="landing-hero-text mb-32 text-xl">Our engine detects visitor intent, device, and location in real-time. Serve optimized content to every user automatically. Run split tests on your CTAs to identify your highest-converting offers with mathematical precision.</p>
                <div class="benefit-tag">
                    <span class="text-2xl">📊</span>
                    <span>Real-time optimization engine active.</span>
                </div>
            </div>
            <div class="feature-card-dark shadow-xl">
                <div class="mb-20 p-24 bg-glass-light radius-16">
                    <div class="flex-between mb-12">
                        <strong class="text-sm">Variant A: "Book Now"</strong>
                        <span class="font-black color-red">14.2%</span>
                    </div>
                    <div class="progress-bar-container"><div class="progress-bar-fill-red progress-fill-14"></div></div>
                </div>
                <div class="p-24 bg-glass-vibrant radius-16">
                    <div class="flex-between mb-12">
                        <strong class="text-sm">Variant B: "Claim My Session"</strong>
                        <span class="font-black color-vibrant">32.5% (Winner)</span>
                    </div>
                    <div class="progress-bar-container"><div class="progress-bar-fill-vibrant progress-fill-32"></div></div>
                </div>
                <p class="mt-24 text-xs opacity-60 text-center text-uppercase font-bold ls-1">Real-time A/B Testing Data</p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="section-padding bg-subtle">
    <div class="container-wide text-center">
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
                <div class="card-light radius-xl">
                    <div class="step-number"><?php echo $step_num++; ?></div>
                    <h3 class="text-2xl mb-15"><?php echo esc_html($s['title']); ?></h3>
                    <p><?php echo esc_html($s['desc']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Theme Showcase Section -->
<section class="section-padding bg-color">
    <div class="container-wide text-center">
        <h2 class="section-title-large">Bespoke themes for elite brands</h2>
        <div class="grid-4">
            <div class="theme-showcase-card is-light">
                <div class="theme-preview-box">Light Mode</div>
                <h4 class="mb-0">Clean & Professional</h4>
            </div>
            <div class="theme-showcase-card is-dark">
                <div class="theme-preview-box">Dark Mode</div>
                <h4 class="mb-0">Modern & Bold</h4>
            </div>
            <div class="theme-showcase-card is-vibrant">
                <div class="theme-preview-box">Vibrant</div>
                <h4 class="mb-0">Energetic & Fun</h4>
            </div>
            <div class="theme-showcase-card is-luxury">
                <div class="theme-preview-box">Luxury</div>
                <h4 class="mb-0">Premium & Elite</h4>
            </div>
        </div>
    </div>
</section>

<!-- Featured Profiles Section -->
<section class="section-padding bg-subtle">
    <div class="container-wide text-center">
        <h2 class="text-4xl mb-20">Join thousands of elite professionals</h2>
        <p class="color-light text-xl mb-60">See how others are using our platform to scale their digital identity.</p>

        <div class="grid-3">
            <?php
            $featured_profiles = get_posts([
                'post_type' => 'saas_profile',
                'post_status' => 'publish',
                'meta_key' => '_saas_show_in_directory',
                'meta_value' => '1',
                'numberposts' => 3,
                'orderby' => 'date',
                'order' => 'DESC'
            ]);

            if ($featured_profiles) :
                foreach ($featured_profiles as $fp) :
                    $fp_meta = saas_get_profile_meta($fp->ID);
                    $fp_url = home_url('/' . $fp->post_name);
                    ?>
                    <div class="card-white text-center hover-lift border-light">
                        <div class="mx-auto mb-20 radius-full border-light avatar-fixed-100 overflow-hidden">
                            <?php if (has_post_thumbnail($fp->ID)) : ?>
                                <?php echo get_the_post_thumbnail($fp->ID, 'thumbnail', ['class' => 'full-size-cover']); ?>
                            <?php else : ?>
                                <div class="bg-grey-light flex-center text-4xl full-width full-height">👤</div>
                            <?php endif; ?>
                        </div>
                        <h3 class="mb-5"><?php echo esc_html($fp->post_title); ?></h3>
                        <p class="color-primary font-bold text-sm mb-15"><?php echo esc_html($fp_meta['headline']); ?></p>
                        <a href="<?php echo esc_url($fp_url); ?>" target="_blank" class="saas-link-btn text-xs font-bold btn-profile-view">View Profile</a>
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

<!-- Comparison Section -->
<?php
$comparison_json = get_option('saas_home_comparison_json');
if ($comparison_json) : ?>
<section class="section-padding bg-color">
    <div class="container-standard text-center mx-auto">
        <h2 class="section-title-large">Why elite creators choose us</h2>
        <div class="comparison-table-wrapper">
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th class="p-30 text-xl">Feature</th>
                        <th class="p-30 text-xl color-lighter">Basic Link Hubs</th>
                        <th class="p-30 text-xl color-primary font-black">Elite SaaS Funnel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rows = json_decode($comparison_json, true);
                    foreach ($rows as $row) : ?>
                        <tr>
                            <td class="p-25 font-bold"><?php echo esc_html($row['label']); ?></td>
                            <td class="p-25 status-basic"><?php echo esc_html($row['basic']); ?></td>
                            <td class="p-25 color-vibrant font-bold"><?php echo esc_html($row['elite']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Testimonials Section -->
<section class="section-padding bg-light">
    <div class="container-wide text-center mx-auto">
        <h2 class="section-title-large"><?php echo get_option('saas_home_testimonials_title') ?: 'What elite creators are saying'; ?></h2>
        <div class="grid-3">
            <?php
            $t_json = get_option('saas_home_testimonials');
            $testimonials = json_decode($t_json, true) ?: [];
            foreach ($testimonials as $t) : ?>
                <div class="testimonial-block bg-white p-50 radius-40 border-light shadow-sm">
                    <div class="mb-20 text-2xl color-vibrant star-rating">★★★★★</div>
                    <p class="quote text-xl color-light lh-1-7 mb-30">"<?php echo esc_html($t['text']); ?>"</p>
                    <div class="flex gap-15 flex-center">
                        <div class="flex-center font-black color-lighter radius-full bg-grey-light testimonial-avatar-placeholder avatar-fixed-80"></div>
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
<section class="pricing-section bg-subtle section-padding">
    <div class="container-standard text-center mx-auto">
        <h2 class="text-4xl mb-60"><?php echo get_option('saas_pricing_title') ?: 'Simple, Transparent Pricing'; ?></h2>
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
                    'features' => ['Everything in Free', 'Unlimited Premium Blocks', 'Lead Generation CRM', 'Custom Domain Mapping', 'Priority Support']
                ],
                [
                    'name' => 'Agency Unlimited', 'price' => '$49', 'period' => '/mo', 'cta' => 'Go Unlimited', 'link' => '/register?plan=agency', 'style' => 'light',
                    'features' => ['Everything in Pro', 'Unlimited Sub-accounts', 'API Access', 'White-label Client Funnels', 'Dedicated Manager']
                ]
            ];
            foreach ($plans as $p) :
                $is_featured = (isset($p['style']) && $p['style'] === 'featured');
                $cta_text = $p['cta'] ?? 'Get Started';
                $cta_link = $p['link'] ?? '/register';
            ?>
                <div class="pricing-plan-card <?php echo $is_featured ? 'is-featured' : 'is-light'; ?> relative flex-column">
                    <?php if (!empty($p['badge'])) : ?>
                        <div class="badge-pro-price demo-card-badge-static"><?php echo esc_html($p['badge']); ?></div>
                    <?php endif; ?>
                    <h3 class="mb-0"><?php echo esc_html($p['name']); ?></h3>
                    <div class="text-5xl font-black mt-20 mb-20"><?php echo esc_html($p['price']); ?><small class="text-base opacity-70"><?php echo esc_html($p['period']); ?></small></div>
                    <ul class="benefit-list mb-40 text-left flex-1 pricing-features-list">
                        <?php foreach ($p['features'] as $f) : ?>
                            <li class="mb-10">✓ <?php echo esc_html($f); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?php echo home_url($cta_link); ?>" class="saas-link-btn font-bold btn-pricing-cta <?php echo $is_featured ? 'style-featured' : ''; ?>">
                        <?php echo esc_html($cta_text); ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Founder's Letter Section -->
<section class="section-padding bg-light">
    <div class="container-narrow card-white text-center mx-auto">
        <div class="flex gap-30 mb-30 flex-center">
            <?php
            $founder_img = get_option('saas_home_founder_image');
            if ($founder_img) : ?>
                <img src="<?php echo esc_url($founder_img); ?>" class="mx-auto shadow-md radius-full full-size-cover founder-avatar avatar-fixed-80 border-white-4" loading="lazy">
            <?php else : ?>
                <div class="mx-auto shadow-md bg-primary radius-full founder-avatar-placeholder avatar-fixed-80 border-white-4"></div>
            <?php endif; ?>
            <div class="text-left">
                <h3 class="mb-0 text-2xl">A Message from the Founder</h3>
                <p class="mb-0 color-light">Consultant & Digital Architect</p>
            </div>
        </div>
        <p class="text-left font-italic text-xl color-light lh-1-8">
            "<?php echo get_option('saas_home_founder_letter') ?: 'I built this because I saw so many hard-working coaches losing leads to standard link trees. You deserve a system that converts your hard work into results.'; ?>"
        </p>
        <p class="text-left font-bold color-primary mt-20">— Let’s help more people, together.</p>
    </div>
</section>

<!-- Final CTA Section -->
<section class="footer-cta-section section-padding-large bg-dark text-center relative overflow-hidden">
    <!-- Animated Glows -->
    <div class="inset-0 full-width full-height opacity-30 pointer-events-none absolute-glow-layer">
        <div class="drift-glow-1"></div>
        <div class="drift-glow-2"></div>
    </div>
    <style> @keyframes drift { from { transform: translate(0,0); } to { transform: translate(10%, 10%); } } </style>

    <div class="container-standard relative z-1 mx-auto">
        <h2 class="font-black mb-25 color-white text-6xl-responsive ls-neg-3 lh-1">
            Scale your <span class="text-gradient-gold">Authority</span>.<br>
            Own your <span class="text-gradient-vibrant">Future</span>.
        </h2>
        <p class="mx-auto mb-60 color-lighter lh-1-4 hero-footer-desc">
            The world's most elite creators are switching to our funnel-first bio engine. Are you ready to convert more traffic?
        </p>

        <div class="flex-column flex-center gap-30">
            <a href="<?php echo home_url('/register'); ?>" class="font-black text-2xl shadow-gold shadow-gold-hover btn-elite-launch">
                Get Your Elite Link Now
            </a>

            <div class="flex-center gap-40 font-bold color-light text-xs text-uppercase ls-2">
                <span>✓ No Coding</span>
                <span>✓ No Credit Card</span>
                <span>✓ Instant Setup</span>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="section-padding bg-subtle">
    <div class="container-narrow mx-auto">
        <h2 class="text-center text-4xl mb-60">Common Questions</h2>
        <?php
        $f_json = get_option('saas_home_faq');
        $faqs = json_decode($f_json, true) ?: [];
        foreach ($faqs as $f) : ?>
            <details class="faq-block mb-15 bg-light radius-16">
                <summary class="p-20 font-bold cursor-pointer"><?php echo esc_html($f['q']); ?></summary>
                <p class="p-20 color-light mb-0"><?php echo esc_html($f['a']); ?></p>
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
            $.post('<?php echo admin_url("admin-ajax.php"); ?>', {
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
