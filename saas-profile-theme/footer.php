<?php
/**
 * The template for displaying the footer.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Hide global footer on user profile pages
if ( ! get_query_var( 'saas_profile' ) ) : ?>

<footer id="colophon" class="footer-main bg-subtle">
    <div class="footer-container mx-auto">
        <!-- Column 1: Branding -->
        <div class="footer-branding">
            <div class="footer-logo-text font-black tracking-tight"><?php echo get_bloginfo('name'); ?></div>
            <p class="footer-tagline">The conversion-first digital identity engine for elite creators and top performers.</p>
            <div class="footer-social-links">
                <a href="#" class="footer-social-icon">𝕏</a>
                <a href="#" class="footer-social-icon">📸</a>
                <a href="#" class="footer-social-icon">💼</a>
            </div>
        </div>

        <!-- Column 2: Navigation Groups -->
        <div class="footer-nav-wrapper">
            <div>
                <h4 class="footer-nav-title">Platform</h4>
                <ul class="footer-nav-list">
                    <li class="footer-nav-item"><a href="<?php echo home_url('/directory'); ?>" class="footer-nav-link">Discovery</a></li>
                    <li class="footer-nav-item"><a href="<?php echo home_url('/pricing'); ?>" class="footer-nav-link">Pricing</a></li>
                    <li class="footer-nav-item"><a href="<?php echo home_url('/login'); ?>" class="footer-nav-link">Login</a></li>
                    <li class="footer-nav-item"><a href="<?php echo home_url('/register'); ?>" class="footer-nav-link">Register</a></li>
                </ul>
            </div>
            <div>
                <h4 class="footer-nav-title">Company</h4>
                <ul class="footer-nav-list">
                    <li class="footer-nav-item"><a href="<?php echo home_url('/about'); ?>" class="footer-nav-link">About Us</a></li>
                    <li class="footer-nav-item"><a href="<?php echo home_url('/contact'); ?>" class="footer-nav-link">Contact</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Success Stories</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Careers</a></li>
                </ul>
            </div>
            <div>
                <h4 class="footer-nav-title">Legal</h4>
                <ul class="footer-nav-list">
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Terms of Service</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Privacy Policy</a></li>
                    <li class="footer-nav-item"><a href="#" class="footer-nav-link">Cookie Policy</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="footer-bottom mx-auto">
        <div class="footer-copyright">
            &copy; <?php echo date('Y'); ?> <?php echo get_bloginfo('name'); ?>. All rights reserved.
        </div>
        <div class="footer-utility-links">
            Designed for the Elite 1%. 🚀
        </div>
    </div>
</footer>

<?php endif; // End if !get_query_var('saas_profile') ?>

<?php
// Restore Pro feature for custom footer scripts
$slug = get_query_var( 'saas_profile' );
if ($slug) {
    $profile = saas_get_profile_by_slug($slug);
    if ($profile) {
        $payments = new Saas_Payments();
        if ($payments->is_pro_user($profile->post_author)) {
            echo get_post_meta($profile->ID, '_saas_footer_scripts', true);
        }
    }
}
?>

<?php wp_footer(); ?>
</body>
</html>
