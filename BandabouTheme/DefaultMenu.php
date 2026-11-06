<?php

namespace BandabouTheme;

class DefaultMenu
{
    private static function items(): array
    {
        return [
            ['label' => __('Villas', 'contabai-bandabou-theme'), 'url' => home_url('/#villas')],
            ['label' => __('Bandabou & Fontein', 'contabai-bandabou-theme'), 'url' => home_url('/#bandabou')],
            ['label' => __('List your home?', 'contabai-bandabou-theme'), 'url' => home_url('/#verhuren')],
            ['label' => __('Contact', 'contabai-bandabou-theme'), 'url' => home_url('/#contact')],
        ];
    }

    public static function desktop(): void
    {
        echo '<ul class="bandabou-nav-list">';
        foreach (self::items() as $item) {
            echo '<li class="menu-item"><a href="' . esc_url($item['url']) . '">' . esc_html($item['label']) . '</a></li>';
        }
        echo '</ul>';
    }

    public static function mobile(): void
    {
        echo '<ul class="m-0 flex list-none flex-col p-0">';
        foreach (self::items() as $item) {
            echo '<li><a href="' . esc_url($item['url']) . '" x-on:click="slideOverOpen = false" class="contabai-display flex items-center gap-3 py-2.5 text-4xl font-semibold leading-tight text-white/85 no-underline transition hover:text-white">' . esc_html($item['label']) . '</a></li>';
        }
        echo '</ul>';
    }
}
