<?php

namespace BandabouTheme;

class Languages
{
    private const CODES = ['en', 'nl', 'de', 'es', 'fr', 'pt'];

    public static function links(): array
    {
        $base     = rtrim(is_multisite() ? network_home_url() : home_url(), '/');
        $homePath = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);
        $request  = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $path     = str_starts_with($request, $homePath) ? substr($request, strlen($homePath)) : '';
        if (is_404() || is_singular('post') || is_archive() || is_home()) {
            $path = '';
        }
        $current = substr(get_locale(), 0, 2);

        $links = [];
        foreach (self::CODES as $code) {
            $links[] = [
                'code'    => strtoupper($code),
                'url'     => $base . '/' . ($code === 'en' ? '' : $code . '/') . $path,
                'current' => $code === $current,
            ];
        }

        return $links;
    }
}
