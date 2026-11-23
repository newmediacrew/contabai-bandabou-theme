<?php

namespace BandabouTheme;

class DefaultMenu
{
    public static function items(): array
    {
        return [
            ['label' => __('Villas', 'contabai-bandabou-theme'), 'url' => self::listingsUrl()],
            ['label' => __('Destinations', 'contabai-bandabou-theme'), 'url' => '', 'areas' => true],
            ['label' => __('About Bandabou', 'contabai-bandabou-theme'), 'url' => home_url('/about/')],
            ['label' => __('How to book', 'contabai-bandabou-theme'), 'url' => home_url('/#how-to-book')],
            ['label' => __('Rent out your home', 'contabai-bandabou-theme'), 'url' => home_url('/rent-out/')],
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

        self::build();
    }

    public static function restore(): void
    {
        $locations = (array) get_theme_mod('nav_menu_locations', []);
        if (! empty($locations['header_navigation'])) {
            wp_delete_nav_menu((int) $locations['header_navigation']);
        }

        self::build();
    }

    public static function mobile(): void
    {
        echo '<ul class="m-0 flex list-none flex-col p-0">';
        foreach (self::items() as $item) {
            if ($item['url'] === '') {
                continue;
            }
            echo '<li><a href="' . esc_url($item['url']) . '" class="contabai-display flex items-center gap-3 py-2.5 text-4xl font-semibold leading-tight text-white/85 no-underline transition hover:text-white lg:text-6xl">' . esc_html($item['label']) . '</a></li>';
        }
        echo '</ul>';
    }

    private static function build(): void
    {
        $switched = switch_to_locale(get_locale());

        $name = __('Main menu', 'contabai-bandabou-theme');
        $existing = wp_get_nav_menu_object($name);
        if ($existing) {
            wp_delete_nav_menu($existing->term_id);
        }

        $menuId = wp_create_nav_menu($name);
        if (! is_wp_error($menuId)) {
            $areas = Villas::areas();
            foreach (self::items() as $item) {
                if (! empty($item['areas']) && $areas === []) {
                    continue;
                }
                $parentId = self::addItem($menuId, $item['label'], $item['url'] !== '' ? $item['url'] : '#');
                if (empty($item['areas']) || is_wp_error($parentId)) {
                    continue;
                }
                foreach ($areas as $area) {
                    $url = add_query_arg(['country' => $area['country'], 'region' => $area['region'], 'area' => $area['area']], self::listingsUrl());
                    self::addItem($menuId, $area['name'], $url, $parentId);
                }
            }

            $locations = (array) get_theme_mod('nav_menu_locations', []);
            $locations['header_navigation'] = $menuId;
            set_theme_mod('nav_menu_locations', $locations);
        }

        if ($switched) {
            restore_previous_locale();
        }
    }

    private static function addItem(int $menuId, string $title, string $url, int $parentId = 0)
    {
        return wp_update_nav_menu_item($menuId, 0, [
            'menu-item-title'     => $title,
            'menu-item-url'       => $url,
            'menu-item-type'      => 'custom',
            'menu-item-status'    => 'publish',
            'menu-item-parent-id' => $parentId,
        ]);
    }

    private static function listingsUrl(): string
    {
        return home_url('/' . (defined('CONTABAI_LISTINGS_PAGE_SLUG') ? CONTABAI_LISTINGS_PAGE_SLUG : 'contabai-listings') . '/');
    }
}
