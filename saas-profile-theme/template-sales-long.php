<?php
/**
 * Template Name: Sales Page - Strategic Authority (Long-form)
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header(); ?>

<main id="sales-long" class="landing-main bg-color">
    <!-- Animated Mesh Background -->
    <div class="animated-mesh-bg">
        <div class="mesh-circle-1"></div>
        <div class="mesh-circle-2"></div>
    </div>

    <!-- 1. THE HOOK: The Authority Gap -->
    <section class="landing-content mx-auto pt-100">
        <div class="badge-ui mb-24">For Strategic Consultants & Elite Experts</div>
        <h1 class="landing-title text-gradient-primary">
            Stop Leaking High-Ticket Leads To Standard Link Trees.
        </h1>
        <p class="landing-hero-text max-w-800 mx-auto">
            You aren't a "content creator." You are an authority. Why is your digital home built on a platform designed for teenagers and hobbyists?
        </p>

        <div class="cta-actions mt-40">
            <a href="<?php echo home_url('/register'); ?>" class="btn-elite-launch shadow-gold">
                Claim Your Strategic Command Center
            </a>
            <p class="hero-claim-subtext mt-20">Takes 60 seconds. Changes your authority forever.</p>
        </div>
    </section>

    <!-- 2. THE STORY: The Founder's Epiphany -->
    <section class="section-padding bg-subtle">
        <div class="container-narrow mx-auto card-white p-64">
            <h2 class="text-4xl font-black mb-40 tracking-tight">"I was famous on social media, but my bank account didn't match my reach."</h2>

            <div class="flex gap-30 mb-30 flex-center">
                <div class="mx-auto shadow-md bg-primary radius-full avatar-fixed-80 border-white-4"></div>
                <div class="text-left">
                    <strong class="text-xl">A Message from the Founder</strong>
                    <p class="mb-0 color-light">Ex-Consultant turned Digital Architect</p>
                </div>
            </div>

            <div class="text-left lh-1-8 color-light text-lg">
                <p>I remember the moment everything changed. I had 50,000 followers. My posts were getting thousands of likes. I was 'successful' by every metric except the one that mattered: <strong>Revenue.</strong></p>

                <p>I was sending all my traffic to a standard link tree. It was a messy list of 15 different options. I realized that an <em>expert</em> doesn't give a list of options. An expert provides a <strong>path.</strong></p>

                <p>Most "link-in-bio" tools are digital graveyards. They are designed to keep people clicking around, not to convert them into high-ticket clients. I built the <strong>Authority Engine</strong> because you deserve a system that honors your expertise and automates your trust.</p>
            </div>

            <div class="mt-40 border-t pt-40">
                <blockquote class="font-italic text-2xl color-primary">
                    "You don't have a business problem; you have a vehicle problem. Let's upgrade your engine."
                </blockquote>
            </div>
        </div>
    </section>

    <!-- 3. THE OFFER: The Command Center -->
    <section class="section-padding bg-light border-t">
        <div class="container-wide mx-auto text-center">
            <h2 class="section-title-large text-6xl">Your Strategic Command Center</h2>
            <p class="text-2xl mb-80 color-light max-w-700 mx-auto">Everything you need to capture, convert, and scale your authority.</p>

            <div class="grid-3">
                <div class="card-white text-left hover-lift">
                    <div class="text-5xl mb-24">🎯</div>
                    <h3 class="mb-15">Lead Capture Funnels</h3>
                    <p class="color-light">Don't just list links. Capture high-intent leads directly in your bio with integrated forms that sync to your CRM.</p>
                </div>
                <div class="card-white text-left hover-lift">
                    <div class="text-5xl mb-24">📊</div>
                    <h3 class="mb-15">Conversion Intelligence</h3>
                    <p class="color-light">Track views, clicks, and actual conversion rates. Know exactly what drives revenue with mathematical precision.</p>
                </div>
                <div class="card-white text-left hover-lift">
                    <div class="text-5xl mb-24">📳</div>
                    <h3 class="mb-15">Elite Networking</h3>
                    <p class="color-light">Instant vCard exchange for real-world events. One tap, and you are saved in their phone. No app required.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. PRICING & CLOSE -->
    <section class="section-padding bg-dark color-white relative overflow-hidden">
        <div class="drift-glow-1"></div>
        <div class="container-standard mx-auto text-center relative z-1">
            <h2 class="text-6xl font-black mb-20 tracking-tight">Own Your Future. Today.</h2>
            <p class="text-2xl opacity-80 mb-60">Join the top 1% of creators who have graduated from "link lists" to "authority engines."</p>

            <div class="pricing-plan-card is-featured max-w-500 mx-auto p-64">
                <div class="badge-pro-price demo-card-badge-static bg-accent color-dark">MOST POPULAR</div>
                <h3 class="mb-0 text-3xl color-white">Elite Command</h3>
                <div class="text-5xl font-black mt-24 mb-24">$19<small class="text-lg opacity-70">/mo</small></div>
                <ul class="benefit-list mb-40 text-left color-white">
                    <li class="mb-12">✓ Unlimited Authority Engines</li>
                    <li class="mb-12">✓ Lead Generation CRM</li>
                    <li class="mb-12">✓ Custom Domain Mapping</li>
                    <li class="mb-12">✓ Elite Whitelabeling</li>
                </ul>
                <a href="<?php echo home_url('/register?plan=pro'); ?>" class="btn-elite-launch bg-white color-primary shadow-lg full-width">
                    Activate My Engine
                </a>
            </div>

            <div class="mt-80 opacity-60 flex-center gap-40 font-bold text-xs text-uppercase ls-2">
                <span>✓ 14-Day Money Back Guarantee</span>
                <span>✓ Cancel Anytime</span>
                <span>✓ Instant Setup</span>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
