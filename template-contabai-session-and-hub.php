<?php
/* Template Name: Contabai Session & Hub (blank, centered) */
?>
<!doctype html>
<html lang="<?php echo esc_attr(substr(get_locale(), 0, 2)); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php wp_head(); ?>
</head>
<body class="min-h-screen text-neutral-900 antialiased">
<div class="grid grid-cols-1 min-h-screen place-items-center px-4 py-10">
    <div class="w-full min-w-0">
        <?php while (have_posts()) : the_post(); ?>
            <?php the_content(); ?>
        <?php endwhile; ?>
        <p class="mt-8 text-center text-xs text-neutral-400"><a href="https://www.contabai.network" target="_blank" rel="noopener noreferrer" class="text-inherit no-underline transition-colors hover:text-neutral-600 hover:underline"><?php esc_html_e('Powered by Contabai', 'contabai-bandabou-theme'); ?></a></p>
    </div>
</div>
<?php wp_footer(); ?>
</body>
</html>
