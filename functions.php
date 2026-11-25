<?php

use BandabouTheme\DefaultMenu;
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
    $missing = [];
    foreach (['privacy', 'terms'] as $key) {
        $pageSlug = sanitize_title((string) get_option('bandabou_' . $key . '_page_slug', $key));
        if (! get_page_by_path($pageSlug)) {
            $missing[$key] = $pageSlug;
        }
    }
    if ($missing === []) {
        return;
    }
    $switched = switch_to_locale(get_locale());

    $titles = [
        'privacy' => __('Privacy policy', 'contabai-bandabou-theme'),
        'terms'   => __('Terms of service', 'contabai-bandabou-theme'),
    ];
    foreach ($missing as $key => $pageSlug) {
        $id = wp_insert_post([
            'post_type'   => 'page',
            'post_name'   => $pageSlug,
            'post_title'  => $titles[$key],
            'post_status' => 'draft',
        ]);
        if ($key === 'privacy' && ! (int) get_option('wp_page_for_privacy_policy')) {
            update_option('wp_page_for_privacy_policy', $id);
        }
    }
    if ($switched) {
        restore_previous_locale();
    }
};
add_action('after_switch_theme', $legalPages);
add_action('admin_init', $legalPages);

$aboutPage = function () {
    if (get_page_by_path('about')) {
        return;
    }
    $switched = switch_to_locale(get_locale());

    $items = '';
    foreach ([__('Secure resorts', 'contabai-bandabou-theme'), __('Beaches nearby', 'contabai-bandabou-theme'), __('Hiking, cycling & diving', 'contabai-bandabou-theme'), __('Free parking at the villa', 'contabai-bandabou-theme')] as $item) {
        $items .= '<!-- wp:list-item --><li>' . esc_html($item) . '</li><!-- /wp:list-item -->';
    }
    $content = "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">" . esc_html__('The quiet west side of Curaçao', 'contabai-bandabou-theme') . "</h2>\n<!-- /wp:heading -->\n\n"
        . "<!-- wp:paragraph -->\n<p>" . esc_html__('Bandabou is the green, hilly west of the island. Here you will find hiking trails through Christoffelpark, small bays with clear water and beaches like Cas Abou, Grote Knip and Playa Lagun, where you can snorkel straight from the shore.', 'contabai-bandabou-theme') . "</p>\n<!-- /wp:paragraph -->\n\n"
        . "<!-- wp:paragraph -->\n<p>" . esc_html__('Our holiday homes and villas are spread across special places in Bandabou, including Villapark Fontein, Coral Estate, Bario Bandabou in San Sebastian and Cas Abou. Every spot has its own character: from a relaxed stay among the green hills to a villa overlooking the Caribbean Sea. Choose the surroundings and facilities that suit your holiday and enjoy outdoor living on the west side of Curaçao.', 'contabai-bandabou-theme') . "</p>\n<!-- /wp:paragraph -->\n\n"
        . "<!-- wp:list -->\n<ul class=\"wp-block-list\">" . $items . "</ul>\n<!-- /wp:list -->";

    wp_insert_post([
        'post_type'    => 'page',
        'post_name'    => 'about',
        'post_title'   => __('About Bandabou', 'contabai-bandabou-theme'),
        'post_status'  => 'draft',
        'post_content' => $content,
    ]);
    if ($switched) {
        restore_previous_locale();
    }
};
add_action('after_switch_theme', $aboutPage);
add_action('admin_init', $aboutPage);

$rentOutPage = function () {
    if (get_page_by_path('rent-out')) {
        return;
    }
    $switched = switch_to_locale(get_locale());

    $listItems = function (array $items): string {
        $html = '';
        foreach ($items as [$label, $text]) {
            $html .= '<!-- wp:list-item --><li><strong>' . esc_html($label) . '.</strong> ' . esc_html($text) . '</li><!-- /wp:list-item -->';
        }
        return $html;
    };
    $services = $listItems([
        [__('A strong presentation', 'contabai-bandabou-theme'), __('Beautiful photos and a clear description, so guests immediately see what makes your home special.', 'contabai-bandabou-theme')],
        [__('Bookings and guests', 'contabai-bandabou-theme'), __('We answer questions, handle bookings and stay in touch with your guests, from their first question until after they leave.', 'contabai-bandabou-theme')],
        [__('One calendar for everything', 'contabai-bandabou-theme'), __('Your availability is correct everywhere, on our own site and on the platforms where your home is listed, without double bookings.', 'contabai-bandabou-theme')],
        [__('A personal welcome', 'contabai-bandabou-theme'), __('Our team welcomes your guests, hands over the key and shows them the home.', 'contabai-bandabou-theme')],
        [__('Cleaning and small maintenance', 'contabai-bandabou-theme'), __('After every stay your home is fresh and ready again, and small jobs such as replacing a light bulb are taken care of right away.', 'contabai-bandabou-theme')],
    ]);
    $howItWorks = $listItems([
        [__('Get acquainted', 'contabai-bandabou-theme'), __('You tell us about your home and your wishes.', 'contabai-bandabou-theme')],
        [__('Going online', 'contabai-bandabou-theme'), __('We get your home ready for rental and put it online.', 'contabai-bandabou-theme')],
        [__('We handle the guests', 'contabai-bandabou-theme'), __('Bookings, welcome, cleaning and questions during the stay.', 'contabai-bandabou-theme')],
        [__('You enjoy the income', 'contabai-bandabou-theme'), __('Without worrying about the day-to-day running.', 'contabai-bandabou-theme')],
    ]);
    $heading = fn (string $text): string => "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">" . esc_html($text) . "</h2>\n<!-- /wp:heading -->";
    $paragraph = fn (string $text): string => "<!-- wp:paragraph -->\n<p>" . esc_html($text) . "</p>\n<!-- /wp:paragraph -->";

    $content = implode("\n\n", [
        $heading(__('Do you own a home in Bandabou?', 'contabai-bandabou-theme')),
        $paragraph(__('Your holiday home on Curaçao deserves guests who enjoy it as much as you do. We take the rental and management off your hands, from the first photo to the last clean. You keep your peace of mind, we make sure everything runs smoothly.', 'contabai-bandabou-theme')),
        $heading(__('What we arrange for you', 'contabai-bandabou-theme')),
        "<!-- wp:list -->\n<ul class=\"wp-block-list\">" . $services . "</ul>\n<!-- /wp:list -->",
        $heading(__('How it works', 'contabai-bandabou-theme')),
        "<!-- wp:list {\"ordered\":true} -->\n<ol class=\"wp-block-list\">" . $howItWorks . "</ol>\n<!-- /wp:list -->",
        $heading(__('Why Bandabou Curaçao', 'contabai-bandabou-theme')),
        $paragraph(__('We live on the island and know Bandabou like no one else. Guests book directly with us, with no platform in between, and get personal contact from booking to departure. You notice it in happy guests and a home that is in good hands.', 'contabai-bandabou-theme')),
        $paragraph(__('Curious what we can do for your home?', 'contabai-bandabou-theme')),
        "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link wp-element-button\" href=\"" . esc_url(home_url('/#contact')) . "\">" . esc_html__('Ask about the options', 'contabai-bandabou-theme') . "</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons -->",
    ]);

    wp_insert_post([
        'post_type'    => 'page',
        'post_name'    => 'rent-out',
        'post_title'   => __('Rent out your home', 'contabai-bandabou-theme'),
        'post_status'  => 'draft',
        'post_content' => $content,
    ]);
    if ($switched) {
        restore_previous_locale();
    }
};
add_action('after_switch_theme', $rentOutPage);
add_action('admin_init', $rentOutPage);

add_action('init', function () {
    register_nav_menus(['header_navigation' => esc_html__('Header navigation', 'contabai-bandabou-theme')]);
});

add_action('after_switch_theme', [DefaultMenu::class, 'create']);
add_action('admin_init', [DefaultMenu::class, 'create']);

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('contabai-bandabou-theme-css', get_template_directory_uri() . '/assets/css/theme.css', [], '10');
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

add_filter('get_the_archive_title_prefix', '__return_empty_string');
