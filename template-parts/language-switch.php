<?php $current = strtoupper(substr(get_locale(), 0, 2)); ?>
<div class="relative" x-data="{ langOpen: false }" x-on:click.outside="langOpen = false" x-on:keydown.escape.window="langOpen = false">
    <button type="button" x-on:click="langOpen = ! langOpen" x-bind:aria-expanded="langOpen" title="<?php esc_attr_e('Language', 'contabai-bandabou-theme'); ?>" aria-label="<?php esc_attr_e('Language', 'contabai-bandabou-theme'); ?>" class="bandabou-header-icon text-[13px] font-semibold"><?php echo esc_html($current); ?></button>
    <ul x-show="langOpen" x-cloak x-transition.opacity.duration.150ms class="bandabou-lang-menu">
        <?php foreach (\BandabouTheme\Languages::links() as $language) : ?>
            <li><a href="<?php echo esc_url($language['url']); ?>" hreflang="<?php echo esc_attr(strtolower($language['code'])); ?>"<?php echo $language['current'] ? ' aria-current="true" class="is-active"' : ''; ?>><?php echo esc_html($language['code']); ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>
