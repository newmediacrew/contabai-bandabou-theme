<?php

namespace BandabouTheme;

class DefaultMenu
{
    public static function items(): array
    {
        return [
            ['label' => __('Villas', 'contabai-bandabou-theme'), 'url' => home_url('/#villas')],
            ['label' => __('Bandabou & Fontein', 'contabai-bandabou-theme'), 'url' => home_url('/#bandabou')],
            ['label' => __('List your home?', 'contabai-bandabou-theme'), 'url' => home_url('/#verhuren')],
            ['label' => __('Contact', 'contabai-bandabou-theme'), 'url' => home_url('/#contact')],
        ];
    }

    public static function create(): void
    {
        if (get_option('bandabou_default_menu') !== false) {
            return;
        }
        add_option('bandabou_default_menu', '1');

        $locations = (array) get_theme_mod('nav_menu_locations', []);
        if (! empty($locations['header_navigation'])) {
            return;
        }

        switch_to_locale(get_locale());
        $name = __('Main menu', 'contabai-bandabou-theme');
        $menu = wp_get_nav_menu_object($name);
        $menuId = $menu ? (int) $menu->term_id : wp_create_nav_menu($name);
        if (! is_wp_error($menuId)) {
            if (! $menu) {
                foreach (self::items() as $item) {
                    wp_update_nav_menu_item($menuId, 0, [
                        'menu-item-title'  => $item['label'],
                        'menu-item-url'    => $item['url'],
                        'menu-item-type'   => 'custom',
                        'menu-item-status' => 'publish',
                    ]);
                }
            }
            $locations['header_navigation'] = $menuId;
            set_theme_mod('nav_menu_locations', $locations);
        }
        restore_previous_locale();
    }

    public static function mobile(): void
    {
        echo '<ul class="m-0 flex list-none flex-col p-0">';
        foreach (self::items() as $item) {
            echo '<li><a href="' . esc_url($item['url']) . '" class="contabai-display flex items-center gap-3 py-2.5 text-4xl font-semibold leading-tight text-white/85 no-underline transition hover:text-white lg:text-6xl">' . esc_html($item['label']) . '</a></li>';
        }
        echo '</ul>';
    }
}
