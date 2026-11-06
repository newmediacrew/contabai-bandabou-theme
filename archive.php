<?php set_query_var('contabai_overlay_header', true); ?>
<?php get_header(); ?>
<?php
$archiveTitle = wp_strip_all_tags(get_the_archive_title());
get_template_part('template-parts/photo-hero', null, [
    'image'    => \BandabouTheme\PhotoHero::image(0),
    'title'    => $archiveTitle,
    'subtitle' => get_the_archive_description(),
    'crumbs'   => [['label' => __('Home', 'contabai-bandabou-theme'), 'url' => home_url('/')], ['label' => $archiveTitle, 'url' => null]],
]);
get_template_part('template-parts/post-list');
?>
<?php get_footer(); ?>
