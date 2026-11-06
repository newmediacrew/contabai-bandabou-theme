<?php

use BandabouTheme\Heroicon;

$languages = $args['languages'] ?? \BandabouTheme\Languages::links();
$current   = strtoupper(substr(get_locale(), 0, 2));
?>
<?php if ($languages === []) : ?>
    <span class="bandabou-lang"><span class="bandabou-lang-item is-active"><?php echo esc_html($current); ?></span></span>
<?php else : ?>
    <div class="relative" x-data="{ langOpen: false }" x-on:click.outside="langOpen = false" x-on:keydown.escape.window="langOpen = false">
        <button type="button" x-on:click="langOpen = ! langOpen" x-bind:aria-expanded="langOpen" aria-label="<?php esc_attr_e('Language', 'contabai-bandabou-theme'); ?>" class="bandabou-lang">
            <span class="bandabou-lang-item is-active inline-flex items-center gap-1"><?php echo esc_html($current); ?><?php echo Heroicon::outline('chevron-down', 'w-3.5 h-3.5'); ?></span>
        </button>
        <ul x-show="langOpen" x-cloak x-transition.opacity.duration.150ms class="bandabou-lang-menu">
            <?php foreach ($languages as $language) : ?>
                <li><a href="<?php echo esc_url($language['url']); ?>" hreflang="<?php echo esc_attr(strtolower($language['code'])); ?>"<?php echo $language['current'] ? ' aria-current="true" class="is-active"' : ''; ?>><?php echo esc_html($language['code']); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
