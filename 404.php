<?php set_query_var('contabai_overlay_header', true); ?>
<?php get_header(); ?>
<?php
get_template_part('template-parts/photo-hero', null, [
    'image'    => \BandabouTheme\PhotoHero::image(0),
    'title'    => __('Page not found', 'contabai-bandabou-theme'),
    'subtitle' => esc_html__('This page does not exist (anymore). Our villas are still here.', 'contabai-bandabou-theme'),
]);
?>
<div class="flex justify-center px-5 py-12">
    <a href="<?php echo esc_url(home_url()); ?>" class="bandabou-cta min-h-[52px] justify-center !rounded-[14px] px-7"><?php esc_html_e('Back to home', 'contabai-bandabou-theme'); ?></a>
</div>
<?php get_footer(); ?>
