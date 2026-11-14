<?php

declare(strict_types=1);

require_once __DIR__ . '/url.php';

/**
 * Renderiza AVIF/WebP responsivos y conserva el JPG original como fallback.
 *
 * @param array{
 *   alt: string,
 *   class?: string,
 *   width?: int,
 *   height?: int,
 *   sizes?: string,
 *   priority?: bool,
 *   loading?: 'eager'|'lazy'
 * } $options
 */
function gp_responsive_picture(string $source, array $options): string
{
    $source = ltrim($source, '/');
    $manifestPath = dirname(__DIR__, 2) . '/public/assets/img/responsive-manifest.json';
    static $manifest = null;
    if ($manifest === null) {
        $decoded = is_file($manifestPath) ? json_decode((string) file_get_contents($manifestPath), true) : [];
        $manifest = is_array($decoded) ? $decoded : [];
    }

    $entry = $manifest[$source] ?? null;
    $sizes = (string) ($options['sizes'] ?? '100vw');
    $priority = (bool) ($options['priority'] ?? false);
    $loading = (string) ($options['loading'] ?? ($priority ? 'eager' : 'lazy'));
    $attributes = [
        'src="' . htmlspecialchars(asset($source), ENT_QUOTES, 'UTF-8') . '"',
        'alt="' . htmlspecialchars((string) $options['alt'], ENT_QUOTES, 'UTF-8') . '"',
        'class="' . htmlspecialchars((string) ($options['class'] ?? ''), ENT_QUOTES, 'UTF-8') . '"',
        'loading="' . ($loading === 'eager' ? 'eager' : 'lazy') . '"',
        'decoding="async"',
    ];
    if (!empty($options['width'])) {
        $attributes[] = 'width="' . (int) $options['width'] . '"';
    }
    if (!empty($options['height'])) {
        $attributes[] = 'height="' . (int) $options['height'] . '"';
    }
    if ($priority) {
        $attributes[] = 'fetchpriority="high"';
    }

    $sources = '';
    if (is_array($entry) && isset($entry['variants']) && is_array($entry['variants'])) {
        foreach (['avif' => 'image/avif', 'webp' => 'image/webp'] as $format => $mime) {
            $variants = $entry['variants'][$format] ?? [];
            if (!is_array($variants) || $variants === []) {
                continue;
            }
            ksort($variants, SORT_NUMERIC);
            $srcset = [];
            foreach ($variants as $width => $path) {
                $srcset[] = htmlspecialchars(asset((string) $path), ENT_QUOTES, 'UTF-8') . ' ' . (int) $width . 'w';
            }
            $sources .= '<source type="' . $mime . '" srcset="' . implode(', ', $srcset)
                . '" sizes="' . htmlspecialchars($sizes, ENT_QUOTES, 'UTF-8') . '">';
        }
    }

    return '<picture class="gp-picture">' . $sources . '<img ' . implode(' ', $attributes) . '></picture>';
}
