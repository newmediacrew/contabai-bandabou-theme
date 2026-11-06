<?php

use BandabouTheme\Heroicon;
use BandabouTheme\ThemeSettings\ThemeSettings;
use BandabouTheme\ThemeUpdater;

require __DIR__ . '/vendor/autoload.php';

add_theme_support('post-thumbnails');
add_theme_support('title-tag');
add_theme_support('menus');

add_action('after_setup_theme', function () {
    load_theme_textdomain('contabai-bandabou-theme', get_template_directory() . '/languages');
});

add_action('after_switch_theme', function () {
    foreach (ThemeSettings::DESIGN_DEFAULTS + ['bandabou_cta_enabled' => '1'] as $option => $value) {
        add_option($option, $value);
    }
});

$legalPages = function () {
    if (get_option('bandabou_legal_pages') !== false) {
        return;
    }

    $pages = [
        'privacy' => __('Privacy policy', 'contabai-bandabou-theme'),
        'terms'   => __('Terms of service', 'contabai-bandabou-theme'),
    ];
    $ids = [];
    foreach ($pages as $slug => $title) {
        $pageSlug = sanitize_title((string) get_option('bandabou_' . $slug . '_page_slug', $slug));
        $page = get_page_by_path($pageSlug);
        $ids[$slug] = $page ? $page->ID : wp_insert_post([
            'post_type'   => 'page',
            'post_name'   => $pageSlug,
            'post_title'  => $title,
            'post_status' => 'draft',
        ]);
    }

    if (! (int) get_option('wp_page_for_privacy_policy')) {
        update_option('wp_page_for_privacy_policy', $ids['privacy']);
    }
    add_option('bandabou_legal_pages', $ids);
};
add_action('after_switch_theme', $legalPages);
add_action('admin_init', $legalPages);

add_action('init', function () {
    register_nav_menus(['header_navigation' => esc_html__('Header navigation', 'contabai-bandabou-theme')]);
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('contabai-bandabou-theme-css', get_template_directory_uri() . '/assets/css/theme.css', [], '1');
});

add_action('wp_head', function () {
    if (has_site_icon()) {
        return;
    }
    $img = get_template_directory_uri() . '/assets/img';
    echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url($img . '/favicon-32.png') . '">';
    echo '<link rel="apple-touch-icon" href="' . esc_url($img . '/apple-touch-icon.png') . '">';
}, 5);

if (class_exists(ThemeSettings::class)) {
    new ThemeSettings();
}

if (class_exists(ThemeUpdater::class)) {
    new ThemeUpdater();
}

add_filter('nav_menu_item_title', function ($title, $item) {
    if (in_array('menu-item-has-children', $item->classes, true)) {
        $title .= Heroicon::outline('chevron-down', 'w-4 h-4');
    }
    return $title;
}, 10, 2);

add_filter('get_the_archive_title_prefix', '__return_empty_string');
