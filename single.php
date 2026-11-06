<?php set_query_var('contabai_overlay_header', true); ?>
<?php get_header(); ?>
<?php while (have_posts()) : the_post(); ?>
    <?php
    $categories = get_the_category();
    $crumbs = [['label' => __('Home', 'contabai-bandabou-theme'), 'url' => home_url('/')]];
    if (! empty($categories)) {
        $crumbs[] = ['label' => $categories[0]->name, 'url' => get_category_link($categories[0]->term_id)];
    }
    $crumbs[] = ['label' => get_the_title(), 'url' => null];
    ?>
    <article itemscope itemtype="https://schema.org/Article">
        <?php
        get_template_part('template-parts/photo-hero', null, [
            'image'      => \BandabouTheme\PhotoHero::image(get_the_ID()),
            'alt'        => \BandabouTheme\PhotoHero::alt(get_the_ID()),
            'title'      => get_the_title(),
            'title_attr' => ' itemprop="headline"',
            'eyebrow'    => implode(' · ', array_filter([! empty($categories) ? $categories[0]->name : '', get_the_date()])),
            'crumbs'     => $crumbs,
        ]);
        ?>
        <div class="bandabou-container py-10">
            <div class="entry-content mx-auto max-w-3xl" itemprop="articleBody"><?php the_content(); ?></div>
        </div>
    </article>
<?php endwhile; ?>
<?php get_footer(); ?>
