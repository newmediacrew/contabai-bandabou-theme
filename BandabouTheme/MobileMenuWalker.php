<?php

namespace BandabouTheme;

class MobileMenuWalker extends \Walker_Nav_Menu
{
    private bool $cards = false;

    // A submenu shows photo cards only when every item in it has a picture, so one menu never mixes cards and text.
    public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output)
    {
        if ($depth === 0 && ! empty($children_elements[$element->ID])) {
            $this->cards = true;
            foreach ($children_elements[$element->ID] as $child) {
                if ($this->image($child) === '') {
                    $this->cards = false;
                    break;
                }
            }
        }
        parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }

    public function start_lvl(&$output, $depth = 0, $args = null)
    {
        $list = $this->cards ? 'grid grid-cols-3 gap-2.5' : 'flex flex-wrap gap-2';
        $output .= '<ul x-show="open" x-cloak x-transition.opacity.duration.200ms class="m-0 list-none p-0 pb-4 pt-1.5 ' . $list . '">';
    }

    public function end_lvl(&$output, $depth = 0, $args = null)
    {
        $output .= '</ul>';
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        $hasChildren = in_array('menu-item-has-children', $classes, true);
        $current = in_array('current-menu-item', $classes, true);
        $title = esc_html(apply_filters('the_title', $item->title, $item->ID));
        $href = ! empty($item->url) ? ' href="' . esc_url($item->url) . '"' : '';

        if ($depth > 0) {
            $output .= '<li>';
            if ($this->cards) {
                $output .= '<a' . $href . ' class="relative block h-32 overflow-hidden rounded-2xl no-underline">'
                    . '<img src="' . esc_url($this->image($item)) . '" alt="" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover">'
                    . '<span class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black/70"></span>'
                    . '<span class="absolute bottom-2 left-2.5 right-2 truncate text-sm font-semibold text-white">' . $title . '</span>'
                    . '</a>';
            } else {
                $output .= '<a' . $href . ' class="inline-flex items-center rounded-full border border-white/35 bg-white/10 px-4 py-2 text-sm font-semibold text-white no-underline backdrop-blur-sm transition hover:bg-white/20">' . $title . '</a>';
            }
            return;
        }

        $big = 'contabai-display text-4xl font-semibold leading-tight';
        if ($hasChildren) {
            $output .= '<li x-data="{ open: false }">'
                . '<button type="button" x-on:click="open = ! open" x-bind:aria-expanded="open" class="flex w-full items-center justify-between py-2.5 text-left text-white/85 ' . $big . '">'
                . $title
                . '<span class="transition-transform duration-200" x-bind:class="open ? \'rotate-180\' : \'\'">' . Heroicon::outline('chevron-down', 'w-6 h-6') . '</span>'
                . '</button>';
            return;
        }

        $output .= '<li><a' . $href . ($current ? ' aria-current="page"' : '') . ' class="flex items-center gap-3 py-2.5 no-underline transition hover:text-white ' . $big . ' ' . ($current ? 'text-white' : 'text-white/85') . '">'
            . $title
            . ($current ? '<span class="h-2 w-2 rounded-full bg-[color:var(--theme-color,#EE6A3C)]"></span>' : '')
            . '</a>';
    }

    public function end_el(&$output, $item, $depth = 0, $args = null)
    {
        $output .= '</li>';
    }

    private function image($item): string
    {
        $postId = $item->type === 'post_type' ? (int) $item->object_id : url_to_postid((string) $item->url);

        return $postId ? (string) get_the_post_thumbnail_url($postId, 'medium') : '';
    }
}
