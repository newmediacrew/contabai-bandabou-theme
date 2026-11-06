<?php

use BandabouTheme\Heroicon;
use BandabouTheme\Villas;

$footerLogo = (string) get_option('bandabou_logo', '');
$footerLogo = $footerLogo !== '' ? $footerLogo : get_theme_file_uri('assets/img/logo.webp');
$footerName = (string) get_option('bandabou_site_name', 'Bandabou Curaçao');
$footerPayoff = (string) get_option('bandabou_payoff', 'Real Estate · Rentals · Management');

$footerVillas = [];
if (class_exists('Contabai\\Controllers\\ListingRewriteController')) {
    foreach (Villas::random(5) as $villa) {
        $footerVillas[] = ['url' => \Contabai\Controllers\ListingRewriteController::listing_url($villa), 'label' => (string) ($villa['title'] ?? '')];
    }
}
$allVillasUrl = home_url('/' . (defined('CONTABAI_LISTINGS_PAGE_SLUG') ? CONTABAI_LISTINGS_PAGE_SLUG : 'contabai-listings'));

$infoLinks = [
    ['url' => home_url('/#bandabou'), 'label' => __('About Bandabou', 'contabai-bandabou-theme')],
    ['url' => home_url('/#bandabou'), 'label' => __('About Fontein', 'contabai-bandabou-theme')],
    ['url' => home_url('/#verhuren'), 'label' => __('List your home?', 'contabai-bandabou-theme')],
];
foreach (['terms' => __('Terms and conditions', 'contabai-bandabou-theme'), 'privacy' => __('Privacy', 'contabai-bandabou-theme')] as $legalKey => $legalLabel) {
    $legalPage = get_page_by_path(sanitize_title((string) get_option('bandabou_' . $legalKey . '_page_slug', $legalKey)));
    if ($legalPage && $legalPage->post_status === 'publish') {
        $infoLinks[] = ['url' => get_permalink($legalPage), 'label' => $legalLabel];
    }
}

$contactPhone = (string) get_option('bandabou_contact_phone', '+5999 699 6011');
$contactEmail = (string) get_option('bandabou_contact_email', 'rental@bandaboucuracao.com');
$contactLines = array_filter([
    (string) get_option('bandabou_contact_address', 'Parallelweg J-8'),
    (string) get_option('bandabou_contact_city', 'Julianadorp, Curaçao'),
]);

$socialDefs = [
    ['opt' => 'bandabou_social_facebook',  'icon' => 'facebook',  'label' => 'Facebook'],
    ['opt' => 'bandabou_social_x',         'icon' => 'x',         'label' => 'X'],
    ['opt' => 'bandabou_social_instagram', 'icon' => 'instagram', 'label' => 'Instagram'],
    ['opt' => 'bandabou_social_linkedin',  'icon' => 'linkedin',  'label' => 'LinkedIn'],
    ['opt' => 'bandabou_social_youtube',   'icon' => 'youtube',   'label' => 'YouTube'],
    ['opt' => 'bandabou_social_tiktok',    'icon' => 'tiktok',    'label' => 'TikTok'],
];
$socialLinks = [];
foreach ($socialDefs as $def) {
    $url = trim((string) get_option($def['opt'], ''));
    if ($url !== '') {
        $socialLinks[] = ['url' => $url, 'icon' => $def['icon'], 'label' => $def['label']];
    }
}
?>
</main>

<?php $villaPage = (bool) get_query_var('contabai_listing_id'); ?>
<div aria-hidden="true" class="<?php echo $villaPage ? 'bg-bandabou-sand ' : (is_front_page() ? '' : 'mt-16 '); ?>leading-[0]">
    <svg viewBox="0 0 1440 70" preserveAspectRatio="none" width="100%" height="70" class="block"><path d="M0 38 C 260 70 520 6 780 30 S 1220 66 1440 24 V70 H0 Z" fill="rgba(15,163,163,0.45)"></path><path d="M0 50 C 300 20 600 76 900 46 S 1280 30 1440 44 V70 H0 Z" fill="#0B5A6E"></path></svg>
</div>
<footer class="bandabou-footer">
    <div class="mx-auto grid max-w-[1280px] grid-cols-2 gap-x-5 gap-y-8 px-5 pb-8 pt-12 md:gap-9 md:px-8 md:pb-9 md:pt-14 lg:grid-cols-4 lg:gap-10 lg:pb-10 lg:pt-[72px]">
        <div class="col-span-2 flex flex-col gap-[18px] lg:col-span-1">
            <div class="flex items-center gap-3">
                <img src="<?php echo esc_url($footerLogo); ?>" alt="" loading="lazy" class="h-[52px] w-auto">
                <span class="contabai-display text-[26px] font-bold leading-[1.05] text-white"><?php echo implode('<br>', array_map('esc_html', explode(' ', $footerName, 2))); ?></span>
            </div>
            <p class="m-0 text-[15px] leading-[1.6]"><?php esc_html_e('Your trusted partner for an unforgettable stay on Curaçao.', 'contabai-bandabou-theme'); ?> <strong class="text-bandabou-gold">Ayo!</strong></p>
        </div>

        <nav aria-label="<?php esc_attr_e('Villas', 'contabai-bandabou-theme'); ?>" class="flex flex-col gap-2.5 text-[15px]">
            <h2 class="bandabou-footer-title"><?php esc_html_e('Villas', 'contabai-bandabou-theme'); ?></h2>
            <?php foreach ($footerVillas as $link) : ?>
                <a href="<?php echo esc_url($link['url']); ?>" class="bandabou-footer-link"><?php echo esc_html($link['label']); ?></a>
            <?php endforeach; ?>
            <a href="<?php echo esc_url($allVillasUrl); ?>" class="bandabou-footer-link font-semibold"><?php esc_html_e('All villas', 'contabai-bandabou-theme'); ?></a>
        </nav>

        <nav aria-label="<?php esc_attr_e('Information', 'contabai-bandabou-theme'); ?>" class="flex flex-col gap-2.5 text-[15px]">
            <h2 class="bandabou-footer-title"><?php esc_html_e('Information', 'contabai-bandabou-theme'); ?></h2>
            <?php foreach ($infoLinks as $link) : ?>
                <a href="<?php echo esc_url($link['url']); ?>" class="bandabou-footer-link"><?php echo esc_html($link['label']); ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="col-span-2 flex flex-col gap-2.5 text-[15px] lg:col-span-1">
            <h2 class="bandabou-footer-title"><?php esc_html_e('Contact', 'contabai-bandabou-theme'); ?></h2>
            <?php foreach ($contactLines as $line) : ?>
                <span><?php echo esc_html($line); ?></span>
            <?php endforeach; ?>
            <?php if ($contactPhone !== '') : ?>
                <a href="tel:<?php echo esc_attr(preg_replace('/[^+0-9]/', '', $contactPhone)); ?>" class="bandabou-footer-link"><?php echo esc_html($contactPhone); ?></a>
            <?php endif; ?>
            <?php if ($contactEmail !== '') : ?>
                <a href="mailto:<?php echo esc_attr($contactEmail); ?>" class="bandabou-footer-link"><?php echo esc_html($contactEmail); ?></a>
            <?php endif; ?>
        </div>
    </div>

    <div class="mx-auto flex max-w-[1280px] flex-wrap items-center justify-between gap-3 border-t border-white/10 px-5 pb-7 pt-5 text-[13px] text-[#9FCBD6] md:px-8 lg:pb-[30px] lg:pt-[22px]">
        <span class="flex flex-wrap items-center gap-x-2">
            <span>&copy; <?php echo esc_html(date('Y')); ?> <?php echo esc_html($footerName); ?></span>
            <span aria-hidden="true">&middot;</span>
            <a href="https://www.contabai.network" target="_blank" rel="noopener noreferrer" class="text-[#9FCBD6] no-underline transition hover:text-white"><?php esc_html_e('Powered by Contabai', 'contabai-bandabou-theme'); ?></a>
        </span>
        <?php if ($socialLinks !== []) : ?>
            <span class="flex items-center gap-3">
                <?php foreach ($socialLinks as $social) : ?>
                    <a href="<?php echo esc_url($social['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr($social['label']); ?>" aria-label="<?php echo esc_attr($social['label']); ?>" class="text-[#9FCBD6] no-underline transition hover:text-white"><?php echo Heroicon::render($social['icon'], 'brands', 'w-4 h-4'); ?></a>
                <?php endforeach; ?>
            </span>
        <?php endif; ?>
        <span><?php echo esc_html($footerPayoff); ?></span>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
