<?php get_header(); ?>
<?php while (have_posts()) : the_post(); ?>
    <div class="mx-auto max-w-7xl px-4 py-10">
        <h1 class="contabai-heading contabai-display text-4xl text-[color:var(--heading-color,#0B4F6C)]"><?php the_title(); ?></h1>
        <div class="entry-content mt-6"><?php the_content(); ?></div>
    </div>
<?php endwhile; ?>
<?php get_footer(); ?>
