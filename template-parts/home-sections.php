<?php

use BandabouTheme\Heroicon;

$listingsUrl = home_url('/' . (defined('CONTABAI_LISTINGS_PAGE_SLUG') ? CONTABAI_LISTINGS_PAGE_SLUG : 'contabai-listings'));

$strip = [
    ['icon' => 'home', 'bg' => '#E3F2EF', 'color' => '#0B6E6C', 'title' => __('Booked directly with us', 'contabai-bandabou-theme'), 'text' => __('No booking platform in between: you arrange everything directly with the team that knows the villas.', 'contabai-bandabou-theme')],
    ['icon' => 'map-pin', 'bg' => '#FDF1DC', 'color' => '#B5650A', 'title' => __('Local team in Julianadorp', 'contabai-bandabou-theme'), 'text' => __('We live on the island and are there for you, from the key handover to your departure.', 'contabai-bandabou-theme')],
    ['icon' => 'sun', 'bg' => '#DDF3F3', 'color' => '#0FA3A3', 'title' => __('A private pool at every villa', 'contabai-bandabou-theme'), 'text' => __('Cool off whenever you like, with sun loungers and a covered porch for the hottest hours.', 'contabai-bandabou-theme')],
];

$highlights = [
    __('Gated villa park', 'contabai-bandabou-theme'),
    __('Beaches nearby', 'contabai-bandabou-theme'),
    __('Hiking and snorkelling', 'contabai-bandabou-theme'),
    __('Free parking at the villa', 'contabai-bandabou-theme'),
];

$reasons = [
    ['icon' => 'chat-bubble-left-right', 'title' => __('Quick answers on WhatsApp', 'contabai-bandabou-theme'), 'text' => __('A question about the villa, your arrival or a tip for a beach? Just send a message.', 'contabai-bandabou-theme')],
    ['icon' => 'key', 'title' => __('We welcome you ourselves', 'contabai-bandabou-theme'), 'text' => __('Our team hands you the key and shows you the villa. Check-in from 15:00, check-out until 11:00.', 'contabai-bandabou-theme')],
    ['icon' => 'shield-check', 'title' => __('Help during your stay', 'contabai-bandabou-theme'), 'text' => __('If something goes wrong, we sort it out. Cleaning during your stay is available on request.', 'contabai-bandabou-theme')],
];
?>
<section aria-label="<?php esc_attr_e('Why Bandabou Curaçao', 'contabai-bandabou-theme'); ?>" class="border-b border-[#EFE4D1] bg-white">
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
                <h2 class="bandabou-section-title mt-2.5"><?php esc_html_e('Villas in one peaceful place', 'contabai-bandabou-theme'); ?></h2>
                <p class="mt-3.5 text-[17px] leading-[1.6] text-[#5A5348]"><?php esc_html_e('All on Villapark Fontein, all with their own pool. Choose the villa that suits your group.', 'contabai-bandabou-theme'); ?></p>
            </div>
            <a href="<?php echo esc_url($listingsUrl); ?>" class="inline-flex min-h-11 items-center gap-1.5 text-[15px] font-bold text-[color:var(--content-link-color,#0B6E6C)] no-underline"><?php esc_html_e('Compare all villas', 'contabai-bandabou-theme'); ?><?php echo Heroicon::outline('chevron-right', 'w-[18px] h-[18px]'); ?></a>
        </div>
        <div class="bandabou-villas mt-11"><?php echo do_shortcode('[contabai_random_listings count="9"]'); ?></div>
    </div>
</section>

<section id="bandabou" class="scroll-mt-24 pb-[72px] lg:pb-[104px]">
    <div class="bandabou-container grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-[72px]">
        <div class="relative">
            <img src="<?php echo esc_url(get_theme_file_uri('assets/img/bandabou-porch.webp')); ?>" alt="<?php esc_attr_e('Covered porch of Villa Moreno with a view over green Bandabou', 'contabai-bandabou-theme'); ?>" loading="lazy" decoding="async" class="block aspect-[5/4] w-full rounded-[28px] object-cover">
            <div class="bandabou-welcome">
                <p class="contabai-display m-0 text-[25px] font-semibold leading-[1.2]">Bon bini na Bandabou</p>
                <p class="mt-1.5 text-sm leading-[1.5] text-white/90"><?php esc_html_e('Welcome to the most beautiful side of the island.', 'contabai-bandabou-theme'); ?></p>
            </div>
        </div>
        <div>
            <p class="bandabou-eyebrow"><?php esc_html_e('About Bandabou & Fontein', 'contabai-bandabou-theme'); ?></p>
            <h2 class="bandabou-section-title mt-2.5"><?php esc_html_e('The quiet west side of Curaçao', 'contabai-bandabou-theme'); ?></h2>
            <p class="mt-5 text-[17px] leading-[1.7] text-[color:var(--content-text-color,#3A4A52)]"><?php esc_html_e('Bandabou is the green, hilly west of the island. Here you will find hiking trails through Christoffelpark, small bays with clear water and beaches like Cas Abou, Grote Knip and Playa Lagun, where you can snorkel straight from the shore.', 'contabai-bandabou-theme'); ?></p>
            <p class="mt-4 text-[17px] leading-[1.7] text-[color:var(--content-text-color,#3A4A52)]"><?php esc_html_e('Our villas are on Villapark Fontein: a quiet, gated park on a hill, with wide views and a Caribbean sunset from your own terrace in the evening.', 'contabai-bandabou-theme'); ?></p>
            <ul class="mt-[26px] grid list-none grid-cols-2 gap-x-6 gap-y-3.5 p-0 text-[15px] font-semibold text-[color:var(--heading-color,#0B4F6C)]">
                <?php foreach ($highlights as $highlight) : ?>
                    <li class="flex items-center gap-2.5"><span class="flex-none text-[#EE8A12]"><?php echo Heroicon::outline('check', 'w-5 h-5'); ?></span><?php echo esc_html($highlight); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<section class="bandabou-why py-16 text-white lg:py-24">
    <div class="bandabou-container grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-[72px]">
        <div>
            <p class="bandabou-eyebrow !text-bandabou-gold"><?php esc_html_e('Why book with us', 'contabai-bandabou-theme'); ?></p>
            <h2 class="bandabou-section-title mt-2.5 !text-white"><?php esc_html_e('Personal contact, from booking to departure', 'contabai-bandabou-theme'); ?></h2>
            <div class="mt-8 flex flex-col gap-3.5">
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
        <figure class="relative m-0 rounded-[28px] bg-bandabou-cream px-7 pb-8 pt-8 text-bandabou-ink lg:px-11 lg:pb-10 lg:pt-11">
            <span aria-hidden="true" class="contabai-display block h-9 text-[88px] leading-[.8] text-[#EE8A12]">&ldquo;</span>
            <blockquote class="contabai-display m-0 mt-[22px] text-[26px] font-semibold leading-[1.3] text-[color:var(--heading-color,#0B4F6C)] lg:text-[31px]"><?php esc_html_e('We know every villa and every little beach in Bandabou. That personal contact makes the difference for our guests. Ayo, see you soon on Curaçao!', 'contabai-bandabou-theme'); ?></blockquote>
            <figcaption class="mt-[26px] flex items-center gap-3.5 border-t border-[#E6D6B8] pt-[22px]">
                <img src="<?php echo esc_url(get_theme_file_uri('assets/img/logo.webp')); ?>" alt="" loading="lazy" class="h-auto w-14">
                <span>
                    <span class="block text-base font-bold text-[color:var(--heading-color,#0B4F6C)]">Joost van Zweeden</span>
                    <span class="block text-sm text-[#5A5348]"><?php esc_html_e('Owner, Bandabou Curaçao', 'contabai-bandabou-theme'); ?></span>
                </span>
            </figcaption>
        </figure>
    </div>
</section>

<section id="verhuren" class="scroll-mt-24 pt-14 lg:pt-24">
    <div class="bandabou-container">
        <div class="flex flex-wrap items-center justify-between gap-8 rounded-[28px] border border-[#E6D6B8] bg-bandabou-sand px-7 py-9 lg:px-14 lg:py-12">
            <div class="max-w-[720px]">
                <p class="bandabou-eyebrow"><?php esc_html_e('List your home?', 'contabai-bandabou-theme'); ?></p>
                <h2 class="bandabou-section-title bandabou-section-title-sm mt-2.5"><?php esc_html_e('Do you own a home in Bandabou?', 'contabai-bandabou-theme'); ?></h2>
                <p class="mt-3.5 text-[17px] leading-[1.6] text-[color:var(--content-text-color,#3A4A52)]"><?php esc_html_e('We take care of rentals and management: from the photos and the guests to cleaning and maintenance. You enjoy the income.', 'contabai-bandabou-theme'); ?></p>
            </div>
            <a href="#contact" class="bandabou-cta min-h-14 justify-center !rounded-[14px] px-7"><?php esc_html_e('Ask about the options', 'contabai-bandabou-theme'); ?></a>
        </div>
    </div>
</section>
