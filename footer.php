<?php

use BandabouTheme\Heroicon;

$footerLogo = (string) get_option('bandabou_logo', '');
$footerLogo = $footerLogo !== '' ? $footerLogo : get_theme_file_uri('assets/img/logo.webp');
$footerName = (string) get_option('bandabou_site_name', 'Bandabou Curaçao');
$footerPayoff = (string) get_option('bandabou_payoff', 'Real Estate · Rentals · Management');

$legalLinks = [];
foreach (['terms' => __('Terms and conditions', 'contabai-bandabou-theme'), 'privacy' => __('Privacy', 'contabai-bandabou-theme')] as $legalKey => $legalLabel) {
    $legalPage = get_page_by_path(sanitize_title((string) get_option('bandabou_' . $legalKey . '_page_slug', $legalKey)));
    if ($legalPage && $legalPage->post_status === 'publish') {
        $legalLinks[] = ['url' => get_permalink($legalPage), 'label' => $legalLabel];
    }
}

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
    <div class="mx-auto max-w-[1280px] px-5 pb-8 pt-12 md:px-8 md:pb-9 md:pt-14 lg:pb-10 lg:pt-[72px]">
        <div class="flex max-w-[640px] flex-col gap-[18px]">
            <div class="flex items-center gap-3">
                <img src="<?php echo esc_url($footerLogo); ?>" alt="" loading="lazy" class="h-[52px] w-auto">
                <span class="contabai-display text-[26px] font-bold leading-[1.05] text-white"><?php echo implode('<br>', array_map('esc_html', explode(' ', $footerName, 2))); ?></span>
            </div>
            <p class="m-0 text-[15px] leading-[1.6]"><?php esc_html_e('Your trusted partner for an unforgettable stay on Curaçao.', 'contabai-bandabou-theme'); ?> <strong class="text-bandabou-gold">Ayo!</strong></p>
        </div>
    </div>

    <div class="mx-auto flex max-w-[1280px] flex-wrap items-center justify-between gap-3 border-t border-white/10 px-5 pb-7 pt-5 text-[13px] text-[#9FCBD6] md:px-8 lg:pb-[30px] lg:pt-[22px]">
        <span class="flex flex-wrap items-center gap-x-4">
            <span>&copy; <?php echo esc_html(date('Y')); ?> <?php echo esc_html($footerName); ?></span>
            <span aria-hidden="true">&middot;</span>
            <a href="https://www.contabai.network" target="_blank" rel="noopener noreferrer" class="text-[#9FCBD6] no-underline transition hover:text-white"><?php esc_html_e('Powered by Contabai', 'contabai-bandabou-theme'); ?></a>
            <?php foreach ($legalLinks as $link) : ?>
                <span class="flex items-center gap-x-4">
                    <span aria-hidden="true">&middot;</span>
                    <a href="<?php echo esc_url($link['url']); ?>" class="text-[#9FCBD6] no-underline transition hover:text-white"><?php echo esc_html($link['label']); ?></a>
                </span>
            <?php endforeach; ?>
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
