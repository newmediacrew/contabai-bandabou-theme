<?php
/* Template Name: Location (country / region) */

use Contabai\Controllers\AiSeoRenderer;

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
    $titleTag = $generated ? 'p' : 'h1';
    ?>
    <div class="mx-auto max-w-7xl px-4 pt-6">
        <?php echo \Contabai\View::render('components.breadcrumb', ['crumbs' => $crumbs]); ?>
        <<?php echo $titleTag; ?> class="contabai-heading contabai-display text-4xl text-[color:var(--heading-color,#0B4F6C)]"><?php the_title(); ?></<?php echo $titleTag; ?>>
    </div>
    <?php the_content(); ?>
    <?php if ($generated) : ?>
        <?php
        echo AiSeoRenderer::content($id, \BandabouTheme\PhotoHero::image(0));
        echo AiSeoRenderer::related($id);
        echo AiSeoRenderer::jsonld($id);
        ?>
    <?php endif; ?>
<?php endwhile; ?>
<?php get_footer(); ?>
