<?php

use BandabouTheme\Heroicon;

$listingsUrl = home_url('/' . (defined('CONTABAI_LISTINGS_PAGE_SLUG') ? CONTABAI_LISTINGS_PAGE_SLUG : 'contabai-listings'));

$strip = [
    ['icon' => 'home', 'bg' => '#E3F2EF', 'color' => '#0B6E6C', 'title' => __('Booked directly with us', 'contabai-bandabou-theme'), 'text' => __('No booking platform in between: you arrange everything directly with the team that knows the villas.', 'contabai-bandabou-theme')],
    ['icon' => 'map-pin', 'bg' => '#FDF1DC', 'color' => '#B5650A', 'title' => __('Local team in Bandabou', 'contabai-bandabou-theme'), 'text' => __('We live on the island and are there for you, from the key handover to your departure.', 'contabai-bandabou-theme')],
    ['icon' => 'sun', 'bg' => '#DDF3F3', 'color' => '#0FA3A3', 'title' => __('Made for outdoor living', 'contabai-bandabou-theme'), 'text' => __('A porch, sun loungers and an outdoor dining area at every villa, and a private pool at most of them.', 'contabai-bandabou-theme')],
];

$highlights = [
    __('Peace and space', 'contabai-bandabou-theme'),
    __('Beaches nearby', 'contabai-bandabou-theme'),
    __('Hiking and snorkelling', 'contabai-bandabou-theme'),
    __('Free parking at the villa', 'contabai-bandabou-theme'),
];

$reasons = [
    ['icon' => 'chat-bubble-left-right', 'title' => __('Quick answers on WhatsApp', 'contabai-bandabou-theme'), 'text' => __('A question about the villa, your arrival or a tip for a beach? Just send a message.', 'contabai-bandabou-theme')],
    ['icon' => 'key', 'title' => __('We welcome you ourselves', 'contabai-bandabou-theme'), 'text' => __('Our team hands you the key and shows you the villa. Check-in from 15:00, check-out until 11:00.', 'contabai-bandabou-theme')],
    ['icon' => 'shield-check', 'title' => __('Help during your stay', 'contabai-bandabou-theme'), 'text' => __('If something goes wrong, we sort it out. Cleaning during your stay is available on request.', 'contabai-bandabou-theme')],
];

$steps = [
    ['title' => __('Create an account and choose your villa', 'contabai-bandabou-theme'), 'text' => __('Create a free account, find the villa that suits your group and pick your travel dates in the calendar.', 'contabai-bandabou-theme')],
    ['title' => __('Check the costs', 'contabai-bandabou-theme'), 'text' => __('Before you book, you see the full price of your stay.', 'contabai-bandabou-theme')],
    ['title' => __('Book your stay', 'contabai-bandabou-theme'), 'text' => __('Confirm your booking online. You book directly with us, no platform in between.', 'contabai-bandabou-theme')],
    ['title' => __('Personal contact', 'contabai-bandabou-theme'), 'text' => __('After booking, our team gets in touch personally about your arrival, the key handover and any wishes.', 'contabai-bandabou-theme')],
];

$pageBg = sanitize_hex_color((string) get_option('bandabou_bg_color', '#FBF5EA')) ?: '#FBF5EA';
$wave = function (string $fill, string $back = 'transparent', bool $alt = false): string {
    $rear  = $alt ? 'M0 30 C 240 0 520 64 760 36 S 1200 6 1440 34 V70 H0 Z' : 'M0 38 C 260 70 520 6 780 30 S 1220 66 1440 24 V70 H0 Z';
    $front = $alt ? 'M0 46 C 320 70 620 18 920 42 S 1300 62 1440 40 V70 H0 Z' : 'M0 50 C 300 20 600 76 900 46 S 1280 30 1440 44 V70 H0 Z';
    $defs  = str_starts_with($fill, 'url(') ? '<defs><linearGradient id="bandabou-why-wave" x1="0" x2="1" y1="0" y2="0"><stop offset="0" stop-color="#0B5A6E"/><stop offset="1" stop-color="#109A9D"/></linearGradient></defs>' : '';
    return '<div aria-hidden="true" class="leading-[0]" style="background: ' . esc_attr($back) . ';"><svg viewBox="0 0 1440 70" preserveAspectRatio="none" class="block h-11 w-full md:h-[70px]">' . $defs . '<path d="' . $rear . '" fill="rgba(15,163,163,0.2)"></path><path d="' . $front . '" fill="' . esc_attr($fill) . '"></path></svg></div>';
};
?>
<section id="highlights" aria-label="<?php esc_attr_e('Why Bandabou Curaçao', 'contabai-bandabou-theme'); ?>" class="scroll-mt-24 border-b border-[#EFE4D1] bg-white">
    <div class="bandabou-container grid grid-cols-1 gap-6 py-8 md:grid-cols-3 md:gap-5 lg:gap-8 lg:py-10">
        <?php foreach ($strip as $item) : ?>
            <div class="flex items-start gap-4">
                <span class="flex h-12 w-12 flex-none items-center justify-center rounded-[14px]" style="background: <?php echo esc_attr($item['bg']); ?>; color: <?php echo esc_attr($item['color']); ?>;"><?php echo Heroicon::outline($item['icon'], 'w-6 h-6'); ?></span>
                <div>
                    <h2 class="bandabou-small-title text-[17px] text-[color:var(--heading-color,#0B4F6C)]"><?php echo esc_html($item['title']); ?></h2>
                    <p class="mt-1.5 text-[15px] leading-[1.55] text-[#5A5348]"><?php echo esc_html($item['text']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section id="villas" class="scroll-mt-20 pb-14 pt-16 lg:pb-[88px] lg:pt-24">
    <div class="bandabou-container">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div class="max-w-[640px]">
                <p class="bandabou-eyebrow"><?php esc_html_e('Our villas', 'contabai-bandabou-theme'); ?></p>
                <h2 class="bandabou-section-title mt-2.5"><?php esc_html_e('Villas across Bandabou', 'contabai-bandabou-theme'); ?></h2>
                <p class="mt-3.5 text-[17px] leading-[1.6] text-[#5A5348]"><?php esc_html_e('Wake up between green hills and quiet bays on the west side of Curaçao. Find the villa that suits your group.', 'contabai-bandabou-theme'); ?></p>
            </div>
            <a href="<?php echo esc_url($listingsUrl); ?>" class="inline-flex min-h-11 items-center gap-1.5 text-[15px] font-bold text-[color:var(--content-link-color,#0B6E6C)] no-underline"><?php esc_html_e('Explore available villas', 'contabai-bandabou-theme'); ?><?php echo Heroicon::outline('chevron-right', 'w-[18px] h-[18px]'); ?></a>
        </div>
        <div class="bandabou-villas mt-11"><?php echo do_shortcode('[contabai_random_listings count="12"]'); ?></div>
    </div>
</section>

<?php echo $wave('#FFFFFF'); ?>

<section id="bandabou" class="scroll-mt-24 bg-white pb-[72px] pt-10 lg:pb-[104px] lg:pt-14">
    <div class="bandabou-container grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-[72px]">
        <div class="relative">
            <img src="<?php echo esc_url(get_theme_file_uri('assets/img/bandabou-porch.webp')); ?>" alt="<?php esc_attr_e('Covered porch of Villa Moreno with a view over green Bandabou', 'contabai-bandabou-theme'); ?>" loading="lazy" decoding="async" class="block aspect-[5/4] w-full rounded-[28px] object-cover">
            <div class="bandabou-welcome">
                <p class="contabai-display m-0 text-[25px] font-semibold leading-[1.2]">Bon bini na Bandabou</p>
                <p class="mt-1.5 text-sm leading-[1.5] text-white/90"><?php esc_html_e('The quiet west side of Curaçao', 'contabai-bandabou-theme'); ?></p>
            </div>
        </div>
        <div>
            <p class="bandabou-eyebrow"><?php esc_html_e('About Bandabou', 'contabai-bandabou-theme'); ?></p>
            <h2 class="bandabou-section-title mt-2.5"><?php esc_html_e('The quiet west side of Curaçao', 'contabai-bandabou-theme'); ?></h2>
            <p class="mt-5 text-[17px] leading-[1.7] text-[color:var(--content-text-color,#3A4A52)]"><?php esc_html_e('Bandabou is the green, hilly west of the island. Here you will find hiking trails through Christoffelpark, small bays with clear water and beaches like Cas Abou, Grote Knip and Playa Lagun, where you can snorkel straight from the shore.', 'contabai-bandabou-theme'); ?></p>
            <p class="mt-4 text-[17px] leading-[1.7] text-[color:var(--content-text-color,#3A4A52)]"><?php esc_html_e('Our villas are tucked away in the green hills of Bandabou: quiet, close to the beaches, and in the evening a Caribbean sunset from your terrace.', 'contabai-bandabou-theme'); ?></p>
            <ul class="mt-[26px] grid list-none grid-cols-2 gap-x-6 gap-y-3.5 p-0 text-[15px] font-semibold text-[color:var(--heading-color,#0B4F6C)]">
                <?php foreach ($highlights as $highlight) : ?>
                    <li class="flex items-center gap-2.5"><span class="flex-none text-[#EE8A12]"><?php echo Heroicon::outline('check', 'w-5 h-5'); ?></span><?php echo esc_html($highlight); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<?php echo $wave('url(#bandabou-why-wave)', '#FFFFFF', true); ?>
<section id="how-we-work" class="bandabou-why scroll-mt-24 py-16 text-white lg:py-24">
    <div class="bandabou-container">
        <div>
            <p class="bandabou-eyebrow text-bandabou-gold!"><?php esc_html_e('This is how we work', 'contabai-bandabou-theme'); ?></p>
            <h2 class="bandabou-section-title mt-2.5 text-white!"><?php esc_html_e('Personal contact, from booking to departure', 'contabai-bandabou-theme'); ?></h2>
        </div>
        <div class="mt-8 flex flex-col gap-3.5 lg:mt-11">
            <?php foreach ($reasons as $reason) : ?>
                <div class="flex items-start gap-4 rounded-[18px] border border-white/15 bg-white/5 px-5 py-[18px]">
                    <span class="mt-0.5 flex-none text-bandabou-gold"><?php echo Heroicon::outline($reason['icon'], 'w-6 h-6'); ?></span>
                    <div>
                        <h3 class="bandabou-small-title text-[17px]"><?php echo esc_html($reason['title']); ?></h3>
                        <p class="mt-1 text-[15px] leading-[1.55] text-[#D6EEF2]"><?php echo esc_html($reason['text']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php echo $wave($pageBg, 'linear-gradient(90deg, #0D7584 0%, #0E8C96 40%, #12A7A3 100%)'); ?>

<section id="how-to-book" class="scroll-mt-24 pb-16 pt-16 lg:pb-24 lg:pt-24">
    <div class="bandabou-container">
        <div class="bandabou-journey">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div class="max-w-[640px]">
                    <p class="bandabou-eyebrow"><?php esc_html_e('How to book', 'contabai-bandabou-theme'); ?></p>
                    <h2 class="bandabou-section-title mt-2.5"><?php esc_html_e('Your villa booked in four steps', 'contabai-bandabou-theme'); ?></h2>
                </div>
                <a href="<?php echo esc_url($listingsUrl); ?>" class="bandabou-cta min-h-13 justify-center rounded-[14px]! px-6 text-[15px]"><?php esc_html_e('Explore available villas', 'contabai-bandabou-theme'); ?><?php echo Heroicon::outline('chevron-right', 'w-[18px] h-[18px]'); ?></a>
            </div>
            <div class="relative mt-12 lg:mt-14">
                <svg aria-hidden="true" viewBox="0 0 1000 80" preserveAspectRatio="none" class="bandabou-journey-trail"><path d="M60 40 C 180 -10 280 90 380 40 S 580 -10 640 40 S 840 90 940 40" fill="none" stroke="currentColor" stroke-width="2.5" stroke-dasharray="2 10" stroke-linecap="round" vector-effect="non-scaling-stroke"/></svg>
                <ol class="relative grid list-none grid-cols-1 gap-10 p-0 sm:grid-cols-2 lg:grid-cols-4 lg:gap-8">
                    <?php foreach ($steps as $stepIndex => $step) : ?>
                        <li class="flex flex-col items-center text-center">
                            <span class="bandabou-journey-stop">
                                <img src="<?php echo esc_url(get_theme_file_uri('assets/img/steps/step-' . ($stepIndex + 1) . '.webp')); ?>" alt="" loading="lazy" decoding="async" width="360" height="360">
                                <span class="bandabou-journey-number"><?php echo (int) $stepIndex + 1; ?></span>
                            </span>
                            <h3 class="contabai-display mt-6 text-[25px] font-semibold leading-[1.15] text-[color:var(--heading-color,#0B4F6C)]"><?php echo esc_html($step['title']); ?></h3>
                            <p class="mt-2.5 max-w-[260px] text-[15px] leading-[1.6] text-[#5A5348]"><?php echo esc_html($step['text']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </div>
</section>

<?php echo $wave('#EAF5F2'); ?>
<section id="team" class="scroll-mt-24 bg-[#EAF5F2] pb-14 pt-8 lg:pb-20 lg:pt-12">
    <div class="bandabou-container">
        <figure class="bandabou-quote-card m-0 grid grid-cols-1 overflow-hidden lg:grid-cols-[2fr_3fr]">
            <img src="<?php echo esc_url(get_theme_file_uri('assets/img/bandabou-moreno-pool.webp')); ?>" alt="" loading="lazy" decoding="async" class="block aspect-[16/10] h-full w-full object-cover lg:aspect-auto">
            <div class="flex flex-col justify-center px-7 py-9 lg:px-14 lg:py-14">
                <span aria-hidden="true" class="contabai-display block h-10 text-[96px] leading-[.8] text-[#EE8A12]">&ldquo;</span>
                <blockquote class="contabai-display m-0 mt-5 text-[23px] font-semibold leading-[1.35] text-[color:var(--heading-color,#0B4F6C)] lg:text-[27px]"><?php esc_html_e('Your holiday starts with a place that suits you. From a cosy holiday home among the green hills to a luxury villa with a sea view: we help you choose and are ready for you on Curaçao. Book directly with us and enjoy personal attention, local tips and a warm welcome. This is where your Curaçao begins.', 'contabai-bandabou-theme'); ?>&rdquo;</blockquote>
                <figcaption class="mt-7 flex items-center gap-3.5 border-t border-[#F0E6D5] pt-6">
                    <img src="<?php echo esc_url(get_theme_file_uri('assets/img/logo.webp')); ?>" alt="" loading="lazy" class="h-auto w-12">
                    <span>
                        <span class="block text-base font-bold text-[color:var(--heading-color,#0B4F6C)]"><?php esc_html_e('Team Bandabou Curaçao', 'contabai-bandabou-theme'); ?></span>
                        <span class="block text-sm text-[#5A5348]"><?php esc_html_e('Locally trusted. Personally arranged.', 'contabai-bandabou-theme'); ?></span>
                    </span>
                </figcaption>
            </div>
        </figure>
    </div>
</section>
<?php echo $wave($pageBg, '#EAF5F2', true); ?>
