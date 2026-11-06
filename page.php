<?php set_query_var('contabai_overlay_header', true); ?>
<?php get_header(); ?>
<?php while (have_posts()) : the_post(); ?>
    <?php
    get_template_part('template-parts/photo-hero', null, [
        'image'  => \BandabouTheme\PhotoHero::image(get_the_ID()),
        'alt'    => \BandabouTheme\PhotoHero::alt(get_the_ID()),
        'title'  => get_the_title(),
        'crumbs' => [['label' => __('Home', 'contabai-bandabou-theme'), 'url' => home_url('/')], ['label' => get_the_title(), 'url' => null]],
    ]);
    ?>
    <div class="bandabou-container py-10">
        <div class="entry-content mx-auto max-w-3xl"><?php the_content(); ?></div>
    </div>
<?php endwhile; ?>
<?php get_footer(); ?>
