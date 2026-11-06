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

<?php get_footer(); ?>
