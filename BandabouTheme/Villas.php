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
}
