<!-- Benefits Section -->
<section class="section-padding bg-subtle relative z-1">
    <div class="container-wide flex-wrap flex-center gap-80 mx-auto">
        <div class="flex-1 min-w-320">
            <h2 class="section-title-large text-6xl font-black tracking-tight mb-40">Stop losing traffic.<br>Start building your list.</h2>
            <ul class="benefit-list">
                <?php
                $benefits = json_decode(get_option('saas_home_benefits'), true) ?: [
                    'One link to rule them all',
                    'Capture leads even while you sleep',
                    'Instant vCard exchange for networking',
                    'Beautiful, mobile-first design'
                ];
                foreach ($benefits as $b) : ?>
                    <li class="benefit-item">
                        <div class="benefit-check">✓</div>
                        <?php echo esc_html($b); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="flex-1 min-w-320">
            <div class="card-light relative shadow-xl p-48">
                <div class="demo-card-badge">Live Demo</div>
                <h4 class="mt-0 text-3xl font-black mb-10 tracking-tight">Your Profile Preview</h4>
                <p class="mb-30 color-lighter">See how your business card looks on mobile instantly.</p>

                <div class="hero-image-perspective">
                    <div class="iphone-mockup relative overflow-visible">
                        <div class="absolute -left-20 top-40 z-10 animate-float">
                            <div class="bg-vibrant-gradient p-15 radius-15 shadow-lg color-white text-xs font-bold">
                                🚀 New Lead Captured!
                            </div>
                        </div>
                        <div class="iphone-content p-32">
                            <div class="iphone-avatar mb-24"></div>
                            <div class="iphone-line-lg mb-16"></div>
                            <div class="iphone-line-sm mb-32"></div>
                            <div class="iphone-btn-primary mb-12">GET STARTED</div>
                            <div class="iphone-btn-secondary"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Grid -->
<section id="features" class="section-padding-large bg-light border-t">
    <div class="container-wide text-center mx-auto">
        <h2 class="section-title-large text-6xl font-black tracking-tight mb-20">Everything you need to grow online</h2>
        <p class="mb-100 text-2xl color-light max-w-700 mx-auto">Powerful tools designed for the modern creator economy and top performers.</p>
        <div class="grid-3">
            <?php
            $features = json_decode(get_option('saas_home_features'), true) ?: [
                ['icon' => '🚀', 'title' => 'Fast Setup', 'desc' => 'Launch your profile in under 60 seconds with our setup wizard.'],
                ['icon' => '📊', 'title' => 'Real-Time Insights', 'desc' => 'Track every click, view, and NFC tap with our performance analytics.'],
                ['icon' => '🎯', 'title' => 'Lead CRM', 'desc' => 'Convert traffic into customers with built-in forms and lead management.'],
                ['icon' => '💾', 'title' => 'Digital Card', 'desc' => 'Instant vCard exchange and QR codes for offline networking.'],
                ['icon' => '🎨', 'title' => 'Custom Branding', 'desc' => 'Your brand, your colors. Full control over every visual element.'],
                ['icon' => '🔒', 'title' => 'Password Protected', 'desc' => 'Secure your premium content with individual link passwords.']
            ];

            foreach ($features as $f) : ?>
                <div class="card-white text-left hover-lift">
                    <div class="feature-icon-large"><?php echo esc_html($f['icon']); ?></div>
                    <h3 class="mb-15 text-2xl font-black"><?php echo esc_html($f['title']); ?></h3>
                    <p class="color-light lh-1-6"><?php echo esc_html($f['desc']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
