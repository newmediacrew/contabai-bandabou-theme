<?php

use BandabouTheme\Heroicon;
use BandabouTheme\Villas;

$heroEyebrow = (string) get_option('bandabou_hero_eyebrow', __('Villapark Fontein · Bandabou · Curaçao', 'contabai-bandabou-theme'));
$heroHeading = (string) get_option('bandabou_hero_heading', __('Your villa in peaceful Bandabou', 'contabai-bandabou-theme'));
$heroSub     = (string) get_option('bandabou_hero_subheading', __('Carefully furnished villas on Villapark Fontein, with views over the hills and the Caribbean Sea. You book directly with us, the local Bandabou Curaçao team.', 'contabai-bandabou-theme'));

$badges = [];
for ($i = 1; $i <= 3; $i++) {
    $text = trim((string) get_option('bandabou_hero_badge' . $i . '_text', ''));
    if ($text !== '') {
        $badges[] = ['n' => $i, 'text' => $text, 'icon' => (string) get_option('bandabou_hero_badge' . $i . '_icon', 'check')];
    }
}

$heroCount = min(max((int) get_option('bandabou_hero_slides', '5'), 1), 8);
$heroSlides = [];
if (get_option('bandabou_hero_listing_photos', '1') === '1') {
    foreach (Villas::random($heroCount) as $villa) {
        $photo = $villa['photos'][0] ?? [];
        if (! empty($photo['hero'])) {
            $srcset = array_filter([
                ! empty($photo['medium']) ? $photo['medium'] . ' 1280w' : '',
                $photo['hero'] . ' 1920w',
                ! empty($photo['hero_large']) ? $photo['hero_large'] . ' 2560w' : '',
            ]);
            $heroSlides[] = ['src' => $photo['hero'], 'srcset' => implode(', ', $srcset), 'alt' => (string) ($photo['alt'] ?? '')];
        }
    }
}

$files = glob(get_theme_file_path('assets/img/hero/bandabou-*.webp')) ?: [];
shuffle($files);
foreach (array_slice($files, 0, $heroCount - count($heroSlides)) as $file) {
    $heroSlides[] = ['src' => get_theme_file_uri('assets/img/hero/' . basename($file)), 'srcset' => '', 'alt' => ''];
}

$heroAlign = get_option('bandabou_hero_align', 'left');
if (! in_array($heroAlign, ['left', 'center', 'right'], true)) {
    $heroAlign = 'left';
}

$heroAnim = get_option('bandabou_hero_animation', 'fade');
if (! in_array($heroAnim, ['zoom-in', 'zoom-out', 'pan-left', 'pan-right', 'pan-up', 'pan-down', 'diagonal', 'fade', 'breathe', 'static'], true)) {
    $heroAnim = 'fade';
}
?>
<section id="top" class="bandabou-hero align-<?php echo esc_attr($heroAlign); ?> bandabou-hero-anim-<?php echo esc_attr($heroAnim); ?>">
    <div class="bandabou-hero-media">
        <?php foreach ($heroSlides as $idx => $slide) : ?>
            <?php if ($idx === 0) : ?>
                <img src="<?php echo esc_url($slide['src']); ?>"<?php if ($slide['srcset'] !== '') : ?> srcset="<?php echo esc_attr($slide['srcset']); ?>" sizes="100vw"<?php endif; ?> alt="<?php echo esc_attr($slide['alt']); ?>" fetchpriority="high" decoding="async" class="bandabou-hero-slide is-active">
            <?php else : ?>
                <img data-src="<?php echo esc_url($slide['src']); ?>"<?php if ($slide['srcset'] !== '') : ?> data-srcset="<?php echo esc_attr($slide['srcset']); ?>" sizes="100vw"<?php endif; ?> alt="<?php echo esc_attr($slide['alt']); ?>" decoding="async" class="bandabou-hero-slide">
            <?php endif; ?>
        <?php endforeach; ?>
        <div class="bandabou-hero-shade"></div>
        <div class="bandabou-hero-overlay"></div>
        <svg aria-hidden="true" viewBox="0 0 1440 100" preserveAspectRatio="none" width="100%" height="100" class="absolute inset-x-0 -bottom-px z-[2] block"><path d="M0 45 C 240 5 520 85 760 50 S 1200 10 1440 40 V100 H0 Z" fill="rgba(15,163,163,0.35)"></path><path d="M0 70 C 300 35 560 100 860 68 S 1260 42 1440 62 V100 H0 Z" fill="#FFFFFF"></path></svg>
    </div>
    <div class="bandabou-hero-inner">
        <?php if ($heroEyebrow !== '') : ?>
            <p class="bandabou-hero-eyebrow"><?php echo esc_html($heroEyebrow); ?></p>
        <?php endif; ?>
        <?php if ($heroHeading !== '') : ?>
            <h1 class="bandabou-hero-title"><?php echo esc_html($heroHeading); ?></h1>
        <?php endif; ?>
        <?php if ($heroSub !== '') : ?>
            <p class="bandabou-hero-sub"><?php echo esc_html($heroSub); ?></p>
        <?php endif; ?>
        <?php if ($badges !== []) : ?>
            <div class="bandabou-hero-badges">
                <?php foreach ($badges as $badge) : ?>
                    <span class="bandabou-hero-badge bandabou-hero-badge-<?php echo (int) $badge['n']; ?>"><?php echo Heroicon::solid($badge['icon'], 'w-4 h-4'); ?><?php echo esc_html($badge['text']); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (shortcode_exists('contabai_search')) : ?>
            <div class="bandabou-hero-search"><?php echo do_shortcode('[contabai_search]'); ?></div>
        <?php endif; ?>
    </div>
</section>
<script>
(function () {
    const slides = document.querySelectorAll('.bandabou-hero-slide');
    if (slides.length < 2 || document.querySelector('.bandabou-hero-anim-static') || window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }
    const fetchSlide = function (slide) {
        if (slide.dataset.srcset) { slide.srcset = slide.dataset.srcset; slide.removeAttribute('data-srcset'); }
        if (slide.dataset.src) { slide.src = slide.dataset.src; slide.removeAttribute('data-src'); }
    };
    const run = function () {
        let current = 0;
        setTimeout(function () { fetchSlide(slides[1]); }, 3000);
        setInterval(function () {
            slides[current].classList.remove('is-active');
            current = (current + 1) % slides.length;
            slides[current].classList.add('is-active');
            fetchSlide(slides[(current + 1) % slides.length]);
        }, 8000);
    };
    if (document.readyState === 'complete') { run(); } else { window.addEventListener('load', run); }
})();
</script>
