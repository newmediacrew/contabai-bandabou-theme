<?php

namespace BandabouTheme;

class Languages
{
    public static function links(): array
    {
        if (! is_multisite()) {
            return [];
        }

        $homePath = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);
        $request  = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $path     = str_starts_with($request, $homePath) ? substr($request, strlen($homePath)) : '';
        $samePath = ! is_404() && ! is_singular('post') && ! is_archive() && ! is_home();
        $pageUri  = is_page() && ! is_front_page() ? get_page_uri(get_queried_object_id()) : '';

        $links = [];
        foreach (get_sites(['public' => 1, 'archived' => 0, 'deleted' => 0, 'spam' => 0, 'number' => 0, 'fields' => 'ids']) as $siteId) {
            $code = self::code((int) $siteId);
            if (isset($links[$code])) {
                continue;
            }

            $twin = $samePath;
            if ($twin && $pageUri !== '') {
                switch_to_blog((int) $siteId);
                $page = get_page_by_path($pageUri);
                restore_current_blog();
                $twin = $page && $page->post_status === 'publish';
            }

            $links[$code] = [
                'code'    => strtoupper($code),
                'url'     => get_home_url((int) $siteId, '/' . ($twin ? $path : '')),
                'current' => (int) $siteId === get_current_blog_id(),
            ];
        }

        return count($links) > 1 ? array_values($links) : [];
    }

    private static function code(int $siteId): string
    {
        $locale = get_blog_option($siteId, 'WPLANG');
        if ($locale === false) {
            $locale = get_site_option('WPLANG');
        }

        return $locale ? strtolower(substr((string) $locale, 0, 2)) : 'en';
    }
}
