<?php
/**
 * Template Name: Contact
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
?>

<main id="contact-page" class="site-main bg-color">
    <section class="landing-content pt-100">
        <div class="contact-container bg-subtle radius-lg shadow-xl">
            <h1 class="text-6xl font-black mb-16">Get in Touch</h1>
            <p class="text-xl mb-40 color-light">Have questions? We are here to help you scale.</p>

            <form action="" method="POST" class="text-left">
                <div class="mb-24">
                    <label class="contact-label">Your Name</label>
                    <input type="text" name="contact_name" class="contact-input" required>
                </div>
                <div class="mb-24">
                    <label class="contact-label">Email Address</label>
                    <input type="email" name="contact_email" class="contact-input" required>
                </div>
                <div class="mb-32">
                    <label class="contact-label">Message</label>
                    <textarea name="contact_message" class="contact-input contact-textarea" required></textarea>
                </div>
                <button type="submit" class="saas-link-btn style-featured full-width p-20 font-bold">Send Message →</button>
            </form>

            <div class="contact-footer">
                <span>Email: support@example.com</span>
                <span>Response Time: < 24 Hours</span>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
