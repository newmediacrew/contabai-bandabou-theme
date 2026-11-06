<?php
/* Template Name: Listing (no breadcrumb) */

$isListingDetail = (bool) get_query_var('contabai_listing_id');
if ($isListingDetail && class_exists('Contabai\\Controllers\\ListingController')) {
    [$listingData] = \Contabai\Controllers\ListingController::get((int) get_query_var('contabai_listing_id'));
    $listingForHeader = $listingData['data']['listing'] ?? null;
    if ($listingForHeader && ! empty($listingForHeader['photos']) && \Contabai\Controllers\ListingController::hostAllows($listingForHeader)) {
        set_query_var('contabai_overlay_header', true);
    }
}
?>
<?php get_header(); ?>
<?php while (have_posts()) : the_post(); ?>
    <?php the_content(); ?>
<?php endwhile; ?>
<?php get_footer(); ?>
