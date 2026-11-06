<?php

use BandabouTheme\Heroicon;

$mmName       = (string) ($args['brandName'] ?? '');
$mmPayoff     = (string) ($args['brandPayoff'] ?? '');
$mmLogo       = (string) ($args['brandLogo'] ?? '');
$mmCtaShow    = ! empty($args['ctaShow']);
$mmCtaLabel   = (string) ($args['ctaLabel'] ?? '');
$mmCtaUrl     = (string) ($args['ctaUrl'] ?? '');
$mmAccountUrl = (string) ($args['accountUrl'] ?? home_url('/'));
$mmPhoto      = \BandabouTheme\PhotoHero::image(0);
?>
<div x-data="{ slideOverOpen: false }" class="lg:hidden">
    <button x-on:click="slideOverOpen = true" title="<?php esc_attr_e('Menu', 'contabai-bandabou-theme'); ?>" aria-label="<?php esc_attr_e('Menu', 'contabai-bandabou-theme'); ?>" class="bandabou-header-icon">
        <?php echo Heroicon::outline('bars-3', 'w-5 h-5'); ?>
    </button>
    <template x-teleport="body">
        <div id="mobile-menu" x-show="slideOverOpen" x-cloak role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Menu', 'contabai-bandabou-theme'); ?>"
             x-on:keydown.escape.window="slideOverOpen = false"
             x-effect="document.body.style.overflow = slideOverOpen ? 'hidden' : ''"
             x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[60] overflow-hidden bg-[#073B4C] text-white lg:hidden">
            <?php if ($mmPhoto !== '') : ?>
                <img src="<?php echo esc_url($mmPhoto); ?>" alt="" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full scale-105 object-cover">
            <?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-b from-[#062636]/55 via-[#062636]/75 to-[#062636]/90"></div>

            <div class="relative flex h-full flex-col px-5 pb-7 pt-4">
                <div class="flex items-center justify-between gap-4">
                    <a href="<?php echo esc_url(home_url()); ?>" class="flex min-w-0 items-center gap-2.5 text-white no-underline">
                        <img src="<?php echo esc_url($mmLogo); ?>" alt="" class="h-[42px] w-auto shrink-0">
                        <span class="contabai-display truncate text-[22px] font-bold"><?php echo esc_html($mmName); ?></span>
                    </a>
                    <button x-on:click="slideOverOpen = false" aria-label="<?php esc_attr_e('Close', 'contabai-bandabou-theme'); ?>"
                            class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-white/35 bg-white/10 text-white backdrop-blur-sm transition hover:bg-white/20">
                        <?php echo Heroicon::outline('x-mark', 'w-5 h-5'); ?>
                    </button>
                </div>

                <div class="mt-9 min-h-0 flex-1 overflow-y-auto">
                    <?php if ($mmPayoff !== '') : ?>
                        <p class="bandabou-eyebrow-on-dark mb-2"><?php echo esc_html($mmPayoff); ?></p>
                    <?php endif; ?>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'header_navigation',
                        'container'      => false,
                        'depth'          => 2,
                        'fallback_cb'    => [\BandabouTheme\DefaultMenu::class, 'mobile'],
                        'walker'         => new \BandabouTheme\MobileMenuWalker(),
                        'items_wrap'     => '<ul class="m-0 flex list-none flex-col p-0">%3$s</ul>',
                    ]);
                    ?>
                </div>

                <div class="mt-6 flex flex-col gap-2.5">
                    <?php if ($mmCtaShow) : ?>
                        <a href="<?php echo esc_url($mmCtaUrl); ?>" class="bandabou-cta h-13 justify-center text-base">
                            <?php echo Heroicon::render('whatsapp', 'brands', 'w-5 h-5'); ?><?php echo esc_html($mmCtaLabel); ?>
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo esc_url($mmAccountUrl); ?>" class="inline-flex h-13 items-center justify-center gap-2 rounded-full border border-white/45 bg-white/10 text-base font-semibold text-white no-underline backdrop-blur-sm transition hover:bg-white/20">
                        <?php echo Heroicon::solid('user-circle', 'w-5 h-5'); ?><?php esc_html_e('My hub', 'contabai-bandabou-theme'); ?>
                    </a>
                </div>
            </div>
        </div>
    </template>
</div>
