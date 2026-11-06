<?php

use BandabouTheme\Villas;

$photos = $listing['photos'] ?? [];
$lang = \Contabai\Helper::currentLang();

$amenityLabels = [];
foreach (($labels['amenities_catalogue'] ?? []) as $group) {
    if (is_array($group)) {
        $amenityLabels += $group;
    }
}
$amenityNames = array_map(function ($key) use ($amenityLabels) {
    return (string) ($amenityLabels[$key] ?? ucfirst(str_replace('_', ' ', (string) $key)));
}, (array) ($listing['amenities'] ?? []));

$reviews       = $listing['reviews'] ?? ['average' => null, 'count' => 0, 'recent' => []];
$reviewAverage = $reviews['average'] ?? null;
$reviewCount   = (int) ($reviews['count'] ?? 0);
$reviewRecent  = $reviews['recent'] ?? [];

$currency = $listing['currency'] ?? '';
$moneyLabel = function ($cents) use ($currency) {
    return ($currency ? $currency . ' ' : '') . number_format(((int) $cents) / 100, 2, '.', '');
};
$priceLabel = $moneyLabel($listing['from_price_cents'] ?? 0);

$formatHour = function ($hour) {
    return sprintf('%02d:00', (int) $hour);
};

$localized = function ($map) use ($lang) {
    return is_array($map) ? (string) ($map[$lang] ?? $map['en'] ?? '') : (string) ($map ?? '');
};
$description = $localized($listing['description'] ?? null);
$houseRules = $localized($listing['house_rules'] ?? null);
$cancellationPolicy = $localized($listing['cancellation_policy'] ?? null);

$tagline = '';
if (preg_match('~^\s*<p>\s*<strong>(.*?)</strong>\s*</p>~s', $description, $firstLine)) {
    $description = substr($description, strlen($firstLine[0]));
    $parts = preg_split('~\s+[–—-]\s+~u', wp_strip_all_tags($firstLine[1]), 2);
    $tagline = (string) end($parts);
}

$crumbs = [
    ['label' => __('Home', 'contabai'), 'url' => home_url()],
    ['label' => __('Villas', 'contabai-bandabou-theme'), 'url' => home_url('/' . CONTABAI_LISTINGS_PAGE_SLUG)],
    ['label' => $listing['title'] ?? '', 'url' => null],
];
$place = implode(' · ', array_filter([(string) ($listing['area_name'] ?? ''), (string) ($listing['region_name'] ?? '')]));

$guests    = (int) ($listing['max_guests'] ?? 0);
$bedrooms  = (int) ($listing['bedrooms'] ?? 0);
$bathrooms = (int) ($listing['bathrooms'] ?? 0);
$facts = array_filter([
    $guests ? ['users', sprintf(_n('%d guest', '%d guests', $guests, 'contabai'), $guests)] : null,
    $bedrooms ? ['home', sprintf(_n('%d bedroom', '%d bedrooms', $bedrooms, 'contabai'), $bedrooms)] : null,
    $bathrooms ? ['home-modern', sprintf(_n('%d bathroom', '%d bathrooms', $bathrooms, 'contabai'), $bathrooms)] : null,
    ! empty($listing['living_area_sqm']) ? ['squares-2x2', $listing['living_area_sqm'] . ' m²'] : null,
]);

$heroPhoto  = $photos[0] ?? [];
$heroImage  = (string) ($heroPhoto['hero_large'] ?? $heroPhoto['hero'] ?? $heroPhoto['medium'] ?? '');
$heroImage  = $heroImage !== '' ? $heroImage : \BandabouTheme\PhotoHero::image(0);
$heroSrcset = (! empty($heroPhoto['hero']) && ! empty($heroPhoto['hero_large'])) ? (! empty($heroPhoto['medium']) ? $heroPhoto['medium'] . ' 1280w, ' : '') . $heroPhoto['hero'] . ' 1920w, ' . $heroPhoto['hero_large'] . ' 2560w' : '';
$stripPhotos = array_slice($photos, 1, 4);

$contactPhone    = (string) get_option('bandabou_contact_phone', '+5999 699 6011');
$contactWhatsapp = (string) get_option('bandabou_cta_url', 'https://wa.me/59996996011');

$mapLat = $listing['latitude'] ?? null;
$mapLng = $listing['longitude'] ?? null;
$mapOn  = $mapLat !== null && $mapLng !== null && (int) get_option('contabai_map_enabled', 1) === 1 && function_exists('contabai_map_providers');
if ($mapOn) {
    $mapProviders = contabai_map_providers();
    $mapProvider  = $mapProviders[(string) get_option('contabai_map_provider', 'osm')] ?? $mapProviders['osm'];
    $mapKey       = trim((string) get_option('contabai_map_api_key', ''));
    if ($mapProvider['key'] && $mapKey === '') {
        $mapProvider = $mapProviders['osm'];
    }
}

$otherVillas = [];
foreach (Villas::random(4) as $villa) {
    if ((int) ($villa['id'] ?? 0) !== (int) ($listing['id'] ?? 0) && count($otherVillas) < 3) {
        $otherVillas[] = $villa;
    }
}

$reviewsListingId = (int) ($listing['id'] ?? 0);
$reviewsPerPage   = 5;
$reviewsEndpoint  = rest_url('contabai/v1/sanctum/listing/reviews/' . $reviewsListingId);

    $availability     = $listing['availability'] ?? [];
    $unavailableDates = $availability['unavailable_dates'] ?? [];
    $leadDays         = (int) ($availability['min_days_before_checkin'] ?? 0);
    $windowFrom       = $availability['window']['from'] ?? null;
    if ($leadDays > 0 && $windowFrom) {
        for ($day = 0; $day < $leadDays; $day++) {
            $unavailableDates[] = gmdate('Y-m-d', strtotime($windowFrom . ' +' . $day . ' days'));
        }
    }
    $occupiedNights   = $availability['unavailable_dates'] ?? [];
    $disabledSet      = array_flip($unavailableDates);
    $checkoutOnly     = [];
    foreach ($occupiedNights as $night) {
        $prev = gmdate('Y-m-d', strtotime($night . ' -1 day'));
        if ($windowFrom && $prev >= $windowFrom && ! isset($disabledSet[$prev])) {
            $checkoutOnly[] = $night;
        }
    }
    $unavailableDates = array_values(array_diff($unavailableDates, $checkoutOnly));
    $windowTo         = $availability['window']['to'] ?? null;
    $minNights        = (int) ($listing['min_nights'] ?? 1);
    $maxNights        = (int) ($listing['max_nights'] ?? 0);
    $rangeErrorMsg    = ($minNights && $maxNights)
        ? sprintf(__('Stay must be between %1$d and %2$d nights.', 'contabai'), $minNights, $maxNights)
        : '';
    $listingId     = (int) ($listing['id'] ?? 0);
    $isLoggedIn    = ! empty($_COOKIE['contabai_sanctum_session_token']);
    $quoteEndpoint = rest_url('contabai/v1/sanctum/listing/quote/' . $listingId);
    $loginUrl      = home_url('/' . CONTABAI_LOGIN_PAGE_SLUG);
    $bookUrl       = home_url('/' . CONTABAI_BOOK_PAGE_SLUG . '/');
    $listingCosts  = $listing['listing_costs'] ?? [];

$costList = '';
foreach ($listingCosts as $c) {
    $cLabel    = $c['label'] ?: ($labels['additional_cost_types'][$c['type_key']] ?? $c['type_key']);
    $isPercent = strpos((string) $c['billing_unit'], 'percent_') === 0;
    $rate      = $isPercent ? number_format(((int) $c['amount_cents']) / 100, 2, '.', '') . '%' : $moneyLabel($c['amount_cents']);
    $unit      = $labels['cost_billing_units'][$c['billing_unit']] ?? $c['billing_unit'];
    if (($c['type_key'] ?? '') === 'extra_person' && isset($listing['base_price_guests'])) {
        $cLabel .= ' (' . sprintf(_n('Above %d guest', 'Above %d guests', (int) $listing['base_price_guests'], 'contabai'), (int) $listing['base_price_guests']) . ')';
    } elseif (($c['type_key'] ?? '') === 'pet') {
        $cLabel .= ' (' . __('Only with pets', 'contabai') . ')';
    }
    $costList .= '<div class="flex justify-between gap-3"><dt class="min-w-0 text-[color:var(--content-text-color,#3A4A52)]">' . esc_html($cLabel) . '</dt><dd class="m-0 min-w-0 text-right font-semibold"><span class="whitespace-nowrap">' . esc_html($rate) . '</span> <span class="block text-sm font-normal text-[#5A5348]">' . esc_html($unit) . '</span></dd></div>';
}
$depositCents = (int) ($listing['deposit_cents'] ?? 0);
if ($depositCents > 0) {
    $costList .= '<div class="flex justify-between gap-3"><dt class="text-[color:var(--content-text-color,#3A4A52)]">' . esc_html__('Deposit, settled after your stay', 'contabai-bandabou-theme') . '</dt><dd class="m-0 whitespace-nowrap font-semibold">' . esc_html($moneyLabel($depositCents)) . '</dd></div>';
}
?>
<div x-data='{
        images: <?php echo esc_attr(wp_json_encode($photos)); ?>,
        lbOpen: false, lbIndex: 0,
        lbShow(i) { this.lbIndex = i; this.lbOpen = true; },
        lbNext() { this.lbIndex = (this.lbIndex + 1) % this.images.length; },
        lbPrev() { this.lbIndex = (this.lbIndex - 1 + this.images.length) % this.images.length; }
     }'>
    <section class="bandabou-villa-hero">
        <div class="bandabou-hero-media">
            <?php if ($heroImage !== '') : ?>
                <img src="<?php echo esc_url($heroImage); ?>"<?php if ($heroSrcset !== '') : ?> srcset="<?php echo esc_attr($heroSrcset); ?>" sizes="100vw"<?php endif; ?> alt="<?php echo esc_attr($heroPhoto['alt'] ?? ''); ?>" fetchpriority="high" decoding="async" class="bandabou-hero-slide is-active">
            <?php endif; ?>
            <div class="bandabou-hero-shade"></div>
            <div class="bandabou-hero-overlay"></div>
            <svg aria-hidden="true" viewBox="0 0 1440 110" preserveAspectRatio="none" width="100%" height="110" class="absolute inset-x-0 -bottom-px z-[2] block"><path d="M0 50 C 240 6 520 94 760 55 S 1200 11 1440 44 V110 H0 Z" fill="rgba(15,163,163,0.35)"></path><path d="M0 77 C 300 38 560 110 860 75 S 1260 46 1440 68 V110 H0 Z" fill="#FBF5EA"></path></svg>
        </div>
        <div class="bandabou-villa-hero-inner">
            <div class="max-w-[760px]">
                <?php echo \Contabai\View::render('components.breadcrumb', ['crumbs' => $crumbs]); ?>
                <?php if ($place !== '') : ?>
                    <p class="bandabou-villa-place"><?php echo \Contabai\Heroicon::outline('map-pin', 'w-[17px] h-[17px]'); ?><?php echo esc_html($place); ?></p>
                <?php endif; ?>
                <h1 class="bandabou-villa-title"><?php echo esc_html($listing['title'] ?? ''); ?></h1>
                <?php if ($tagline !== '') : ?>
                    <p class="bandabou-villa-tagline"><?php echo esc_html($tagline); ?></p>
                <?php endif; ?>
                <?php if ($reviewCount > 0) : ?>
                    <a href="#reviews" class="mt-3 inline-flex items-center gap-2 text-sm text-white no-underline hover:underline">
                        <?php $sr_rating = $reviewAverage; $sr_size = 'w-4 h-4'; include \Contabai\View::path('components.star-rating'); ?>
                        <span><span class="font-semibold"><?php echo esc_html(number_format((float) $reviewAverage, 1)); ?></span> · <?php echo esc_html(sprintf(_n('%d review', '%d reviews', $reviewCount, 'contabai'), $reviewCount)); ?></span>
                    </a>
                <?php endif; ?>
            </div>
            <div class="bandabou-villa-pricebox">
                <p class="m-0 whitespace-nowrap text-[15px]"><?php echo esc_html__('from', 'contabai'); ?> <strong class="contabai-display text-[34px] leading-none lg:text-[38px]"><?php echo esc_html($priceLabel); ?></strong> <?php echo esc_html__('/ night', 'contabai'); ?></p>
                <a href="#beschikbaarheid" class="bandabou-cta min-h-[52px] justify-center !rounded-[14px] px-6 text-base"><?php esc_html_e('Check availability', 'contabai-bandabou-theme'); ?></a>
            </div>
        </div>
    </section>

    <?php if ($stripPhotos !== []) : ?>
        <div class="bandabou-container">
            <div class="bandabou-photo-strip">
                <?php foreach ($stripPhotos as $i => $p) : ?>
                    <button type="button" x-on:click="lbShow(<?php echo (int) $i + 1; ?>)" class="bandabou-photo-strip-item">
                        <img src="<?php echo esc_url($p['card'] ?? $p['medium'] ?? ''); ?>" alt="<?php echo esc_attr($p['alt'] ?? ''); ?>" loading="lazy" decoding="async" class="block h-full w-full object-cover">
                    </button>
                <?php endforeach; ?>
                <button type="button" x-on:click="lbShow(0)" class="bandabou-photo-strip-all">
                    <?php echo \Contabai\Heroicon::solid('squares-2x2', 'w-4 h-4'); ?>
                    <?php echo esc_html(sprintf(__('All %d photos', 'contabai-bandabou-theme'), count($photos))); ?>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <!-- lightbox (teleported to body) -->
    <template x-teleport="body">
        <div x-show="lbOpen" x-cloak x-on:click="lbOpen = false"
             x-on:keydown.escape.window="lbOpen = false"
             x-on:keydown.arrow-right.window="if (lbOpen) lbNext()"
             x-on:keydown.arrow-left.window="if (lbOpen) lbPrev()"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[99] flex select-none items-center justify-center bg-black/80">
            <div class="relative inline-flex" x-on:click.stop>
                <img x-bind:src="lbOpen && images[lbIndex] ? (images[lbIndex].hero_large || images[lbIndex].hero || images[lbIndex].medium) : ''" x-bind:alt="images[lbIndex] ? images[lbIndex].alt : ''" class="max-h-[90vh] max-w-[92vw] rounded-2xl object-contain">
                <button type="button" x-show="images.length > 1" x-on:click.stop="lbPrev()" aria-label="<?php esc_attr_e('Previous', 'contabai'); ?>"
                        class="absolute left-3 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-black/60 text-white transition hover:bg-black/60"><?php echo \Contabai\Heroicon::outline('chevron-left', 'w-4 h-4'); ?></button>
                <button type="button" x-show="images.length > 1" x-on:click.stop="lbNext()" aria-label="<?php esc_attr_e('Next', 'contabai'); ?>"
                        class="absolute right-3 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-black/60 text-white transition hover:bg-black/60"><?php echo \Contabai\Heroicon::outline('chevron-right', 'w-4 h-4'); ?></button>
            </div>
            <button type="button" x-on:click="lbOpen = false" aria-label="<?php esc_attr_e('Close', 'contabai'); ?>"
                    class="absolute right-4 top-4 flex h-11 w-11 items-center justify-center rounded-full bg-black/60 text-white transition hover:bg-black/60"><?php echo \Contabai\Heroicon::outline('x-mark', 'w-4 h-4'); ?></button>
        </div>
    </template>
</div>

<div class="bandabou-container">
    <div x-data="contabaiAvailability()" class="mt-8 grid grid-cols-1 items-start gap-10 lg:mt-12 lg:grid-cols-[minmax(0,1fr)_380px] lg:gap-14">
        <div class="min-w-0">
            <?php if ($facts !== []) : ?>
                <ul class="m-0 flex list-none flex-wrap gap-3 border-b border-[#EADFCB] p-0 pb-7">
                    <?php foreach ($facts as $fact) : ?>
                        <li class="bandabou-fact"><?php echo \Contabai\Heroicon::outline($fact[0], 'w-[19px] h-[19px]'); ?><?php echo esc_html($fact[1]); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if ($description !== '') : ?>
                <section class="bandabou-villa-section" x-data="{ more: false }">
                    <div class="bandabou-description" x-bind:class="more ? 'is-open' : ''"><?php echo wp_kses_post($description); ?></div>
                    <button type="button" x-on:click="more = ! more" class="mt-4 inline-flex min-h-11 items-center gap-1.5 text-[15px] font-bold text-[color:var(--content-link-color,#0B6E6C)]">
                        <span x-text="more ? '<?php echo esc_js(__('Show less', 'contabai-bandabou-theme')); ?>' : '<?php echo esc_js(__('Read the full description', 'contabai-bandabou-theme')); ?>'"></span>
                        <span class="transition" x-bind:class="more ? 'rotate-180' : ''"><?php echo \Contabai\Heroicon::outline('chevron-down', 'w-[17px] h-[17px]'); ?></span>
                    </button>
                </section>
            <?php endif; ?>

            <?php if ($amenityNames !== []) : ?>
                <section class="bandabou-villa-section" x-data="{ all: false }">
                    <h2 class="bandabou-villa-h2"><?php esc_html_e('What this villa offers', 'contabai-bandabou-theme'); ?></h2>
                    <ul class="mt-[22px] grid list-none grid-cols-1 gap-x-6 gap-y-4 p-0 text-base md:grid-cols-2 xl:grid-cols-3">
                        <?php foreach ($amenityNames as $i => $amenityName) : ?>
                            <li class="flex items-center gap-2.5"<?php echo $i >= 18 ? ' x-show="all" x-cloak' : ''; ?>><span class="flex-none text-[#0FA3A3]"><?php echo \Contabai\Heroicon::outline('check', 'w-[19px] h-[19px]'); ?></span><?php echo esc_html($amenityName); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if (count($amenityNames) > 18) : ?>
                        <button type="button" x-show="! all" x-on:click="all = true" class="bandabou-outline-button mt-6"><?php echo esc_html(sprintf(__('Show all %d amenities', 'contabai-bandabou-theme'), count($amenityNames))); ?></button>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <section id="beschikbaarheid" class="bandabou-villa-section scroll-mt-24">
                <h2 class="bandabou-villa-h2"><?php echo esc_html__('Availability', 'contabai'); ?></h2>
                <div class="mt-[22px] rounded-[22px] border border-[#EADFCB] bg-white p-4 sm:p-5">
                    <div x-ref="cal"></div>
                </div>
                <div x-show="nights" x-cloak
                     x-effect="document.body.style.paddingBottom = (nights && ! window.matchMedia('(min-width: 1024px)').matches) ? '104px' : ''"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
                     class="fixed inset-x-0 bottom-0 z-40 border-t border-[#EADFCB] bg-white lg:hidden">
                    <div class="bandabou-container flex items-center justify-between gap-4 py-3">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-medium" x-text="rangeLabel()"></div>
                            <div class="text-xl font-bold text-[color:var(--heading-color,#0B4F6C)]" x-text="quote ? money(quote.accommodation_cents) : '…'"></div>
                            <?php if (! empty($listingCosts)) : ?>
                                <div class="text-xs text-[#5A5348]"><?php echo esc_html__('Additional costs calculated at booking', 'contabai'); ?></div>
                            <?php endif; ?>
                        </div>
                        <button type="button" x-on:click="startBooking()" class="bandabou-cta min-h-12 flex-none !rounded-[14px] px-5 text-[15px]">
                            <span x-show="isLoggedIn"><?php esc_html_e('Book', 'contabai-bandabou-theme'); ?></span>
                            <span x-show="! isLoggedIn"><?php echo esc_html__('Log in to book', 'contabai'); ?></span>
                        </button>
                    </div>
                </div>
            </section>

            <section class="bandabou-villa-section">
                <h2 class="bandabou-villa-h2"><?php esc_html_e('Good to know', 'contabai-bandabou-theme'); ?></h2>
                <dl class="mt-[22px] grid grid-cols-2 gap-3 md:grid-cols-3 md:gap-3.5">
                    <div class="bandabou-know"><dt><?php esc_html_e('Check-in', 'contabai-bandabou-theme'); ?></dt><dd><?php echo esc_html(sprintf(__('from %s', 'contabai-bandabou-theme'), $formatHour($listing['checkin_from_hour'] ?? 0))); ?></dd></div>
                    <div class="bandabou-know"><dt><?php esc_html_e('Check-out', 'contabai-bandabou-theme'); ?></dt><dd><?php echo esc_html(sprintf(__('until %s', 'contabai-bandabou-theme'), $formatHour($listing['checkout_by_hour'] ?? 0))); ?></dd></div>
                    <?php if (! empty($listing['min_nights']) || ! empty($listing['max_nights'])) : ?>
                        <div class="bandabou-know"><dt><?php echo esc_html__('Length of stay', 'contabai'); ?></dt><dd><?php echo esc_html(sprintf(__('%1$s–%2$s nights', 'contabai'), (int) ($listing['min_nights'] ?? 0), (int) ($listing['max_nights'] ?? 0))); ?></dd></div>
                    <?php endif; ?>
                    <?php if ($depositCents > 0) : ?>
                        <div class="bandabou-know"><dt><?php esc_html_e('Deposit', 'contabai-bandabou-theme'); ?></dt><dd><?php echo esc_html($moneyLabel($depositCents)); ?></dd></div>
                    <?php endif; ?>
                    <?php foreach (($listing['utility_rates'] ?? []) as $u) : ?>
                        <div class="bandabou-know"><dt><?php echo esc_html($labels['utilities'][$u['utility']] ?? ucfirst((string) $u['utility'])); ?></dt><dd><?php echo esc_html($moneyLabel($u['rate_cents']) . ' / ' . $u['unit']); ?></dd></div>
                    <?php endforeach; ?>
                </dl>
                <?php if (! empty($listing['utility_rates'])) : ?>
                    <p class="mt-4 text-[15px] leading-[1.6] text-[#5A5348]"><?php echo esc_html__('Read at check-out and settled from your deposit.', 'contabai'); ?></p>
                <?php endif; ?>
                <div class="mt-5 space-y-3" x-data="{ open: '', toggle(k) { this.open = this.open === k ? '' : k } }">
                    <?php
                    if ($houseRules !== '') {
                        $acc_key   = 'house_rules';
                        $acc_title = __('House rules', 'contabai');
                        $acc_body  = '<div class="prose prose-sm max-w-none text-neutral-700">' . wp_kses_post($houseRules) . '</div>';
                        include \Contabai\View::path('components.accordion-section');
                    }
                    if ($cancellationPolicy !== '') {
                        $acc_key   = 'cancellation_policy';
                        $acc_title = __('Cancellation policy', 'contabai');
                        $acc_body  = '<div class="prose prose-sm max-w-none text-neutral-700">' . wp_kses_post($cancellationPolicy) . '</div>';
                        include \Contabai\View::path('components.accordion-section');
                    }
                    if (! empty($listing['video_url'])) {
                        $acc_key   = 'video';
                        $acc_title = __('Video', 'contabai');
                        ob_start(); ?>
                        <div class="aspect-video overflow-hidden rounded-xl bg-black">
                            <iframe data-src="<?php echo esc_url($listing['video_url']); ?>" x-bind:src="open === 'video' ? $el.dataset.src : null" class="h-full w-full" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                        <?php
                        $acc_body = ob_get_clean();
                        include \Contabai\View::path('components.accordion-section');
                    }
                    ?>
                </div>
            </section>

            <?php if ($reviewCount > 0) : ?>
                <section id="reviews" class="bandabou-villa-section scroll-mt-24">
                    <h2 class="bandabou-villa-h2"><?php echo esc_html__('Reviews', 'contabai'); ?></h2>
                    <div class="mt-[22px]">
                        <div x-data="contabaiReviews()">
                            <?php if ($reviewRecent): ?>
                            <ul x-show="! reviews.length" class="flex flex-col gap-5 !m-0 !list-none !p-0">
                                <?php foreach ($reviewRecent as $review): ?>
                                    <li>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-semibold text-neutral-900"><?php echo esc_html((string) ($review['reviewer']['nickname'] ?? '')); ?></span>
                                            <span class="inline-flex items-center gap-0.5" role="img">
                                                <?php for ($star = 1; $star <= 5; $star++): ?>
                                                    <span class="<?php echo $star <= (int) ($review['rating'] ?? 0) ? 'text-amber-400' : 'text-neutral-300'; ?>"><?php echo \Contabai\Heroicon::solid('star', 'w-4 h-4'); ?></span>
                                                <?php endfor; ?>
                                            </span>
                                        </div>
                                        <div class="text-xs text-neutral-400" x-text="fmtDate('<?php echo esc_attr((string) $review['created_at']); ?>')"><?php echo esc_html(wp_date(get_option('date_format'), strtotime((string) $review['created_at']))); ?></div>
                                        <?php if (! empty($review['body'])): ?>
                                            <p class="mt-1 text-sm text-neutral-700"><?php echo esc_html((string) $review['body']); ?></p>
                                        <?php endif; ?>
                                        <?php if (! empty($review['reply'])): ?>
                                            <div class="mt-3 ml-4 rounded-xl border-l-2 border-neutral-200 bg-neutral-50 px-4 py-3">
                                                <div class="font-semibold text-neutral-900"><?php echo esc_html__('Reply from the host', 'contabai'); ?></div>
                                                <div class="text-xs text-neutral-400" x-text="fmtDate('<?php echo esc_attr((string) $review['replied_at']); ?>')"><?php echo esc_html(wp_date(get_option('date_format'), strtotime((string) $review['replied_at']))); ?></div>
                                                <p class="mt-1 text-sm text-neutral-700 whitespace-pre-line"><?php echo esc_html((string) $review['reply']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php else: ?>
                            <div x-show="loading && reviews.length === 0" class="py-6 text-center text-sm text-neutral-400"><?php echo esc_html__('Loading…', 'contabai'); ?></div>
                            <?php endif; ?>
                            <ul x-show="reviews.length > 0" class="flex flex-col gap-5 !m-0 !list-none !p-0">
                                <template x-for="r in reviews" x-bind:key="r.id">
                                    <li>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="font-semibold text-neutral-900" x-text="r.reviewer.nickname"></span>
                                            <span class="inline-flex items-center gap-0.5" role="img">
                                                <template x-for="s in 5" x-bind:key="s">
                                                    <span x-bind:class="s <= r.rating ? 'text-amber-400' : 'text-neutral-300'"><?php echo \Contabai\Heroicon::solid('star', 'w-4 h-4'); ?></span>
                                                </template>
                                            </span>
                                        </div>
                                        <div class="text-xs text-neutral-400" x-text="fmtDate(r.created_at)"></div>
                                        <p class="mt-1 text-sm text-neutral-700" x-show="r.body" x-text="r.body"></p>
                                        <div class="mt-3 ml-4 rounded-xl border-l-2 border-neutral-200 bg-neutral-50 px-4 py-3" x-show="r.reply">
                                            <div class="font-semibold text-neutral-900"><?php echo esc_html__('Reply from the host', 'contabai'); ?></div>
                                            <div class="text-xs text-neutral-400" x-text="fmtDate(r.replied_at)"></div>
                                            <p class="mt-1 text-sm text-neutral-700 whitespace-pre-line" x-text="r.reply"></p>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                            <?php $pg_neutral = true; include \Contabai\View::path('components.pagination'); ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($mapOn) : ?>
                <section class="bandabou-villa-section !border-b-0 !pb-0">
                    <h2 class="bandabou-villa-h2"><?php esc_html_e('Location', 'contabai-bandabou-theme'); ?></h2>
                    <p class="mt-2.5 text-[17px] leading-[1.7] text-[color:var(--content-text-color,#3A4A52)]"><?php echo esc_html(implode(', ', array_filter([(string) ($listing['address'] ?? ''), (string) ($listing['region_name'] ?? ''), (string) ($listing['country_name'] ?? '')]))); ?></p>
                    <div id="bandabou-villa-map" class="bandabou-villa-map" role="img" aria-label="<?php echo esc_attr(sprintf(__('Map: %s', 'contabai-bandabou-theme'), (string) ($listing['title'] ?? ''))); ?>"></div>
                </section>
            <?php endif; ?>
        </div>

        <aside id="boeken" class="min-w-0 scroll-mt-24 lg:sticky lg:top-24">
            <div class="bandabou-book">
                <p class="m-0 text-[15px] text-[#5A5348]"><?php echo esc_html__('from', 'contabai'); ?> <strong class="contabai-display text-[36px] text-[color:var(--heading-color,#0B4F6C)]"><?php echo esc_html($priceLabel); ?></strong> <?php echo esc_html__('/ night', 'contabai'); ?></p>
                <button type="button" x-on:click="$refs.cal.scrollIntoView({ behavior: 'smooth', block: 'center' })" class="bandabou-book-dates">
                    <span><span class="bandabou-search-label"><?php esc_html_e('Arrival', 'contabai-bandabou-theme'); ?></span><span class="bandabou-search-value" x-bind:class="arrival ? '' : 'is-empty'" x-text="arrival ? fmt(arrival) : '<?php echo esc_js(__('Pick a date', 'contabai-bandabou-theme')); ?>'"></span></span>
                    <span><span class="bandabou-search-label"><?php esc_html_e('Departure', 'contabai-bandabou-theme'); ?></span><span class="bandabou-search-value" x-bind:class="departure ? '' : 'is-empty'" x-text="departure ? fmt(departure) : '<?php echo esc_js(__('Pick a date', 'contabai-bandabou-theme')); ?>'"></span></span>
                </button>
                <button type="button" x-show="! nights" x-on:click="$refs.cal.scrollIntoView({ behavior: 'smooth', block: 'center' })" class="bandabou-book-submit"><?php esc_html_e('Check availability', 'contabai-bandabou-theme'); ?></button>
                <button type="button" x-show="nights" x-cloak x-on:click="startBooking()" class="bandabou-book-submit">
                    <span x-show="isLoggedIn"><?php esc_html_e('Book', 'contabai-bandabou-theme'); ?></span>
                    <span x-show="! isLoggedIn"><?php echo esc_html__('Log in to book', 'contabai'); ?></span>
                </button>
                <template x-if="! isLoggedIn">
                    <p class="mt-3 flex items-start gap-1 text-xs text-[#5A5348]"><?php echo \Contabai\Heroicon::outline('lock-closed', 'w-4 h-4 shrink-0'); ?> <?php echo esc_html__('Booking requires a verified account', 'contabai'); ?></p>
                </template>
                <dl class="m-0 mt-[18px] flex flex-col gap-2.5 border-t border-[#F0E6D5] pt-[18px] text-[15px]">
                    <div x-show="nights" x-cloak class="flex justify-between gap-3">
                        <dt class="text-[color:var(--content-text-color,#3A4A52)]" x-text="nights + ' ' + (nights === 1 ? nightLabel : nightsLabel)"></dt>
                        <dd class="m-0 font-semibold" x-text="quote ? money(quote.accommodation_cents) : '…'"></dd>
                    </div>
                    <?php echo $costList; ?>
                </dl>
                <p class="mt-[18px] text-sm leading-[1.55] text-[#5A5348]"><?php esc_html_e('You book directly with Bandabou Curaçao. After your request you will receive the confirmation and payment details from us.', 'contabai-bandabou-theme'); ?></p>
                <?php if ($contactPhone !== '') : ?>
                    <a href="<?php echo esc_url($contactWhatsapp); ?>" class="bandabou-outline-button mt-[18px] w-full justify-center gap-2.5 text-center no-underline"><?php echo \BandabouTheme\Heroicon::render('whatsapp', 'brands', 'w-[19px] h-[19px]'); ?><?php esc_html_e('Question? WhatsApp us', 'contabai-bandabou-theme'); ?></a>
                <?php endif; ?>
            </div>
            <div class="mt-[18px] flex items-center gap-3.5 rounded-[22px] border border-[#E6D6B8] bg-bandabou-sand p-[22px]">
                <img src="<?php echo esc_url(get_theme_file_uri('assets/img/logo.webp')); ?>" alt="" loading="lazy" class="h-auto w-[58px] flex-none">
                <p class="m-0 text-[15px] leading-[1.5] text-[color:var(--content-text-color,#3A4A52)]"><?php esc_html_e('On arrival you are welcomed in person by our local team in Bandabou.', 'contabai-bandabou-theme'); ?></p>
            </div>
        </aside>
    </div>
</div>

<?php if ($otherVillas !== []) : ?>
    <section class="mt-16 bg-bandabou-sand pb-16 pt-14 lg:mt-[88px] lg:pb-24 lg:pt-20">
        <div class="bandabou-container">
            <h2 class="bandabou-section-title !text-[36px] lg:!text-[44px]"><?php esc_html_e('See our other villas too', 'contabai-bandabou-theme'); ?></h2>
            <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-3">
                <?php foreach ($otherVillas as $villa) :
                    $villaGuests = (int) ($villa['max_guests'] ?? 0);
                    $villaBedrooms = (int) ($villa['bedrooms'] ?? 0);
                    ?>
                    <a href="<?php echo esc_url(\Contabai\Controllers\ListingRewriteController::listing_url($villa)); ?>" class="flex flex-col overflow-hidden rounded-[20px] bg-white text-inherit no-underline">
                        <img src="<?php echo esc_url($villa['photos'][0]['card'] ?? ''); ?>" alt="<?php echo esc_attr($villa['photos'][0]['alt'] ?? ''); ?>" loading="lazy" decoding="async" class="block aspect-[16/10] w-full object-cover">
                        <span class="flex flex-wrap items-baseline justify-between gap-2.5 px-5 pb-[22px] pt-[18px]">
                            <span>
                                <span class="contabai-display block text-[25px] font-bold text-[color:var(--heading-color,#0B4F6C)]"><?php echo esc_html($villa['title'] ?? ''); ?></span>
                                <span class="mt-0.5 block text-sm text-[#5A5348]"><?php echo esc_html(sprintf(_n('%d guest', '%d guests', $villaGuests, 'contabai'), $villaGuests) . ' · ' . sprintf(_n('%d bedroom', '%d bedrooms', $villaBedrooms, 'contabai'), $villaBedrooms)); ?></span>
                            </span>
                            <span class="whitespace-nowrap text-sm text-[#5A5348]"><?php echo esc_html__('from', 'contabai'); ?> <strong class="text-[19px] text-[color:var(--heading-color,#0B4F6C)]"><?php echo esc_html($moneyLabel($villa['from_price_cents'] ?? 0)); ?></strong></span>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

    <script>
    document.addEventListener('alpine:init', function () {
        Alpine.data('contabaiReviews', function () {
            return {
                loading: false,
                reviews: [],
                currentPage: 1,
                lastPage: <?php echo $reviewRecent ? (int) ceil($reviewCount / $reviewsPerPage) : 1; ?>,
                total: <?php echo $reviewRecent ? $reviewCount : 0; ?>,
                perPage: <?php echo (int) $reviewsPerPage; ?>,
                endpoint: '<?php echo esc_js($reviewsEndpoint); ?>',
                locale: '<?php echo esc_js(\Contabai\Helper::currentLang()); ?>',
                labels: { loadError: '<?php echo esc_js(__('Could not load the reviews.', 'contabai')); ?>' },
                init: function () { if (! this.total) { this.load(1); } },
                load: function (page) {
                    let self = this;
                    self.loading = true;
                    let pageNumber = page || self.currentPage || 1;
                    return fetch(self.endpoint + '?page=' + pageNumber + '&per_page=' + self.perPage, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
                        .then(function (r) { if (! r.ok) { let e = new Error('http'); e.status = r.status; throw e; } return r.json(); })
                        .then(function (res) {
                            let data = (res && res.data) || {};
                            self.reviews = data.reviews || [];
                            self.currentPage = data.current_page || 1;
                            self.lastPage = data.last_page || 1;
                            self.total = data.total || 0;
                            self.loading = false;
                        })
                        .catch(function (e) { self.loading = false; if (window.contabaiToast) { contabaiToast(contabaiStatusText(e.status, self.labels.loadError), 'danger'); } });
                },
                goTo: function (page) {
                    if (page < 1 || page > this.lastPage || this.loading) { return; }
                    this.load(page);
                    document.getElementById('reviews').scrollIntoView({ behavior: 'smooth', block: 'start' });
                },
                pages: function () {
                    let last = this.lastPage, cur = this.currentPage, delta = 1, range = [];
                    for (let pageNumber = 1; pageNumber <= last; pageNumber++) {
                        if (pageNumber === 1 || pageNumber === last || (pageNumber >= cur - delta && pageNumber <= cur + delta)) { range.push(pageNumber); }
                    }
                    let out = [], prev = 0;
                    range.forEach(function (pageNumber) {
                        if (pageNumber - prev > 1) { out.push({ ellipsis: true, key: 'e' + pageNumber }); }
                        out.push({ n: pageNumber, ellipsis: false, key: 'p' + pageNumber });
                        prev = pageNumber;
                    });
                    return out;
                },
                fmtDate: function (iso) {
                    if (! iso) { return ''; }
                    try { return new Date(iso).toLocaleDateString(this.locale, { year: 'numeric', month: 'long', day: 'numeric' }); } catch (e) { return ''; }
                },
            };
        });
        Alpine.data('contabaiAvailability', function () {
            return {
                calendar: null,
                cols: 0,
                disabledDates: <?php echo wp_json_encode($unavailableDates); ?>,   // occupied nights (blocks ∪ bookings, prep-buffer baked in) + arrival lead-time days — from the availability payload
                occupied: <?php echo wp_json_encode((object) array_fill_keys($occupiedNights, true)); ?>,
                checkoutOnly: <?php echo wp_json_encode((object) array_fill_keys($checkoutOnly, true)); ?>,
                checkoutOnlyLabel: '<?php echo esc_js(__('Check-out only', 'contabai')); ?>',
                checkoutOnlyErr: '<?php echo esc_js(__('You can check out on this day, but not arrive. Pick an earlier arrival date.', 'contabai')); ?>',
                occupiedErr: '<?php echo esc_js(__('Some nights in these dates are already booked. Please pick other dates.', 'contabai')); ?>',
                windowFrom: <?php echo wp_json_encode($windowFrom); ?>,             // Laravel's today (UTC) — first bookable day
                windowMax: <?php echo wp_json_encode($windowTo); ?>,               // booking horizon (today + booking_window_months), or null
                minNights: <?php echo $minNights; ?>,
                maxNights: <?php echo $maxNights; ?>,                               // 0 = no max
                nightsLabel: '<?php echo esc_js(__('nights', 'contabai')); ?>',
                nightLabel: '<?php echo esc_js(__('night', 'contabai')); ?>',
                rangeErr: '<?php echo esc_js($rangeErrorMsg); ?>',
                quoteErr: '<?php echo esc_js(__('We could not get a price for these dates. Please try other dates.', 'contabai')); ?>',
                locale: '<?php echo esc_js(\Contabai\Helper::currentLang()); ?>',   // guest language → localized month/weekday names
                // selected range (validate-on-complete)
                arrival: '',
                departure: '',
                nights: 0,
                isLoggedIn: <?php echo $isLoggedIn ? 'true' : 'false'; ?>,
                quoteEndpoint: '<?php echo esc_js($quoteEndpoint); ?>',
                loginUrl: '<?php echo esc_js($loginUrl); ?>',
                bookUrl: '<?php echo esc_js($bookUrl); ?>',
                listingId: <?php echo $listingId; ?>,
                currency: '<?php echo esc_js($currency); ?>',
                quote: null,
                loadingQuote: false,
                showDetails: false,   // top-of-bar "See details" cost-breakdown expander
                // 1 month on mobile, 2 from md (≥768)
                monthsForWidth: function () {
                    let width = window.innerWidth;
                    if (width >= 768) return 2;
                    return 1;
                },
                // type 'default' only allows 1 month; 'multiple' is required for 2–3.
                // selectedTheme pinned 'light' so the lib never follows the OS to dark (plugin has no dark mode).
                options: function (months) {
                    let component = this;
                    let calendarOptions = {
                        type: months === 1 ? 'default' : 'multiple',
                        displayMonthsCount: months,
                        monthsToSwitch: 1,
                        displayDatesOutside: false,   // don't render adjacent-month dates — else a date shows in two grids and both highlight on select
                        disableDatesPast: true,
                        disableDates: this.disabledDates,        // grey out occupied nights (non-selectable)
                        disableDatesGaps: true,                  // block a range spanning an occupied night
                        selectionDatesMode: 'multiple-ranged',   // pick arrival → departure
                        locale: this.locale,                     // localized months/weekdays
                        selectedTheme: 'light',
                        onClickDate: function (cal) { component.handleSelect(cal); },
                        onCreateDateEls: function (cal, dateEl) {
                            if (! component.checkoutOnly[dateEl.getAttribute('data-vc-date')]) return;
                            dateEl.setAttribute('data-contabai-checkout-only', '');
                            let btn = dateEl.querySelector('[data-vc-date-btn]');
                            if (btn) { btn.setAttribute('title', component.checkoutOnlyLabel); }
                        }
                    };
                    // v3.4+ requires the months extension for multi-month display; register it always (extensions are constructor-only, so a later 1→2 month resize can't add it).
                    if (window.VanillaCalendarMonths) { calendarOptions.extensions = [window.VanillaCalendarMonths]; }
                    if (this.windowFrom) calendarOptions.dateToday = this.windowFrom;   // "today" = Laravel's UTC today, not the visitor's clock
                    if (this.windowMax) calendarOptions.displayDateMax = this.windowMax;   // clamp navigation to the booking horizon
                    return calendarOptions;
                },
                // Validate-on-complete: VCP has no native min/max stay-length rule, so enforce it here.
                // occupied-span is already prevented by disableDatesGaps; here we only guard nights range.
                handleSelect: function (cal) {
                    let dates = (cal.context && cal.context.selectedDates) || [];   // sorted YYYY-MM-DD[], first=arrival last=departure
                    if (dates.length < 2) {   // arrival picked (or cleared) — awaiting departure
                        if (dates[0] && this.occupied[dates[0]]) {
                            this.rejectRange(cal, this.checkoutOnlyErr);
                            return;
                        }
                        this.arrival = dates[0] || ''; this.departure = ''; this.nights = 0;
                        this.quote = null;
                        return;
                    }
                    let firstDate = dates[0], lastDate = dates[dates.length - 1];
                    let nights = Math.round((new Date(lastDate) - new Date(firstDate)) / 86400000);
                    if ((this.minNights && nights < this.minNights) || (this.maxNights && nights > this.maxNights)) {
                        this.rejectRange(cal, this.rangeErr);
                        return;
                    }
                    if (this.hasOccupiedNight(firstDate, lastDate)) {
                        this.rejectRange(cal, this.occupiedErr);
                        return;
                    }
                    this.arrival = firstDate; this.departure = lastDate; this.nights = nights;
                    this.fetchQuote();   // public quote — real price for everyone
                },
                rejectRange: function (cal, message) {
                    this.arrival = ''; this.departure = ''; this.nights = 0;
                    this.quote = null;
                    cal.set({ selectedDates: [] });
                    if (window.contabaiToast && message) contabaiToast(message, 'warning');
                },
                hasOccupiedNight: function (firstDate, lastDate) {
                    let parts = firstDate.split('-').map(Number);
                    let cursor = new Date(Date.UTC(parts[0], parts[1] - 1, parts[2]));
                    let night = firstDate;
                    while (night < lastDate) {
                        if (this.occupied[night]) return true;
                        cursor.setUTCDate(cursor.getUTCDate() + 1);
                        night = cursor.toISOString().slice(0, 10);
                    }
                    return false;
                },
                fmt: function (d) {
                    try { return new Date(d + 'T00:00:00').toLocaleDateString(this.locale, { weekday: 'short', day: 'numeric', month: 'short' }); }
                    catch (e) { return d; }
                },
                rangeLabel: function () {
                    return this.nights ? (this.fmt(this.arrival) + ' → ' + this.fmt(this.departure) + ' · ' + this.nights + ' ' + (this.nights === 1 ? this.nightLabel : this.nightsLabel)) : '';
                },
                money: function (cents) {
                    if (cents === null || cents === undefined) return '';
                    return (this.currency || 'EUR') + ' ' + (cents / 100).toFixed(2);
                },
                fetchQuote: function () {   // public base-only quote — accommodation + deposit, no party
                    let self = this;
                    let arrival = self.arrival, departure = self.departure;
                    self.loadingQuote = true; self.quote = null;
                    fetch(self.quoteEndpoint, {
                        method: 'POST', credentials: 'same-origin',
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ arrival: arrival, departure: departure })
                    })
                        .then(function (r) { return r.json().then(function (res) { return { ok: r.ok, status: r.status, res: res }; }); })
                        .then(function (out) {
                            if (self.arrival !== arrival || self.departure !== departure) return;
                            self.loadingQuote = false;
                            if (out.ok) { self.quote = (out.res && out.res.data && out.res.data.quote) || null; return; }
                            self.quoteFailed(out.status);
                        })
                        .catch(function () {
                            if (self.arrival !== arrival || self.departure !== departure) return;
                            self.loadingQuote = false;
                            self.quoteFailed(0);
                        });
                },
                quoteFailed: function (status) {
                    this.arrival = ''; this.departure = ''; this.nights = 0;
                    this.quote = null;
                    if (this.calendar) { this.calendar.set({ selectedDates: [] }); }
                    if (window.contabaiToast) { contabaiToast(contabaiStatusText(status, this.quoteErr), 'danger'); }
                },
                startBooking: function () {
                    if (! this.isLoggedIn) { window.location.href = this.loginUrl; return; }
                    if (! this.nights) { return; }
                    window.location.href = this.bookUrl
                        + '?listing=' + encodeURIComponent(this.listingId)
                        + '&arrival=' + encodeURIComponent(this.arrival)
                        + '&departure=' + encodeURIComponent(this.departure);
                },
                init: function () {
                    let self = this;
                    if (! window.VanillaCalendar) { setTimeout(function () { self.init(); }, 50); return; }
                    self.cols = self.monthsForWidth();
                    self.calendar = new window.VanillaCalendar(self.$refs.cal, self.options(self.cols));
                    self.calendar.init();
                    window.addEventListener('resize', function () {
                        let months = self.monthsForWidth();
                        if (months !== self.cols) { self.cols = months; self.calendar.set(self.options(months)); }
                    });
                }
            };
        });
    });
    </script>

<?php if ($mapOn) : ?>
<script>
(function () {
    const box = document.getElementById('bandabou-villa-map');
    if (! box) { return; }
    const start = function () {
        const css = document.createElement('link');
        css.rel = 'stylesheet';
        css.href = <?php echo wp_json_encode(CONTABAI_PLUGIN_URL . 'assets/css/leaflet.css?ver=1.9.4'); ?>;
        document.head.appendChild(css);
        const js = document.createElement('script');
        js.src = <?php echo wp_json_encode(CONTABAI_PLUGIN_URL . 'assets/js/leaflet/leaflet.js?ver=1.9.4'); ?>;
        js.onload = function () {
            const point = [<?php echo (float) $mapLat; ?>, <?php echo (float) $mapLng; ?>];
            const map = L.map(box, { scrollWheelZoom: false, attributionControl: false }).setView(point, 14);
            L.tileLayer(<?php echo wp_json_encode(str_replace('{key}', rawurlencode($mapKey), $mapProvider['url'])); ?>, <?php echo wp_json_encode($mapProvider['options']); ?>).addTo(map);
            L.control.attribution({ prefix: false }).addAttribution(<?php echo wp_json_encode($mapProvider['attribution']); ?>).addTo(map);
            const pin = document.createElement('span');
            pin.className = 'bandabou-map-pin';
            pin.textContent = <?php echo wp_json_encode((string) ($listing['title'] ?? '')); ?>;
            L.marker(point, { icon: L.divIcon({ className: 'bandabou-map-pin-icon', html: pin, iconSize: null }), keyboard: false, interactive: false }).addTo(map);
        };
        document.head.appendChild(js);
    };
    if (! ('IntersectionObserver' in window)) { start(); return; }
    const observer = new IntersectionObserver(function (entries) {
        if (entries[0].isIntersecting) { observer.disconnect(); start(); }
    }, { rootMargin: '300px' });
    observer.observe(box);
})();
</script>
<?php endif; ?>
