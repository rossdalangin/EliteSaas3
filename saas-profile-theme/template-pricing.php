<?php
/**
 * Template Name: Pricing Plans
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$pricing_json = get_option('saas_home_pricing_json');
$plans = json_decode($pricing_json, true);

if (!$plans) {
    $plans = [
        [
            'name' => 'Foundational', 'price' => '$0', 'period' => 'forever', 'cta' => 'Claim Your Identity', 'link' => '/register', 'style' => 'light',
            'features' => ['1 Authority Engine', 'Standard Blocks', 'Basic Analytics', 'Community Support']
        ],
        [
            'name' => 'Elite Command', 'price' => '$19', 'period' => '/mo', 'cta' => 'Invest in Authority', 'link' => '/register?plan=pro', 'style' => 'featured', 'badge' => 'FOR THE ELITE 1%',
            'features' => ['Everything in Foundational', 'Unlimited Premium Blocks', 'Lead Generation CRM', 'Custom Domain Mapping', 'Priority Support']
        ],
        [
            'name' => 'Empire Scale', 'price' => '$49', 'period' => '/mo', 'cta' => 'Scale Your Empire', 'link' => '/register?plan=agency', 'style' => 'light',
            'features' => ['Everything in Elite Command', 'Unlimited Sub-accounts', 'API Access', 'White-label Client Funnels', 'Dedicated Manager']
        ]
    ];
}
?>

<main id="pricing-page" class="site-main bg-color min-h-500">
    <section class="landing-content pt-100">
        <header class="pricing-section-header">
            <h1 class="landing-title">Invest in Your Growth</h1>
            <p class="landing-hero-text">Simple, transparent pricing for creators at every stage.</p>
        </header>

        <div class="stats-grid">
            <?php foreach ($plans as $p) :
                $is_featured = (isset($p['style']) && $p['style'] === 'featured');
                $is_empire = (strpos(strtolower($p['name']), 'empire') !== false);
                $check_color_class = $is_featured ? 'check-white' : ($is_empire ? 'check-gold' : 'check-green');
            ?>
                <div class="pricing-plan-card <?php echo $is_featured ? 'is-featured' : 'is-light'; ?> flex-column">
                    <?php if (isset($p['badge']) && !empty($p['badge'])) : ?>
                        <div class="pricing-badge-vibrant"><?php echo esc_html($p['badge']); ?></div>
                    <?php endif; ?>
                    <h2 class="text-2xl color-dark"><?php echo esc_html($p['name']); ?></h2>
                    <div class="pricing-price-box">
                        <?php echo esc_html($p['price']); ?><small class="text-lg opacity-60 font-500"><?php echo esc_html($p['period']); ?></small>
                    </div>
                    <ul class="benefit-list mb-32 text-left flex-1">
                        <?php foreach ($p['features'] as $f) : ?>
                            <li class="mb-12 flex gap-12 flex-center">
                                <span class="font-black <?php echo $check_color_class; ?>">✓</span>
                                <span class="opacity-90"><?php echo esc_html($f); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php
                    $btn_class = 'cta-foundational-outline';
                    if ($is_featured) $btn_class = 'cta-pro-featured premium-shimmer';
                    if ($is_empire) $btn_class = 'cta-empire-gold premium-shimmer';
                    ?>
                    <a href="<?php echo home_url($p['link']); ?>" class="saas-link-btn <?php echo $btn_class; ?>"><?php echo esc_html($p['cta']); ?></a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-80 color-lighter font-bold text-center">
            <p>All plans include a 14-day money-back guarantee. No questions asked.</p>
        </div>
    </section>
</main>

<?php get_footer(); ?>
