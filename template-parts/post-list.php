<div class="bandabou-container py-10">
    <?php if (have_posts()) : ?>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php while (have_posts()) : the_post(); ?>
                <a href="<?php the_permalink(); ?>" class="bandabou-card group no-underline">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="aspect-[16/10] overflow-hidden bg-neutral-100">
                            <?php the_post_thumbnail('medium_large', ['class' => 'h-full w-full object-cover transition duration-300 group-hover:scale-105']); ?>
                        </div>
                    <?php endif; ?>
                    <div class="flex flex-1 flex-col p-5">
                        <h2 class="bandabou-card-title"><?php the_title(); ?></h2>
                        <p class="contabai-body mt-2 line-clamp-3 flex-1 text-sm text-[color:var(--content-text-color,#3A4A52)]"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 25)); ?></p>
                        <span class="mt-4 text-xs text-[#5A5348]"><?php echo esc_html(get_the_date()); ?></span>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(['mid_size' => 1, 'prev_text' => __('Previous', 'contabai-bandabou-theme'), 'next_text' => __('Next', 'contabai-bandabou-theme'), 'class' => 'bandabou-pagination']); ?>
    <?php else : ?>
        <p class="contabai-body py-16 text-center text-[color:var(--content-text-color,#3A4A52)]"><?php esc_html_e('No posts found.', 'contabai-bandabou-theme'); ?></p>
    <?php endif; ?>
</div>
