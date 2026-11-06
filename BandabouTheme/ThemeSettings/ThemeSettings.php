<?php

namespace BandabouTheme\ThemeSettings;

use BandabouTheme\Heroicon;

class ThemeSettings
{
    public const DESIGN_DEFAULTS = [
        'bandabou_site_name'             => 'Bandabou Curaçao',
        'bandabou_payoff'                => 'Real Estate · Rentals · Management',
        'bandabou_color_schema'          => '#EE6A3C',
        'bandabou_link_color'            => '#0B6E6C',
        'bandabou_font_schema'           => 'Figtree',
        'bandabou_heading_font'          => 'Cormorant Garamond',
        'bandabou_heading_weight'        => '600',
        'bandabou_heading_line_height'   => '1.1',
        'bandabou_heading_color'         => '#0B4F6C',
        'bandabou_body_text_color'       => '#3A4A52',
        'bandabou_eyebrow_color'         => '#0B6E6C',
        'bandabou_bg_type'               => 'color',
        'bandabou_bg_color'              => '#FBF5EA',
    ];

    private array $google_fonts = [
        'Abel', 'Albert Sans', 'Amatic SC', 'Anton', 'Archivo',
        'Arimo', 'Asap', 'Asap Condensed', 'Atkinson Hyperlegible', 'Baloo 2',
        'Barlow', 'Be Vietnam Pro', 'Bitter', 'Brawler', 'Bricolage Grotesque',
        'Cabin', 'Cairo', 'Caladea', 'Catamaran', 'Chivo',
        'Commissioner', 'Cormorant Garamond', 'Crimson Text', 'DM Sans', 'Dosis',
        'EB Garamond', 'Encode Sans', 'Exo 2', 'Figtree',
        'Fira Sans', 'Fira Sans Condensed', 'Francois One', 'Gabarito', 'Hanken Grotesk', 'Heebo',
        'Hind', 'IBM Plex Sans', 'Inconsolata', 'Instrument Sans', 'Inter',
        'Josefin Sans', 'Josefin Slab', 'Jost', 'Kanit', 'Karla',
        'Lato', 'Lexend', 'Libre Baskerville', 'Libre Franklin', 'Lora',
        'Manrope', 'Merriweather', 'Merriweather Sans', 'Montserrat', 'Mukta',
        'Mulish', 'Nanum Gothic', 'Noto Sans', 'Noto Serif',
        'Nunito', 'Nunito Sans', 'Onest', 'Open Sans', 'Oswald',
        'Outfit', 'Overpass', 'Oxygen', 'PT Sans', 'PT Serif',
        'Playfair Display', 'Plus Jakarta Sans', 'Poppins', 'Prompt', 'Quicksand',
        'Raleway', 'Red Hat Display', 'Righteous', 'Roboto', 'Roboto Condensed',
        'Rokkitt', 'Rubik', 'Russo One', 'Sarabun', 'Signika',
        'Signika Negative', 'Slabo 27px', 'Sora',
        'Space Mono', 'Spectral', 'Titillium Web', 'Ubuntu', 'Ubuntu Condensed',
        'Urbanist', 'Varela Round', 'Vollkorn', 'Work Sans', 'Yanone Kaffeesatz',
    ];

    private array $topbar_icons = [
        'check', 'check-circle', 'check-badge', 'shield-check', 'star',
        'heart', 'home', 'home-modern', 'key', 'map-pin',
        'globe-europe-africa', 'users', 'user-group', 'wifi', 'banknotes',
        'currency-euro', 'wallet', 'credit-card', 'sparkles', 'hand-thumb-up',
        'chat-bubble-left-right', 'phone', 'clock', 'calendar-days', 'gift',
        'tag', 'receipt-percent', 'trophy', 'lock-closed', 'fire',
        'sun', 'building-storefront', 'truck', 'ticket', 'lifebuoy',
        'bolt', 'rocket-launch',
    ];

    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_init', [$this, 'register_theme_settings']);
        add_action('wp_head', [$this, 'add_theme_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_media_uploader']);
        add_filter('use_block_editor_for_post', [$this, 'maybe_classic_editor']);
        add_action('wp_enqueue_scripts', [$this, 'maybe_dequeue_block_css'], 100);
        add_action('admin_post_bandabou_theme_reset_design', [$this, 'handle_reset_design']);
    }

    public function handle_reset_design(): void
    {
        if (! current_user_can('edit_theme_options')) {
            wp_die(esc_html__('You are not allowed to do this.', 'contabai-bandabou-theme'), 403);
        }
        check_admin_referer('bandabou_theme_reset_design', 'bandabou_theme_reset_design_nonce');

        foreach ($this->fields() as $field) {
            [$key, , $type, , $tab] = $field;
            if ($type === 'color' || in_array($tab, ['typography', 'background'], true) || in_array($key, ['bandabou_hero_animation', 'bandabou_hero_overlay_opacity', 'bandabou_hero_align'], true)) {
                delete_option($key);
            }
        }
        foreach (self::DESIGN_DEFAULTS as $option => $value) {
            update_option($option, $value);
        }

        wp_safe_redirect(add_query_arg(['page' => 'bandabou-theme-options', 'tab' => 'advanced', 'design-reset' => '1'], admin_url('admin.php')));
        exit;
    }

    public function sanitize_site_name($value): string
    {
        $value = sanitize_text_field((string) $value);
        if (trim($value) !== '') {
            update_option('blogname', $value);
        }
        return $value;
    }

    public function sanitize_payoff($value): string
    {
        $value = sanitize_text_field((string) $value);
        update_option('blogdescription', $value);
        return $value;
    }

    public function maybe_classic_editor($use)
    {
        return get_option('bandabou_classic_editor', '0') === '1' ? false : $use;
    }

    public function maybe_dequeue_block_css(): void
    {
        if (get_option('bandabou_classic_editor', '0') !== '1') {
            return;
        }
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('global-styles');
    }

    private function tabs(): array
    {
        return [
            'branding' => __('Branding', 'contabai-bandabou-theme'),
            'hero' => __('Hero', 'contabai-bandabou-theme'),
            'topbar' => __('Top bar', 'contabai-bandabou-theme'),
            'typography' => __('Typography', 'contabai-bandabou-theme'),
            'background' => __('Background', 'contabai-bandabou-theme'),
            'contact' => __('Contact', 'contabai-bandabou-theme'),
            'socials' => __('Socials', 'contabai-bandabou-theme'),
            'updates' => __('Updates', 'contabai-bandabou-theme'),
            'advanced' => __('Advanced', 'contabai-bandabou-theme'),
        ];
    }

    private function fields(): array
    {
        $fields = [
            ['bandabou_site_name', __('Site name', 'contabai-bandabou-theme'), 'text', 'Bandabou Curaçao', 'branding'],
            ['bandabou_logo', __('Logo', 'contabai-bandabou-theme'), 'media', '', 'branding', __('Leave empty to use the Bandabou logo.', 'contabai-bandabou-theme')],
            ['bandabou_logo_height', __('Logo height', 'contabai-bandabou-theme'), 'number', '58', 'branding'],
            ['bandabou_payoff', __('Payoff', 'contabai-bandabou-theme'), 'text', 'Real Estate · Rentals · Management', 'branding'],
            ['bandabou_show_brand_text', __('Show brand text', 'contabai-bandabou-theme'), 'checkbox', '1', 'branding'],
            ['bandabou_color_schema', __('Theme color', 'contabai-bandabou-theme'), 'color', '#EE6A3C', 'branding'],
            ['bandabou_cta_enabled', __('Call-to-action button', 'contabai-bandabou-theme'), 'checkbox', '1', 'branding'],
            ['bandabou_cta_label', __('CTA label', 'contabai-bandabou-theme'), 'text', __('WhatsApp us', 'contabai-bandabou-theme'), 'branding'],
            ['bandabou_cta_url', __('CTA URL', 'contabai-bandabou-theme'), 'url', 'https://wa.me/59996996011', 'branding'],
            ['bandabou_sticky_header', __('Sticky header', 'contabai-bandabou-theme'), 'checkbox', '1', 'branding'],
            ['bandabou_breadcrumbs_enabled', __('Breadcrumbs', 'contabai-bandabou-theme'), 'checkbox', '1', 'branding'],

            ['bandabou_hero_enabled', __('Show hero on homepage', 'contabai-bandabou-theme'), 'checkbox', '1', 'hero'],
            ['bandabou_hero_listing_photos', __('Use listing cover photos', 'contabai-bandabou-theme'), 'checkbox', '1', 'hero', __('Shows the cover photos of random listings in the hero instead of the stock photos.', 'contabai-bandabou-theme')],
            ['bandabou_hero_slides', __('Number of slides', 'contabai-bandabou-theme'), 'hero_slides', '5', 'hero'],
            ['bandabou_hero_eyebrow', __('Eyebrow', 'contabai-bandabou-theme'), 'text', __('Villapark Fontein · Bandabou · Curaçao', 'contabai-bandabou-theme'), 'hero'],
            ['bandabou_hero_eyebrow_color', __('Eyebrow color', 'contabai-bandabou-theme'), 'color', '#F4C22B', 'hero'],
            ['bandabou_hero_heading', __('Heading', 'contabai-bandabou-theme'), 'text', __('Your villa in peaceful Bandabou', 'contabai-bandabou-theme'), 'hero'],
            ['bandabou_hero_heading_color', __('Heading color', 'contabai-bandabou-theme'), 'color', '#ffffff', 'hero'],
            ['bandabou_hero_subheading', __('Subheading', 'contabai-bandabou-theme'), 'textarea', __('Carefully furnished villas on Villapark Fontein, with views over the hills and the Caribbean Sea. You book directly with us, the local Bandabou Curaçao team.', 'contabai-bandabou-theme'), 'hero'],
            ['bandabou_hero_subheading_color', __('Subheading color', 'contabai-bandabou-theme'), 'color', '#F6F1E6', 'hero'],
            ['bandabou_hero_badge1_text', __('Badge 1 text', 'contabai-bandabou-theme'), 'text', '', 'hero'],
            ['bandabou_hero_badge1_icon', __('Badge 1 icon', 'contabai-bandabou-theme'), 'icon', 'shield-check', 'hero'],
            ['bandabou_hero_badge1_icon_color', __('Badge 1 icon color', 'contabai-bandabou-theme'), 'color', '#F4C22B', 'hero'],
            ['bandabou_hero_badge2_text', __('Badge 2 text', 'contabai-bandabou-theme'), 'text', '', 'hero'],
            ['bandabou_hero_badge2_icon', __('Badge 2 icon', 'contabai-bandabou-theme'), 'icon', 'banknotes', 'hero'],
            ['bandabou_hero_badge2_icon_color', __('Badge 2 icon color', 'contabai-bandabou-theme'), 'color', '#F4C22B', 'hero'],
            ['bandabou_hero_badge3_text', __('Badge 3 text', 'contabai-bandabou-theme'), 'text', '', 'hero'],
            ['bandabou_hero_badge3_icon', __('Badge 3 icon', 'contabai-bandabou-theme'), 'icon', 'globe-europe-africa', 'hero'],
            ['bandabou_hero_badge3_icon_color', __('Badge 3 icon color', 'contabai-bandabou-theme'), 'color', '#F4C22B', 'hero'],
            ['bandabou_hero_animation', __('Background animation', 'contabai-bandabou-theme'), 'hero_anim', 'fade', 'hero'],
            ['bandabou_hero_overlay_color', __('Overlay color', 'contabai-bandabou-theme'), 'color', '#062636', 'hero'],
            ['bandabou_hero_overlay_opacity', __('Overlay opacity', 'contabai-bandabou-theme'), 'hero_overlay_opacity', '0', 'hero'],
            ['bandabou_hero_align', __('Text alignment', 'contabai-bandabou-theme'), 'hero_align', 'left', 'hero'],

            ['bandabou_topbar_enabled', __('Show top bar', 'contabai-bandabou-theme'), 'checkbox', '0', 'topbar'],
            ['bandabou_topbar_bg', __('Background', 'contabai-bandabou-theme'), 'color', '#0B5A6E', 'topbar'],
            ['bandabou_topbar_text', __('Text color', 'contabai-bandabou-theme'), 'color', '#ffffff', 'topbar'],

            ['bandabou_font_schema', __('Body font', 'contabai-bandabou-theme'), 'font', 'Figtree', 'typography'],
            ['bandabou_heading_font', __('Heading font', 'contabai-bandabou-theme'), 'font', 'Cormorant Garamond', 'typography'],
            ['bandabou_base_font_size', __('Base font size', 'contabai-bandabou-theme'), 'font_size', '16', 'typography'],
            ['bandabou_base_line_height', __('Line height', 'contabai-bandabou-theme'), 'line_height', '1.6', 'typography'],
            ['bandabou_body_text_color', __('Body text color', 'contabai-bandabou-theme'), 'color', '#3A4A52', 'typography'],
            ['bandabou_paragraph_spacing', __('Paragraph spacing', 'contabai-bandabou-theme'), 'paragraph_spacing', 'normal', 'typography'],
            ['bandabou_heading_weight', __('Heading weight', 'contabai-bandabou-theme'), 'heading_weight', '600', 'typography'],
            ['bandabou_heading_line_height', __('Heading line height', 'contabai-bandabou-theme'), 'heading_line_height', '1.1', 'typography'],
            ['bandabou_heading_color', __('Heading color', 'contabai-bandabou-theme'), 'color', '#0B4F6C', 'typography'],
            ['bandabou_eyebrow_color', __('Section eyebrow color', 'contabai-bandabou-theme'), 'color', '#0B6E6C', 'typography'],
            ['bandabou_link_color', __('Link color', 'contabai-bandabou-theme'), 'color', '#0B6E6C', 'typography'],
            ['bandabou_link_underline', __('Link underline', 'contabai-bandabou-theme'), 'link_underline', 'always', 'typography'],

            ['bandabou_bg_type', __('Type', 'contabai-bandabou-theme'), 'bg_type', 'color', 'background'],
            ['bandabou_bg_color', __('Color', 'contabai-bandabou-theme'), 'color', '#FBF5EA', 'background'],
            ['bandabou_bg_gradient', __('Gradient', 'contabai-bandabou-theme'), 'bg_gradient', '1', 'background'],
            ['bandabou_bg_image', __('Image', 'contabai-bandabou-theme'), 'media', '', 'background'],
            ['bandabou_bg_repeat', __('Repeat', 'contabai-bandabou-theme'), 'bg_repeat', 'no-repeat', 'background'],
            ['bandabou_bg_size', __('Size', 'contabai-bandabou-theme'), 'bg_size', 'cover', 'background'],
            ['bandabou_bg_position', __('Position', 'contabai-bandabou-theme'), 'bg_position', 'center center', 'background'],
            ['bandabou_content_opacity', __('Content opacity', 'contabai-bandabou-theme'), 'content_opacity', '100', 'background'],

            ['bandabou_contact_address', __('Address', 'contabai-bandabou-theme'), 'text', 'Parallelweg J-8', 'contact'],
            ['bandabou_contact_city', __('City', 'contabai-bandabou-theme'), 'text', 'Julianadorp, Curaçao', 'contact'],
            ['bandabou_contact_phone', __('Phone', 'contabai-bandabou-theme'), 'text', '+5999 699 6011', 'contact'],
            ['bandabou_contact_email', __('Email', 'contabai-bandabou-theme'), 'text', 'rental@bandaboucuracao.com', 'contact'],

            ['bandabou_social_facebook', __('Facebook', 'contabai-bandabou-theme'), 'url', '', 'socials'],
            ['bandabou_social_x', __('X', 'contabai-bandabou-theme'), 'url', '', 'socials'],
            ['bandabou_social_instagram', __('Instagram', 'contabai-bandabou-theme'), 'url', 'https://www.instagram.com/bandaboucuracao', 'socials'],
            ['bandabou_social_linkedin', __('LinkedIn', 'contabai-bandabou-theme'), 'url', '', 'socials'],
            ['bandabou_social_youtube', __('YouTube', 'contabai-bandabou-theme'), 'url', '', 'socials'],
            ['bandabou_social_tiktok', __('TikTok', 'contabai-bandabou-theme'), 'url', '', 'socials'],

            ['bandabou_privacy_page_slug', __('Privacy page slug', 'contabai-bandabou-theme'), 'text', 'privacy', 'advanced'],
            ['bandabou_terms_page_slug', __('Terms page slug', 'contabai-bandabou-theme'), 'text', 'terms', 'advanced'],
            ['bandabou_classic_editor', __('Classic editor', 'contabai-bandabou-theme'), 'checkbox', '0', 'advanced'],
            ['bandabou_dev_mode', __('Development mode', 'contabai-bandabou-theme'), 'checkbox', '0', 'advanced', __('Switches off theme updates from GitHub. Turn ON for a local development site that is a git checkout. Leave OFF in production.', 'contabai-bandabou-theme')],
        ];

        for ($i = 1; $i <= 4; $i++) {
            $fields[] = ['bandabou_topbar_col' . $i . '_title', sprintf(__('Column %d title', 'contabai-bandabou-theme'), $i), 'text', '', 'topbar'];
            $fields[] = ['bandabou_topbar_col' . $i . '_text', sprintf(__('Column %d text', 'contabai-bandabou-theme'), $i), 'inline', '', 'topbar'];
            $fields[] = ['bandabou_topbar_col' . $i . '_icon', sprintf(__('Column %d icon', 'contabai-bandabou-theme'), $i), 'icon', 'check', 'topbar'];
            $fields[] = ['bandabou_topbar_col' . $i . '_icon_color', sprintf(__('Column %d icon color', 'contabai-bandabou-theme'), $i), 'color', '#F4C22B', 'topbar'];
        }

        return $fields;
    }

    public function register_menu(): void
    {
        add_menu_page(
            __('Bandabou Theme', 'contabai-bandabou-theme'),
            __('Bandabou Theme', 'contabai-bandabou-theme'),
            'edit_theme_options',
            'bandabou-theme-options',
            [$this, 'render_settings_page'],
            'dashicons-art',
            59
        );
    }

    public function sanitize_font(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (in_array($value, $this->google_fonts, true)) {
            return $value;
        }

        return 'Figtree';
    }

    public function sanitize_icon(string $value): string
    {
        return in_array($value, $this->topbar_icons, true) ? $value : 'check';
    }

    private function humanize(string $icon): string
    {
        return ucwords(str_replace('-', ' ', $icon));
    }

    public function enqueue_media_uploader(string $hook): void
    {
        if ($hook !== 'toplevel_page_bandabou-theme-options') {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_style('bandabou-theme-admin', get_template_directory_uri() . '/assets/css/admin.css', [], '3');
    }

    public function register_theme_settings(): void
    {
        foreach (array_keys($this->tabs()) as $tab) {
            add_settings_section($tab . '_section', '', '__return_false', 'bandabou-theme-' . $tab);
        }

        foreach ($this->fields() as $field) {
            [$key, $label, $type, $default, $tab] = $field;

            $callback = 'sanitize_text_field';
            if ($type === 'media' || $type === 'url') {
                $callback = 'esc_url_raw';
            } elseif ($type === 'number') {
                $callback = 'absint';
            } elseif ($type === 'color') {
                $callback = 'sanitize_hex_color';
            } elseif ($type === 'font') {
                $callback = [$this, 'sanitize_font'];
            } elseif ($type === 'icon') {
                $callback = [$this, 'sanitize_icon'];
            } elseif ($type === 'textarea') {
                $callback = 'sanitize_textarea_field';
            } elseif ($type === 'inline') {
                $callback = [$this, 'sanitize_inline_html'];
            }

            if ($key === 'bandabou_site_name') {
                $callback = [$this, 'sanitize_site_name'];
            } elseif ($key === 'bandabou_payoff') {
                $callback = [$this, 'sanitize_payoff'];
            } elseif ($key === 'bandabou_privacy_page_slug' || $key === 'bandabou_terms_page_slug') {
                $callback = 'sanitize_title';
            }

            register_setting('bandabou_theme_' . $tab, $key, [
                'type' => 'string',
                'sanitize_callback' => $callback,
                'default' => $default,
            ]);

            add_settings_field($key, $label, [$this, 'render_field_' . $type], 'bandabou-theme-' . $tab, $tab . '_section', ['key' => $key, 'default' => $default, 'description' => $field[5] ?? '']);
        }
    }

    public function render_settings_page(): void
    {
        if (! current_user_can('edit_theme_options')) {
            return;
        }

        $tabs = $this->tabs();
        $current = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'branding';
        if (! isset($tabs[$current])) {
            $current = 'branding';
        }

        if (isset($_GET['settings-updated'])) {
            add_settings_error('bandabou_theme_messages', 'saved', __('Settings saved.', 'contabai-bandabou-theme'), 'updated');
        }
        if (isset($_GET['design-reset'])) {
            add_settings_error('bandabou_theme_messages', 'design-reset', __('The default design and typography are restored.', 'contabai-bandabou-theme'), 'updated');
        }

        ?>
        <div class="wrap contabai-admin">
            <h1><?php esc_html_e('Bandabou Theme', 'contabai-bandabou-theme'); ?></h1>
            <?php settings_errors('bandabou_theme_messages'); ?>

            <h2 class="nav-tab-wrapper">
                <?php foreach ($tabs as $key => $label) : ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=bandabou-theme-options&tab=' . $key)); ?>" class="nav-tab<?php echo $key === $current ? ' nav-tab-active' : ''; ?>"><?php echo esc_html($label); ?></a>
                <?php endforeach; ?>
            </h2>

            <?php if ($current === 'updates') : ?>
                <?php \BandabouTheme\ThemeUpdater::render_status(); ?>
            <?php else : ?>
            <form method="post" action="options.php">
                <?php
                settings_fields('bandabou_theme_' . $current);
                do_settings_sections('bandabou-theme-' . $current);
                submit_button();
                ?>
            </form>
            <?php if ($current === 'advanced') : ?>
                <hr>
                <h2><?php esc_html_e('Restore default design', 'contabai-bandabou-theme'); ?></h2>
                <p class="description"><?php esc_html_e('Sets the colours, fonts, typography, background and hero look back to the theme\'s default design. Texts, logo, menus, links and all other settings stay as they are.', 'contabai-bandabou-theme'); ?></p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return window.confirm('<?php echo esc_js(__('Restore the default design and typography? Your own colours and fonts will be replaced.', 'contabai-bandabou-theme')); ?>');">
                    <input type="hidden" name="action" value="bandabou_theme_reset_design">
                    <?php wp_nonce_field('bandabou_theme_reset_design', 'bandabou_theme_reset_design_nonce'); ?>
                    <?php submit_button(__('Restore default design', 'contabai-bandabou-theme'), 'secondary', 'bandabou-theme-reset-design', false); ?>
                </form>
            <?php endif; ?>
            <?php endif; ?>

            <?php
            $this->render_media_script();
            if ($current === 'topbar' || $current === 'hero') {
                $this->render_icon_picker_script();
            }
            ?>
        </div>
        <?php
    }

    private function render_media_script(): void
    {
        ?>
        <script>
        document.querySelectorAll('[data-media-btn]').forEach(function (b) {
            b.addEventListener('click', function (e) {
                e.preventDefault();
                const input = document.getElementById(b.getAttribute('data-media-btn'));
                const frame = wp.media({ multiple: false });
                frame.on('select', function () {
                    input.value = frame.state().get('selection').first().toJSON().url;
                });
                frame.open();
            });
        });
        document.querySelectorAll('[data-media-remove]').forEach(function (r) {
            r.addEventListener('click', function (e) {
                e.preventDefault();
                document.getElementById(r.getAttribute('data-media-remove')).value = '';
            });
        });
        </script>
        <?php
    }

    private function render_icon_picker_script(): void
    {
        ?>
        <script>
        document.querySelectorAll('.contabai-icon-grid').forEach(function (grid) {
            grid.querySelectorAll('input[type=radio]').forEach(function (r) {
                r.addEventListener('change', function () {
                    grid.querySelectorAll('.contabai-icon-option').forEach(function (o) { o.classList.remove('is-selected'); });
                    if (r.checked) { r.closest('.contabai-icon-option').classList.add('is-selected'); }
                });
            });
        });
        </script>
        <?php
    }

    public function render_field_color(array $args): void
    {
        $value = get_option($args['key'], $args['default']);
        if (empty($value)) {
            $value = $args['default'];
        }
        ?>
        <input type="color" name="<?php echo esc_attr($args['key']); ?>" value="<?php echo esc_attr($value); ?>">
        <?php
    }

    public function render_field_font(array $args): void
    {
        $selected = get_option($args['key'], $args['default']);
        ?>
        <select name="<?php echo esc_attr($args['key']); ?>">
            <?php foreach ($this->google_fonts as $font) : ?>
                <option value="<?php echo esc_attr($font); ?>" <?php selected($font, $selected); ?>><?php echo esc_html($font); ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    public function render_field_checkbox(array $args): void
    {
        $value = get_option($args['key'], $args['default'] ?? '0');
        ?>
        <label><input type="checkbox" name="<?php echo esc_attr($args['key']); ?>" value="1" <?php checked($value, '1'); ?>> <?php esc_html_e('Enable', 'contabai-bandabou-theme'); ?></label>
        <?php if (($args['description'] ?? '') !== '') : ?>
            <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php endif; ?>
        <?php
    }

    public function render_field_text(array $args): void
    {
        $value = get_option($args['key'], $args['default'] ?? '');
        ?>
        <input type="text" name="<?php echo esc_attr($args['key']); ?>" value="<?php echo esc_attr($value); ?>" class="regular-text">
        <?php
    }

    public function render_field_url(array $args): void
    {
        $value = get_option($args['key'], '');
        ?>
        <input type="url" name="<?php echo esc_attr($args['key']); ?>" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="https://">
        <?php
    }

    public function render_field_textarea(array $args): void
    {
        $value = get_option($args['key'], $args['default'] ?? '');
        ?>
        <textarea name="<?php echo esc_attr($args['key']); ?>" rows="2" class="large-text"><?php echo esc_textarea($value); ?></textarea>
        <?php
    }

    public function render_field_inline(array $args): void
    {
        $value = get_option($args['key'], '');
        wp_editor($value, $args['key'], [
            'textarea_name' => $args['key'],
            'textarea_rows' => 3,
            'teeny' => true,
            'media_buttons' => false,
        ]);
        ?>
        <p class="description"><?php esc_html_e('Bold, italic and links are kept; the top bar is a single line, so paragraphs and lists are not.', 'contabai-bandabou-theme'); ?></p>
        <?php
    }

    public function sanitize_inline_html(string $value): string
    {
        return wp_kses_post($value);
    }

    public function render_field_icon(array $args): void
    {
        $key = $args['key'];
        $selected = get_option($key, $args['default']);
        if (! in_array($selected, $this->topbar_icons, true)) {
            $selected = 'check';
        }

        ?>
        <div class="contabai-icon-grid">
            <?php foreach ($this->topbar_icons as $icon) : ?>
                <label class="contabai-icon-option<?php echo $icon === $selected ? ' is-selected' : ''; ?>" title="<?php echo esc_attr($this->humanize($icon)); ?>">
                    <input type="radio" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($icon); ?>" <?php checked($icon, $selected); ?>>
                    <?php echo Heroicon::solid($icon, 'w-5 h-5'); ?>
                </label>
            <?php endforeach; ?>
        </div>
        <?php
    }

    private function render_select(string $key, string $default, array $options): void
    {
        $value = get_option($key, $default);
        ?>
        <select name="<?php echo esc_attr($key); ?>">
            <?php foreach ($options as $val => $label) : ?>
                <option value="<?php echo esc_attr($val); ?>" <?php selected($value, (string) $val); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    private function percent_options(): array
    {
        $options = [];
        for ($i = 10; $i <= 100; $i += 10) {
            $options[(string) $i] = $i . '%';
        }

        return $options;
    }

    public function render_field_font_size(array $args): void
    {
        $this->render_select($args['key'], $args['default'], ['14' => '14px', '15' => '15px', '16' => '16px', '17' => '17px', '18' => '18px']);
    }

    public function render_field_line_height(array $args): void
    {
        $this->render_select($args['key'], $args['default'], ['1.4' => '1.4', '1.5' => '1.5', '1.6' => '1.6', '1.75' => '1.75']);
    }

    public function render_field_paragraph_spacing(array $args): void
    {
        $this->render_select($args['key'], $args['default'], [
            'tight' => __('Tight', 'contabai-bandabou-theme'),
            'normal' => __('Normal', 'contabai-bandabou-theme'),
            'relaxed' => __('Relaxed', 'contabai-bandabou-theme'),
        ]);
    }

    public function render_field_heading_weight(array $args): void
    {
        $this->render_select($args['key'], $args['default'], [
            '500' => __('Medium', 'contabai-bandabou-theme'),
            '600' => __('Semibold', 'contabai-bandabou-theme'),
            '700' => __('Bold', 'contabai-bandabou-theme'),
            '800' => __('Extra bold', 'contabai-bandabou-theme'),
        ]);
    }

    public function render_field_heading_line_height(array $args): void
    {
        $this->render_select($args['key'], $args['default'], ['1.1' => '1.1', '1.2' => '1.2', '1.3' => '1.3']);
    }

    public function render_field_link_underline(array $args): void
    {
        $this->render_select($args['key'], $args['default'], [
            'always' => __('Always', 'contabai-bandabou-theme'),
            'hover' => __('On hover', 'contabai-bandabou-theme'),
            'none' => __('None', 'contabai-bandabou-theme'),
        ]);
    }

    public function render_field_hero_align(array $args): void
    {
        $this->render_select($args['key'], $args['default'], [
            'left' => __('Left', 'contabai-bandabou-theme'),
            'center' => __('Center', 'contabai-bandabou-theme'),
            'right' => __('Right', 'contabai-bandabou-theme'),
        ]);
    }

    public function render_field_hero_slides(array $args): void
    {
        $this->render_select($args['key'], $args['default'], ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8']);
    }

    public function render_field_hero_overlay_opacity(array $args): void
    {
        $options = [];
        for ($i = 0; $i <= 90; $i += 10) {
            $options[(string) $i] = $i . '%';
        }
        $this->render_select($args['key'], $args['default'], $options);
    }

    public function render_field_hero_anim(array $args): void
    {
        $this->render_select($args['key'], $args['default'], [
            'zoom-in' => __('Ken Burns — zoom in', 'contabai-bandabou-theme'),
            'zoom-out' => __('Ken Burns — zoom out', 'contabai-bandabou-theme'),
            'pan-left' => __('Pan left', 'contabai-bandabou-theme'),
            'pan-right' => __('Pan right', 'contabai-bandabou-theme'),
            'pan-up' => __('Pan up', 'contabai-bandabou-theme'),
            'pan-down' => __('Pan down', 'contabai-bandabou-theme'),
            'diagonal' => __('Diagonal drift', 'contabai-bandabou-theme'),
            'fade' => __('Crossfade only', 'contabai-bandabou-theme'),
            'breathe' => __('Breathe', 'contabai-bandabou-theme'),
            'static' => __('Static (no motion)', 'contabai-bandabou-theme'),
        ]);
    }

    public function render_field_content_opacity(array $args): void
    {
        $this->render_select($args['key'], $args['default'], $this->percent_options());
    }

    public function render_field_bg_type(array $args): void
    {
        $this->render_select($args['key'], $args['default'], [
            'color' => __('Color', 'contabai-bandabou-theme'),
            'gradient' => __('Gradient', 'contabai-bandabou-theme'),
            'image' => __('Image', 'contabai-bandabou-theme'),
        ]);
    }

    public function render_field_bg_gradient(array $args): void
    {
        $this->render_select($args['key'], $args['default'], [
            '1' => __('Top to bottom', 'contabai-bandabou-theme'),
            '2' => __('Bottom to top', 'contabai-bandabou-theme'),
            '3' => __('Left to right', 'contabai-bandabou-theme'),
            '4' => __('Diagonal', 'contabai-bandabou-theme'),
            '5' => __('Radial', 'contabai-bandabou-theme'),
            '6' => __('Soft fade', 'contabai-bandabou-theme'),
            '7' => __('Dual tone', 'contabai-bandabou-theme'),
            '8' => __('Three stop', 'contabai-bandabou-theme'),
            '9' => __('Angled soft', 'contabai-bandabou-theme'),
            '10' => __('Mesh subtle', 'contabai-bandabou-theme'),
        ]);
    }

    public function render_field_media(array $args): void
    {
        $key = $args['key'];
        $value = get_option($key, '');
        $id = 'media-' . $key;
        ?>
        <div>
            <input type="text" name="<?php echo esc_attr($key); ?>" id="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" class="regular-text" readonly>
            <div class="contabai-media-actions">
                <button type="button" class="button" data-media-btn="<?php echo esc_attr($id); ?>"><?php esc_html_e('Choose file', 'contabai-bandabou-theme'); ?></button>
                <button type="button" class="button" data-media-remove="<?php echo esc_attr($id); ?>"><?php esc_html_e('Remove', 'contabai-bandabou-theme'); ?></button>
            </div>
            <?php if ($value) : ?>
                <img src="<?php echo esc_url($value); ?>" alt="" class="contabai-media-preview">
            <?php endif; ?>
            <?php if (($args['description'] ?? '') !== '') : ?>
                <p class="description"><?php echo esc_html($args['description']); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    public function render_field_number(array $args): void
    {
        $value = get_option($args['key'], $args['default']);
        ?>
        <input type="number" name="<?php echo esc_attr($args['key']); ?>" value="<?php echo esc_attr($value); ?>" min="16" max="80" step="1" class="small-text">
        <?php
    }

    public function render_field_bg_repeat(array $args): void
    {
        $this->render_select($args['key'], $args['default'], [
            'no-repeat' => __('No repeat', 'contabai-bandabou-theme'),
            'repeat' => __('Repeat all', 'contabai-bandabou-theme'),
            'repeat-x' => __('Repeat horizontal', 'contabai-bandabou-theme'),
            'repeat-y' => __('Repeat vertical', 'contabai-bandabou-theme'),
        ]);
    }

    public function render_field_bg_size(array $args): void
    {
        $this->render_select($args['key'], $args['default'], [
            'cover' => __('Cover', 'contabai-bandabou-theme'),
            'contain' => __('Contain', 'contabai-bandabou-theme'),
            'auto' => __('Auto', 'contabai-bandabou-theme'),
            '100%' => __('100% width', 'contabai-bandabou-theme'),
        ]);
    }

    public function render_field_bg_position(array $args): void
    {
        $this->render_select($args['key'], $args['default'], [
            'center center' => __('Center', 'contabai-bandabou-theme'),
            'top center' => __('Top center', 'contabai-bandabou-theme'),
            'bottom center' => __('Bottom center', 'contabai-bandabou-theme'),
            'left center' => __('Left center', 'contabai-bandabou-theme'),
            'right center' => __('Right center', 'contabai-bandabou-theme'),
            'top left' => __('Top left', 'contabai-bandabou-theme'),
            'top right' => __('Top right', 'contabai-bandabou-theme'),
            'bottom left' => __('Bottom left', 'contabai-bandabou-theme'),
            'bottom right' => __('Bottom right', 'contabai-bandabou-theme'),
        ]);
    }

    private function get_gradient_css(string $preset, string $color): string
    {
        $darker = 'color-mix(in srgb, ' . $color . ' 70%, #000)';
        $lighter = 'color-mix(in srgb, ' . $color . ' 70%, #fff)';
        $complement = 'color-mix(in srgb, ' . $color . ' 50%, #666)';

        $gradients = [
            '1' => 'linear-gradient(to bottom, ' . $color . ', ' . $darker . ')',
            '2' => 'linear-gradient(to top, ' . $color . ', ' . $darker . ')',
            '3' => 'linear-gradient(to right, ' . $color . ', ' . $darker . ')',
            '4' => 'linear-gradient(135deg, ' . $color . ', ' . $darker . ')',
            '5' => 'radial-gradient(circle, ' . $color . ', ' . $darker . ')',
            '6' => 'linear-gradient(to bottom, ' . $lighter . ', ' . $color . ')',
            '7' => 'linear-gradient(to bottom right, ' . $color . ', ' . $complement . ')',
            '8' => 'linear-gradient(to bottom, ' . $lighter . ', ' . $color . ', ' . $darker . ')',
            '9' => 'linear-gradient(160deg, ' . $lighter . ' 0%, ' . $color . ' 50%, ' . $darker . ' 100%)',
            '10' => 'linear-gradient(135deg, ' . $color . ' 0%, ' . $lighter . ' 50%, ' . $color . ' 100%)',
        ];

        return $gradients[$preset] ?? $gradients['1'];
    }

    public function add_theme_styles(): void
    {
        $color = (string) sanitize_hex_color((string) get_option('bandabou_color_schema', '#EE6A3C'));
        $contentOpacity = (int) get_option('bandabou_content_opacity', '100');
        if ($contentOpacity < 10 || $contentOpacity > 100) {
            $contentOpacity = 100;
        }
        $font = $this->sanitize_font((string) get_option('bandabou_font_schema', 'Figtree'));
        $topbarBg = sanitize_hex_color((string) get_option('bandabou_topbar_bg', '#0B5A6E')) ?: '#0B5A6E';
        $topbarText = sanitize_hex_color((string) get_option('bandabou_topbar_text', '#ffffff')) ?: '#ffffff';
        $topbarIcon1 = sanitize_hex_color((string) get_option('bandabou_topbar_col1_icon_color', '#F4C22B')) ?: '#F4C22B';
        $topbarIcon2 = sanitize_hex_color((string) get_option('bandabou_topbar_col2_icon_color', '#F4C22B')) ?: '#F4C22B';
        $topbarIcon3 = sanitize_hex_color((string) get_option('bandabou_topbar_col3_icon_color', '#F4C22B')) ?: '#F4C22B';
        $topbarIcon4 = sanitize_hex_color((string) get_option('bandabou_topbar_col4_icon_color', '#F4C22B')) ?: '#F4C22B';
        $heroEyebrowColor = sanitize_hex_color((string) get_option('bandabou_hero_eyebrow_color', '#F4C22B')) ?: '#F4C22B';
        $heroHeadingColor = sanitize_hex_color((string) get_option('bandabou_hero_heading_color', '#ffffff')) ?: '#ffffff';
        $heroSubColor = sanitize_hex_color((string) get_option('bandabou_hero_subheading_color', '#F6F1E6')) ?: '#F6F1E6';
        $overlayColor = sanitize_hex_color((string) get_option('bandabou_hero_overlay_color', '#062636')) ?: '#062636';
        $overlayOpacity = (int) get_option('bandabou_hero_overlay_opacity', '0');
        if ($overlayOpacity < 0 || $overlayOpacity > 100) {
            $overlayOpacity = 0;
        }
        $overlayOpacity = $overlayOpacity / 100;
        $heroBadge1 = sanitize_hex_color((string) get_option('bandabou_hero_badge1_icon_color', '#F4C22B')) ?: '#F4C22B';
        $heroBadge2 = sanitize_hex_color((string) get_option('bandabou_hero_badge2_icon_color', '#F4C22B')) ?: '#F4C22B';
        $heroBadge3 = sanitize_hex_color((string) get_option('bandabou_hero_badge3_icon_color', '#F4C22B')) ?: '#F4C22B';

        $logoHeight = (int) get_option('bandabou_logo_height', 58);
        if ($logoHeight < 16 || $logoHeight > 80) {
            $logoHeight = 58;
        }

        $headingFont = $this->sanitize_font((string) get_option('bandabou_heading_font', 'Cormorant Garamond')) ?: 'Cormorant Garamond';
        $bodyTextColor = sanitize_hex_color((string) get_option('bandabou_body_text_color', '#3A4A52')) ?: '#3A4A52';
        $headingColor = sanitize_hex_color((string) get_option('bandabou_heading_color', '#0B4F6C')) ?: '#0B4F6C';
        $linkColor = sanitize_hex_color((string) get_option('bandabou_link_color', '#0B6E6C')) ?: '#0B6E6C';
        $eyebrowColor = sanitize_hex_color((string) get_option('bandabou_eyebrow_color', '#0B6E6C')) ?: '#0B6E6C';

        $baseFontSize = (string) get_option('bandabou_base_font_size', '16');
        if (! in_array($baseFontSize, ['14', '15', '16', '17', '18'], true)) {
            $baseFontSize = '16';
        }
        $lineHeight = (string) get_option('bandabou_base_line_height', '1.6');
        if (! in_array($lineHeight, ['1.4', '1.5', '1.6', '1.75'], true)) {
            $lineHeight = '1.6';
        }
        $paragraphMap = ['tight' => '0.75rem', 'normal' => '1rem', 'relaxed' => '1.5rem'];
        $paragraphSpacing = $paragraphMap[get_option('bandabou_paragraph_spacing', 'normal')] ?? '1rem';
        $headingWeight = (string) get_option('bandabou_heading_weight', '600');
        if (! in_array($headingWeight, ['500', '600', '700', '800'], true)) {
            $headingWeight = '600';
        }
        $headingLineHeight = (string) get_option('bandabou_heading_line_height', '1.1');
        if (! in_array($headingLineHeight, ['1.1', '1.2', '1.3'], true)) {
            $headingLineHeight = '1.1';
        }
        $linkUnderline = get_option('bandabou_link_underline', 'always');
        if (! in_array($linkUnderline, ['always', 'hover', 'none'], true)) {
            $linkUnderline = 'always';
        }
        if ($linkUnderline === 'always') {
            $underlineCss = '.entry-content a, .contabai-link { text-decoration: underline; }';
        } elseif ($linkUnderline === 'none') {
            $underlineCss = '.entry-content a, .contabai-link { text-decoration: none; }';
        } else {
            $underlineCss = '.entry-content a, .contabai-link { text-decoration: none; } .entry-content a:hover, .contabai-link:hover { text-decoration: underline; }';
        }

        $families = array_unique(array_filter([$font, $headingFont]));
        $fontFaces = '';
        foreach ($families as $family) {
            $slug = sanitize_title($family);
            $dir = get_template_directory() . '/assets/fonts/' . $slug;
            $uri = get_template_directory_uri() . '/assets/fonts/' . $slug;
            foreach (glob($dir . '/*.woff2') ?: [] as $file) {
                $weight = (int) basename($file, '.woff2');
                if ($weight < 100 || $weight > 900) {
                    continue;
                }
                $fontFaces .= '@font-face{font-family:"' . $family . '";font-style:normal;font-weight:' . $weight . ';font-display:swap;src:url("' . esc_url($uri . '/' . $weight . '.woff2') . '") format("woff2");}';
            }
        }
        if ($fontFaces !== '') {
            echo '<style id="contabai-fonts">' . $fontFaces . '</style>';
        }
        $bgType = get_option('bandabou_bg_type', 'color');
        $bgColor = (string) sanitize_hex_color((string) get_option('bandabou_bg_color', '#FBF5EA'));

        if ($bgType === 'gradient') {
            $bgPreset = get_option('bandabou_bg_gradient', '1');
            $bgCss = 'background: ' . $this->get_gradient_css($bgPreset, $bgColor) . '; background-attachment: fixed;';
        } elseif ($bgType === 'image') {
            $bgImage = esc_url(get_option('bandabou_bg_image', ''));

            $bgRepeat = get_option('bandabou_bg_repeat', 'no-repeat');
            if (! in_array($bgRepeat, ['no-repeat', 'repeat', 'repeat-x', 'repeat-y'], true)) {
                $bgRepeat = 'no-repeat';
            }

            $bgSize = get_option('bandabou_bg_size', 'cover');
            if (! in_array($bgSize, ['cover', 'contain', 'auto', '100%'], true)) {
                $bgSize = 'cover';
            }

            $bgPosition = get_option('bandabou_bg_position', 'center center');
            if (! in_array($bgPosition, ['center center', 'top center', 'bottom center', 'left center', 'right center', 'top left', 'top right', 'bottom left', 'bottom right'], true)) {
                $bgPosition = 'center center';
            }
            if ($bgImage) {
                $bgCss = 'background: ' . $bgColor . ' url(\'' . $bgImage . '\') ' . $bgRepeat . ' ' . $bgPosition . '; background-size: ' . $bgSize . '; background-attachment: fixed;';
            } else {
                $bgCss = 'background: ' . $bgColor . ';';
            }
        } else {
            $bgCss = 'background: ' . $bgColor . ';';
        }

        echo '<style>:root {
            --theme-color: ' . $color . ';
            --content-opacity: ' . $contentOpacity . '%;
            --theme-font: "' . $font . '", sans-serif;
            --topbar-bg: ' . $topbarBg . ';
            --topbar-text: ' . $topbarText . ';
            --topbar-icon-1-color: ' . $topbarIcon1 . ';
            --topbar-icon-2-color: ' . $topbarIcon2 . ';
            --topbar-icon-3-color: ' . $topbarIcon3 . ';
            --topbar-icon-4-color: ' . $topbarIcon4 . ';
            --hero-eyebrow-color: ' . $heroEyebrowColor . ';
            --hero-heading-color: ' . $heroHeadingColor . ';
            --hero-subheading-color: ' . $heroSubColor . ';
            --hero-overlay-color: ' . $overlayColor . ';
            --hero-overlay-opacity: ' . $overlayOpacity . ';
            --hero-badge-1-color: ' . $heroBadge1 . ';
            --hero-badge-2-color: ' . $heroBadge2 . ';
            --hero-badge-3-color: ' . $heroBadge3 . ';
            --theme-heading-font: "' . $headingFont . '", sans-serif;
            --content-font-size: ' . $baseFontSize . 'px;
            --content-line-height: ' . $lineHeight . ';
            --content-text-color: ' . $bodyTextColor . ';
            --content-paragraph-spacing: ' . $paragraphSpacing . ';
            --heading-weight: ' . $headingWeight . ';
            --heading-line-height: ' . $headingLineHeight . ';
            --heading-color: ' . $headingColor . ';
            --eyebrow-color: ' . $eyebrowColor . ';
            --content-link-color: ' . $linkColor . ';
            --logo-height: ' . $logoHeight . 'px;
        }
        * { font-family: var(--theme-font), system-ui, sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: var(--theme-heading-font, var(--theme-font)), system-ui, sans-serif; }
        html { font-size: var(--content-font-size, 16px); }
        body { ' . $bgCss . ' min-height: 100vh; }
        .text-primary-500, .text-primary-600, .text-primary-700 { color: var(--theme-color) !important; }
        .bg-primary-500 { background-color: var(--theme-color) !important; }
        .bg-primary-50 { background-color: color-mix(in srgb, var(--theme-color) 8%, #fff) !important; }
        .border-primary-500 { border-color: var(--theme-color) !important; }
        ' . $underlineCss . '
        </style>';
    }
}
