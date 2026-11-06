<?php

namespace BandabouTheme;

class DropdownWalker extends \Walker_Nav_Menu
{
    public function start_lvl(&$output, $depth = 0, $args = null)
    {
        $output .= '<ul class="sub-menu" x-show="dropdownOpen" x-cloak'
            . ' x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"'
            . ' x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">';
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        $has_children = in_array('menu-item-has-children', $classes, true);
        $class_str = esc_attr(implode(' ', array_filter(array_unique($classes))));

        $li = ' class="' . $class_str . '"';
        if ($has_children) {
            $li .= ' x-data="{ dropdownOpen: false }" x-on:click.outside="dropdownOpen = false" x-on:keydown.escape="dropdownOpen = false"';
        }
        $output .= '<li' . $li . '>';

        $a = ! empty($item->url) ? ' href="' . esc_url($item->url) . '"' : '';
        if ($has_children) {
            $a .= ' x-on:click.prevent="dropdownOpen = !dropdownOpen" x-bind:aria-expanded="dropdownOpen"';
        }

        $title = apply_filters('the_title', $item->title, $item->ID);
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $output .= '<a' . $a . '>' . $title . '</a>';
    }
}
