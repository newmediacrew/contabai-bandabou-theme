<?php

namespace BandabouTheme;

class PhotoHero
{
    public static function image(int $postId): string
    {
        if ($postId) {
            foreach (array_merge([$postId], get_post_ancestors($postId)) as $sourceId) {
                if (has_post_thumbnail($sourceId)) {
                    return (string) get_the_post_thumbnail_url($sourceId, 'full');
                }
            }
        }
        $files = glob(get_theme_file_path('assets/img/hero/bandabou-*.webp')) ?: [];
        if ($files === []) {
            return '';
        }

        return get_theme_file_uri('assets/img/hero/' . basename($files[array_rand($files)]));
    }

    public static function alt(int $postId): string
    {
        if ($postId) {
            foreach (array_merge([$postId], get_post_ancestors($postId)) as $sourceId) {
                if (has_post_thumbnail($sourceId)) {
                    return (string) get_post_meta((int) get_post_thumbnail_id($sourceId), '_wp_attachment_image_alt', true);
                }
            }
        }

        return '';
    }
}
