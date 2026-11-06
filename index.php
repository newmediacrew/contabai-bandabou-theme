<?php set_query_var('contabai_overlay_header', true); ?>
<?php get_header(); ?>
<?php
get_template_part('template-parts/photo-hero', null, [
    'image'  => \BandabouTheme\PhotoHero::image(0),
    'title'  => __('News', 'contabai-bandabou-theme'),
    'crumbs' => [['label' => __('Home', 'contabai-bandabou-theme'), 'url' => home_url('/')], ['label' => __('News', 'contabai-bandabou-theme'), 'url' => null]],
]);
get_template_part('template-parts/post-list');
?>
<?php get_footer(); ?>
