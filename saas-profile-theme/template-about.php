<?php
/**
 * Template Name: About Us
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

$vision = get_option('saas_about_vision') ?: 'Empowering elite creators to own their digital real estate.';
?>

<main id="about-page" class="site-main bg-color">
    <section class="landing-content p-100-60">
        <div class="text-center mb-80">
            <h1 class="landing-title">Our Vision</h1>
            <p class="landing-hero-text">Building the future of digital identity for the world's top performers.</p>
        </div>

        <div class="stats-grid mb-100 align-stretch">
            <div class="feature-card-light text-left">
                <h2 class="mb-20">Why we built this.</h2>
                <p><?php echo nl2br(esc_html($vision)); ?></p>
            </div>
            <div class="feature-card-dark text-left">
                <div class="text-5xl-important mb-10">✨</div>
                <h3 class="color-white mb-10">Conversion First</h3>
                <p class="color-white-70">We don't just build link lists. We build lead machines designed by marketing experts.</p>
            </div>
        </div>

        <div class="feature-card-light container-narrow mx-auto text-left p-60">
            <h3>The Elite Standard</h3>
            <p>Our platform was born out of a simple observation: most link-in-bio tools are digital graveyards. They are cluttered, slow, and don't represent the authority of the people using them.</p>

            <hr class="border-t-only m-32-0">

            <p>We believe your digital home should be as professional as you are. That means fast loading times, high-end aesthetics, and native lead generation that works while you sleep.</p>
        </div>
    </section>
</main>

<?php get_footer(); ?>
