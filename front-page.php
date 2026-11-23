<?php $heroEnabled = get_option('bandabou_hero_enabled', '1') === '1'; ?>
<?php if ($heroEnabled) { set_query_var('contabai_overlay_header', true); } ?>
<?php get_header(); ?>

<?php if ($heroEnabled) : ?>
    <?php get_template_part('template-parts/hero'); ?>
<?php elseif (shortcode_exists('contabai_search')) : ?>
    <div class="bandabou-container relative z-20 py-10"><?php echo do_shortcode('[contabai_search]'); ?></div>
<?php endif; ?>

<?php get_template_part('template-parts/home-sections'); ?>
<?php get_template_part('template-parts/home-contact'); ?>

<?php
$waLabel = (string) get_option('bandabou_cta_label', __('WhatsApp us', 'contabai-bandabou-theme'));
$waUrl   = (string) get_option('bandabou_cta_url', 'https://wa.me/59996996011');
?>
<?php if (get_option('bandabou_cta_enabled', '1') === '1' && $waLabel !== '' && $waUrl !== '') : ?>
    <a href="<?php echo esc_url($waUrl); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr($waLabel); ?>" aria-label="<?php echo esc_attr($waLabel); ?>" class="bandabou-whatsapp-fab">
        <?php echo \BandabouTheme\Heroicon::render('whatsapp', 'brands', 'w-7 h-7'); ?>
    </a>
<?php endif; ?>

<?php get_footer(); ?>
