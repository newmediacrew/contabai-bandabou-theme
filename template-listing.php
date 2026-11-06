<?php
/* Template Name: Listing (no breadcrumb) */

$isListingDetail = (bool) get_query_var('contabai_listing_id');
$overlay = ! $isListingDetail;
if ($isListingDetail && class_exists('Contabai\\Controllers\\ListingController')) {
    [$listingData] = \Contabai\Controllers\ListingController::get((int) get_query_var('contabai_listing_id'));
    $listingForHeader = $listingData['data']['listing'] ?? null;
    $overlay = $listingForHeader && ! empty($listingForHeader['photos']) && \Contabai\Controllers\ListingController::hostAllows($listingForHeader);
}
if ($overlay) {
    set_query_var('contabai_overlay_header', true);
}
?>
<?php get_header(); ?>
<?php while (have_posts()) : the_post(); ?>
    <?php if ($isListingDetail) : ?>
        <?php the_content(); ?>
    <?php else : ?>
        <?php
        get_template_part('template-parts/photo-hero', null, [
            'image'  => \BandabouTheme\PhotoHero::image(get_the_ID()),
            'title'  => __('Our villas', 'contabai-bandabou-theme'),
            'crumbs' => [['label' => __('Home', 'contabai-bandabou-theme'), 'url' => home_url('/')], ['label' => __('Our villas', 'contabai-bandabou-theme'), 'url' => null]],
            'search' => true,
        ]);
        ?>
        <div class="bandabou-results"><?php the_content(); ?></div>
    <?php endif; ?>
<?php endwhile; ?>
<?php get_footer(); ?>
