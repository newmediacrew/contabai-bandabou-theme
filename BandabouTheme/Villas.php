<?php

namespace BandabouTheme;

class Villas
{
    public static function random(int $count): array
    {
        if (! class_exists('Contabai\\Controllers\\ListingController')) {
            return [];
        }

        $scope = \Contabai\Controllers\ListingController::hostScope();
        if ($scope['mode'] === 'empty') {
            return [];
        }

        $query = ['count' => $count] + ($scope['mode'] === 'host' ? ['host' => $scope['host']] : []);
        [$randomBody] = \Contabai\Helper::api('GET', '/sanctum/listings/random?' . http_build_query($query));

        return (array) ($randomBody['data']['listings'] ?? []);
    }

    public static function areas(): array
    {
        if (! class_exists('Contabai\\Controllers\\ListingController')) {
            return [];
        }

        $scope = \Contabai\Controllers\ListingController::hostScope();
        if ($scope['mode'] === 'empty') {
            return [];
        }

        $areas = [];
        $page = 1;
        do {
            $query = ['per_page' => 50, 'page' => $page] + ($scope['mode'] === 'host' ? ['host' => $scope['host']] : []);
            [$body] = \Contabai\Helper::api('GET', '/sanctum/listings?' . http_build_query($query));
            foreach ((array) ($body['data']['listings'] ?? []) as $listing) {
                $key = (string) ($listing['area'] ?? '');
                if ($key === '') {
                    continue;
                }
                $areas[$key] ??= [
                    'country' => (string) ($listing['country'] ?? ''),
                    'region'  => (string) ($listing['region'] ?? ''),
                    'area'    => $key,
                    'name'    => (string) ($listing['area_name'] ?? $key),
                    'count'   => 0,
                ];
                $areas[$key]['count']++;
            }
            $lastPage = (int) ($body['data']['last_page'] ?? 1);
            $page++;
        } while ($page <= $lastPage);

        uasort($areas, fn ($a, $b) => ($b['count'] <=> $a['count']) ?: strcasecmp($a['name'], $b['name']));

        return array_values($areas);
    }
}
