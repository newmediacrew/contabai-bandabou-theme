<?php
/* Template Name: Location (country / region) */

use Contabai\Controllers\AiSeoRenderer;

set_query_var('contabai_overlay_header', true);
get_header();
?>
<?php while (have_posts()) : the_post(); ?>
    <?php
    $id        = get_the_ID();
    $generated = class_exists('Contabai\\Controllers\\AiSeoRenderer') && AiSeoRenderer::is_generated($id);

    $crumbs = [['label' => __('Home', 'contabai-bandabou-theme'), 'url' => home_url('/')]];
    foreach (array_reverse(get_post_ancestors($id)) as $ancestorId) {
        $crumbs[] = ['label' => get_the_title($ancestorId), 'url' => get_permalink($ancestorId)];
    }
    $crumbs[] = ['label' => get_the_title(), 'url' => null];

    get_template_part('template-parts/photo-hero', null, [
        'image'    => \BandabouTheme\PhotoHero::image($id),
        'alt'      => \BandabouTheme\PhotoHero::alt($id),
        'title'    => get_the_title(),
        'tag'      => $generated ? 'p' : 'h1',
        'subtitle' => (! $generated && has_excerpt()) ? get_the_excerpt() : '',
        'crumbs'   => $crumbs,
        'search'   => true,
    ]);
    ?>
    <div class="bandabou-results"><?php the_content(); ?></div>
    <?php if ($generated) : ?>
        <?php
        echo AiSeoRenderer::content($id, \BandabouTheme\PhotoHero::image(0));
        echo AiSeoRenderer::related($id);
        echo AiSeoRenderer::jsonld($id);
        ?>
    <?php endif; ?>
<?php endwhile; ?>
<?php get_footer(); ?>
