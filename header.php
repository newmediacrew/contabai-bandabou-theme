<?php

use BandabouTheme\Heroicon;

$accountUrl = home_url('/' . (defined('CONTABAI_ACCOUNT_PAGE_SLUG') ? CONTABAI_ACCOUNT_PAGE_SLUG : 'contabai-account'));
$chatUrl    = home_url('/' . (defined('CONTABAI_CHAT_PAGE_SLUG') ? CONTABAI_CHAT_PAGE_SLUG : 'contabai-chat'));
$isLoggedIn = ! empty($_COOKIE['contabai_sanctum_session_token']);

$brandLogo     = (string) get_option('bandabou_logo', '');
$brandLogo     = $brandLogo !== '' ? $brandLogo : get_theme_file_uri('assets/img/logo.webp');
$brandName     = (string) get_option('bandabou_site_name', 'Bandabou Curaçao');
$brandPayoff   = (string) get_option('bandabou_payoff', 'Real Estate · Rentals · Management');
$brandShowText = get_option('bandabou_show_brand_text', '1') === '1';
$stickyHeader  = get_option('bandabou_sticky_header', '1') === '1';

$ctaLabel = (string) get_option('bandabou_cta_label', __('WhatsApp us', 'contabai-bandabou-theme'));
$ctaUrl   = (string) get_option('bandabou_cta_url', 'https://wa.me/59996996011');
$ctaShow  = get_option('bandabou_cta_enabled', '1') === '1' && $ctaLabel !== '' && $ctaUrl !== '';
$overlayHeader = (bool) get_query_var('contabai_overlay_header');
?>
<!doctype html>
<html lang="<?php echo esc_attr(substr(get_locale(), 0, 2)); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php wp_head(); ?>
</head>
<body class="flex min-h-screen flex-col text-bandabou-ink antialiased">

<?php
$topbarColumns = [];
if (get_option('bandabou_topbar_enabled', '0') === '1') {
    for ($topbarIndex = 1; $topbarIndex <= 4; $topbarIndex++) {
        $topbarTitle = trim((string) get_option('bandabou_topbar_col' . $topbarIndex . '_title', ''));
        $topbarText = trim((string) get_option('bandabou_topbar_col' . $topbarIndex . '_text', ''));
        if ($topbarTitle === '' && $topbarText === '') {
            continue;
        }
        $topbarIcon = (string) get_option('bandabou_topbar_col' . $topbarIndex . '_icon', 'check');
        $topbarColumns[] = [
            'n' => $topbarIndex,
            'title' => $topbarTitle,
            'text' => $topbarText,
            'icon' => $topbarIcon !== '' ? $topbarIcon : 'check',
        ];
    }
}
?>
<?php if ($topbarColumns !== []) : ?>
<div class="bandabou-topbar w-full text-sm">
    <div class="no-scrollbar mx-auto flex h-10 max-w-7xl items-center gap-x-6 overflow-x-auto px-4">
        <?php foreach ($topbarColumns as $topbarColumn) : ?>
            <div class="mx-auto flex shrink-0 items-center gap-1.5 whitespace-nowrap">
                <?php echo Heroicon::solid($topbarColumn['icon'], 'w-4 h-4 flex-none bandabou-topbar-icon-' . $topbarColumn['n']); ?>
                <?php if ($topbarColumn['title'] !== '') : ?><span class="font-medium"><?php echo esc_html($topbarColumn['title']); ?></span><?php endif; ?>
                <?php if ($topbarColumn['text'] !== '') : ?><span class="opacity-85"><?php echo wp_kses_post($topbarColumn['text']); ?></span><?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<header class="contabai-header bandabou-header<?php echo $overlayHeader ? ' is-overlay' : ''; ?><?php echo $stickyHeader ? ' is-sticky' : ''; ?>"
        x-data="{ scrolled: false }" x-on:scroll.window="scrolled = scrolled ? window.scrollY > 4 : window.scrollY > 40" x-bind:class="scrolled ? 'is-scrolled' : ''">
    <div class="bandabou-header-inner">
        <a href="<?php echo esc_url(home_url()); ?>" class="flex min-w-0 items-center gap-2.5 no-underline lg:gap-3.5">
            <img src="<?php echo esc_url($brandLogo); ?>" alt="<?php echo esc_attr($brandShowText ? '' : $brandName); ?>" class="bandabou-logo">
            <?php if ($brandShowText) : ?>
                <span class="flex min-w-0 flex-col gap-[3px]">
                    <span class="bandabou-brand"><?php echo esc_html($brandName); ?></span>
                    <?php if ($brandPayoff !== '') : ?>
                        <span class="bandabou-payoff hidden md:block"><?php echo esc_html($brandPayoff); ?></span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </a>

        <div class="flex shrink-0 items-center gap-2 lg:gap-2.5">
            <div class="hidden md:block"><?php get_template_part('template-parts/language-switch'); ?></div>

            <?php if ($isLoggedIn) : ?>
                <span class="hidden md:inline-flex"><a href="<?php echo esc_url($chatUrl); ?>" title="<?php esc_attr_e('Chats', 'contabai-bandabou-theme'); ?>" aria-label="<?php esc_attr_e('Chats', 'contabai-bandabou-theme'); ?>" class="bandabou-header-icon relative">
                    <?php echo Heroicon::outline('chat-bubble-left-right', 'w-5 h-5'); ?>
                    <span class="js-chat-unread contabai-chat-badge absolute -right-1 -top-1 inline-flex min-w-[1.25rem] items-center justify-center rounded-full px-1.5 py-0.5 text-xs font-semibold leading-none text-white ring-2 ring-white"></span>
                </a></span>
            <?php endif; ?>

            <a href="<?php echo esc_url($accountUrl); ?>" title="<?php esc_attr_e('My hub', 'contabai-bandabou-theme'); ?>" aria-label="<?php esc_attr_e('My hub', 'contabai-bandabou-theme'); ?>" class="bandabou-header-icon">
                <?php echo Heroicon::solid('user-circle', 'w-5 h-5'); ?>
            </a>

            <?php if ($ctaShow) : ?>
                <span class="hidden md:inline-flex"><a href="<?php echo esc_url($ctaUrl); ?>" title="<?php echo esc_attr($ctaLabel); ?>" aria-label="<?php echo esc_attr($ctaLabel); ?>" class="bandabou-header-icon">
                    <?php echo Heroicon::outline('chat-bubble-oval-left', 'w-5 h-5'); ?>
                </a></span>
            <?php endif; ?>

            <?php get_template_part('template-parts/mobile-menu', null, ['brandName' => $brandName, 'brandPayoff' => $brandPayoff, 'brandLogo' => $brandLogo, 'ctaShow' => $ctaShow, 'ctaLabel' => $ctaLabel, 'ctaUrl' => $ctaUrl, 'accountUrl' => $accountUrl]); ?>
        </div>
    </div>
</header>
<?php if ($overlayHeader) : ?>
<script>
(function () {
    const header = document.querySelector('.bandabou-header.is-overlay');
    if (! header) { return; }
    const measure = function () {
        if (! header.classList.contains('is-scrolled')) {
            document.documentElement.style.setProperty('--bandabou-header-h', header.offsetHeight + 'px');
        }
    };
    measure();
    window.addEventListener('resize', measure);
})();
</script>
<?php endif; ?>

<main class="min-h-[60vh] flex-1">
