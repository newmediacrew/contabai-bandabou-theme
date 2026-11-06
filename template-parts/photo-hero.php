<?php
$heroImage   = (string) ($args['image'] ?? '');
$heroAlt     = (string) ($args['alt'] ?? '');
$heroTitle   = (string) ($args['title'] ?? '');
$heroTag     = ($args['tag'] ?? 'h1') === 'p' ? 'p' : 'h1';
$heroAttr    = (string) ($args['title_attr'] ?? '');
$heroEyebrow = (string) ($args['eyebrow'] ?? '');
$heroSub     = (string) ($args['subtitle'] ?? '');
$heroCrumbs  = (array) ($args['crumbs'] ?? []);
$heroClass   = ! empty($args['search']) ? ' has-search' : '';
?>
<section class="bandabou-page-hero<?php echo $heroClass; ?>">
    <div class="bandabou-hero-media">
        <?php if ($heroImage !== '') : ?>
            <img src="<?php echo esc_url($heroImage); ?>" alt="<?php echo esc_attr($heroAlt); ?>" fetchpriority="high" decoding="async" class="bandabou-hero-slide is-active">
        <?php endif; ?>
        <div class="bandabou-hero-shade"></div>
        <div class="bandabou-hero-overlay"></div>
        <svg aria-hidden="true" viewBox="0 0 1440 110" preserveAspectRatio="none" width="100%" height="110" class="absolute inset-x-0 -bottom-px z-[2] block"><path d="M0 50 C 240 6 520 94 760 55 S 1200 11 1440 44 V110 H0 Z" fill="rgba(15,163,163,0.35)"></path><path d="M0 77 C 300 38 560 110 860 75 S 1260 46 1440 68 V110 H0 Z" fill="#FBF5EA"></path></svg>
    </div>
    <div class="bandabou-page-hero-inner">
        <?php if ($heroCrumbs !== [] && get_option('bandabou_breadcrumbs_enabled', '1') === '1' && class_exists('Contabai\\View')) : ?>
            <?php echo \Contabai\View::render('components.breadcrumb', ['crumbs' => $heroCrumbs]); ?>
        <?php endif; ?>
        <?php if ($heroEyebrow !== '') : ?>
            <p class="bandabou-villa-place"><?php echo esc_html($heroEyebrow); ?></p>
        <?php endif; ?>
        <<?php echo $heroTag; ?> class="bandabou-page-title"<?php echo $heroAttr; ?>><?php echo esc_html($heroTitle); ?></<?php echo $heroTag; ?>>
        <?php if ($heroSub !== '') : ?>
            <div class="bandabou-villa-tagline"><?php echo wp_kses_post($heroSub); ?></div>
        <?php endif; ?>
    </div>
</section>
