<?php
/**
 * Template Name: Sales Page - Tactical Speed (Short-form)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header(); ?>

<main id="sales-short" class="landing-main bg-color">
    <!-- 1. THE HOOK: Pure Speed -->
    <section class="landing-content mx-auto pt-100">
        <div class="badge-ui mb-24 badge-vibrant">Results in 60 Seconds</div>
        <h1 class="landing-title text-gradient-vibrant">
            Build Your Digital Headquarters in 60 Seconds.
        </h1>
        <p class="landing-hero-text max-w-800 mx-auto">
            Ditch the bio-link mess. Capture leads and close deals with the fastest, most professional profile engine on the planet.
        </p>

        <div class="hero-claim-wrapper mt-60">
            <form action="<?php echo home_url('/register'); ?>" method="GET" class="hero-claim-form p-6 shadow-xl">
                <span class="hero-claim-prefix"><?php echo parse_url(home_url(), PHP_URL_HOST); ?>/</span>
                <input type="text" name="username" placeholder="yourname" class="hero-claim-input">
                <button type="submit" class="hero-claim-btn bg-vibrant-gradient">Launch Now →</button>
            </form>
            <p class="text-xs mt-20 color-lighter font-bold">✓ No credit card required. ✓ Instant Activation.</p>
        </div>
    </section>

    <!-- 2. THE STORY: The Frictionless Path -->
    <section class="section-padding">
        <div class="container-wide mx-auto grid-2 gap-80 flex-center">
            <div class="text-left">
                <h2 class="text-5xl font-black mb-30 ls-neg-3">Complexity is the enemy of conversion.</h2>
                <p class="text-xl color-light lh-1-6 mb-40">Your clients don't want a puzzle to solve; they want a path to take. Our engine removes the friction between your social media presence and your bank account.</p>

                <div class="bg-vibrant-gradient p-32 radius-24 color-white shadow-lg">
                    <h4 class="m-0 text-xl font-black">Elite Performance Standard</h4>
                    <p class="m-10-0-0 opacity-80">99.9% Uptime, < 1s Load Times, & Enterprise Security.</p>
                </div>
            </div>
            <div class="relative">
                <div class="iphone-mockup border-10 shadow-preview">
                    <div class="iphone-content p-32 bg-light-bg full-height">
                         <div class="iphone-avatar mb-24 bg-vibrant"></div>
                         <div class="iphone-line-lg mb-16"></div>
                         <div class="iphone-line-sm mb-40"></div>
                         <div class="iphone-btn-primary mb-15 bg-primary">BOOK CONSULTATION</div>
                         <div class="iphone-btn-glass bg-grey-light">GET MY FREE GUIDE</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. THE OFFER: Rapid Scale -->
    <section class="section-padding bg-subtle">
        <div class="container-standard mx-auto text-center">
            <h2 class="text-5xl font-black mb-60 tracking-tight">The Tactical Advantage</h2>
            <div class="grid-2 gap-30 text-left">
                <div class="card-white hover-lift border-light">
                    <strong class="text-primary text-xl display-block mb-10">01. Pro Visuals</strong>
                    <p class="color-light mb-0">High-end glassmorphism and modern typography that builds instant trust.</p>
                </div>
                <div class="card-white hover-lift border-light">
                    <strong class="text-primary text-xl display-block mb-10">02. Auto-Lead</strong>
                    <p class="color-light mb-0">Native forms that capture inquiries without making users leave your page.</p>
                </div>
                <div class="card-white hover-lift border-light">
                    <strong class="text-primary text-xl display-block mb-10">03. Smart Routing</strong>
                    <p class="color-light mb-0">Send users to different links based on their device or location automatically.</p>
                </div>
                <div class="card-white hover-lift border-light">
                    <strong class="text-primary text-xl display-block mb-10">04. NFC Ready</strong>
                    <p class="color-light mb-0">Sync your profile with any NFC tag for instant networking dominance.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. CLOSE: Join the Elite -->
    <section class="section-padding bg-dark color-white text-center">
        <div class="container-narrow mx-auto">
            <h2 class="text-5xl font-black mb-24 tracking-tight">Don't Get Left Behind.</h2>
            <p class="text-xl opacity-70 mb-60">The future of networking is digital. Secure your username before someone else does.</p>

            <a href="<?php echo home_url('/register'); ?>" class="btn-elite-launch bg-vibrant-gradient color-white shadow-gold full-width text-3xl p-32">
                Secure My Elite Link Now
            </a>

            <div class="mt-40 flex-center gap-20 flex-wrap text-sm font-bold color-lighter">
                <span>⚡ Setup in 60s</span>
                <span>🔥 Verified Expert Status</span>
                <span>💎 100% Whitelabel</span>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
