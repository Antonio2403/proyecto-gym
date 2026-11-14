<?php

declare(strict_types=1);

require_once __DIR__ . '/url.php';
require_once __DIR__ . '/horario_centro.php';

/**
 * Páginas que sí deben salir en Google. El resto lleva noindex.
 *
 * @return array<string, array{title: string, description: string}>
 */
function gp_seo_publicas(): array
{
    $horario = 'Lunes a viernes de 9:00 a 21:00 y sábados de 10:00 a 14:00';

    return [
        '/' => [
            'title' => 'Spartum, gimnasio en Lepe',
            'description' => 'Gimnasio en Lepe (Huelva), en Calle Camelia nº 16. Clases guiadas, zona de pesas y fisioterapia. ' . $horario . '.',
        ],
        '/quienes-somos' => [
            'title' => 'Quiénes somos | Spartum, gimnasio en Lepe',
            'description' => 'Spartum es el gimnasio de Lepe: entrenadores, salas de fuerza y clases en grupo en Calle Camelia nº 16.',
        ],
        '/contacto' => [
            'title' => 'Contacto | Spartum, gimnasio en Lepe',
            'description' => 'Contacta con Spartum, gimnasio en Lepe (Huelva). Calle Camelia nº 16. ' . $horario . '.',
        ],
        '/pago' => [
            'title' => 'Planes | Spartum, gimnasio en Lepe',
            'description' => 'Planes del gimnasio Spartum en Lepe: acceso a sala, clases guiadas y fisioterapia. Elige tu suscripción.',
        ],
    ];
}

function gp_seo_path_actual(): string
{
    $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    $path = is_string($path) && $path !== '' ? $path : '/';
    $base = app_base_path();
    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base)) ?: '/';
    }
    if ($path !== '/' && str_ends_with($path, '/')) {
        $path = rtrim($path, '/');
    }

    return $path === '' ? '/' : $path;
}

/**
 * @return array{title: string, description: string, robots: string, canonical: string, image: string}
 */
function gp_seo_actual(): array
{
    $path = gp_seo_path_actual();
    $publicas = gp_seo_publicas();
    if ($path === '/' || $path === '/inicio') {
        $ficha = $publicas['/'];
        $indexable = $path === '/';
        $canonicalPath = '/';
    } elseif (isset($publicas[$path])) {
        $ficha = $publicas[$path];
        $indexable = true;
        $canonicalPath = $path;
    } else {
        $ficha = [
            'title' => 'Spartum',
            'description' => 'Spartum, gimnasio en Lepe (Huelva).',
        ];
        $indexable = false;
        $canonicalPath = $path;
    }

    return [
        'title' => $ficha['title'],
        'description' => $ficha['description'],
        'robots' => $indexable ? 'index, follow' : 'noindex, nofollow',
        'canonical' => app_url_absolute($canonicalPath),
        'image' => app_url_absolute('/public/assets/img/pexels/hero-spartum-2026.jpg'),
    ];
}

function gp_seo_json_ld(): string
{
    $seo = gp_seo_actual();
    if ($seo['robots'] !== 'index, follow') {
        return '';
    }

    $businessId = app_url_absolute('/') . '#centro';
    $websiteId = app_url_absolute('/') . '#website';
    $business = [
        '@type' => ['HealthClub', 'ExerciseGym'],
        '@id' => $businessId,
        'name' => gp_centro_nombre(),
        'description' => gp_seo_publicas()['/']['description'],
        'url' => app_url_absolute('/'),
        'image' => $seo['image'],
        'hasMap' => gp_horario_centro_maps_link_url(),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Calle Camelia Nº 16',
            'postalCode' => gp_centro_codigo_postal(),
            'addressLocality' => 'Lepe',
            'addressRegion' => 'Huelva',
            'addressCountry' => 'ES',
        ],
        'openingHoursSpecification' => [
            [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'opens' => '09:00',
                'closes' => '21:00',
            ],
            [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => 'Saturday',
                'opens' => '10:00',
                'closes' => '14:00',
            ],
        ],
    ];
    if (gp_centro_telefono() !== '') {
        $business['telephone'] = gp_centro_telefono();
    }
    if (gp_centro_email() !== '') {
        $business['email'] = gp_centro_email();
    }
    if (gp_centro_redes() !== []) {
        $business['sameAs'] = gp_centro_redes();
    }

    $datos = [
        '@context' => 'https://schema.org',
        '@graph' => [
            $business,
            [
                '@type' => 'WebSite',
                '@id' => $websiteId,
                'name' => gp_centro_nombre(),
                'url' => app_url_absolute('/'),
                'inLanguage' => 'es-ES',
                'publisher' => ['@id' => $businessId],
            ],
            [
                '@type' => 'WebPage',
                '@id' => $seo['canonical'] . '#webpage',
                'name' => $seo['title'],
                'description' => $seo['description'],
                'url' => $seo['canonical'],
                'inLanguage' => 'es-ES',
                'isPartOf' => ['@id' => $websiteId],
                'about' => ['@id' => $businessId],
            ],
        ],
    ];

    $json = json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);

    return is_string($json) ? $json : '';
}

function gp_seo_robots_txt(): string
{
    $sitemap = app_url_absolute('/sitemap.xml');

    return implode("\n", [
        'User-agent: *',
        'Allow: /',
        'Disallow: /admin',
        'Disallow: /login',
        'Disallow: /inicio',
        'Disallow: /inicioAdmin',
        'Disallow: /inicioUsuario',
        'Disallow: /inicioMonitor',
        'Disallow: /usuario/',
        'Disallow: /cuenta/',
        'Disallow: /fisio',
        'Disallow: /monitor/',
        'Disallow: /clientes/',
        'Disallow: /pago/exito',
        'Disallow: /pago/cancelado',
        'Disallow: /ticket',
        'Disallow: /recuperar-contrasena',
        'Disallow: /restablecer-contrasena',
        'Disallow: /darse-de-baja',
        'Disallow: /reactivar-cuenta',
        'Disallow: /sesion/',
        'Disallow: /logout',
        'Disallow: /confirmar-cuenta',
        'Disallow: /no-recuerdo-mi-cuenta',
        'Disallow: /no-recuerdo-quien-soy',
        '',
        'Sitemap: ' . $sitemap,
        '',
    ]);
}

function gp_seo_sitemap_xml(): string
{
    $urls = array_keys(gp_seo_publicas());
    $lastModified = gmdate('Y-m-d', max(
        filemtime(__DIR__ . '/../../app/vistas/frontend/inicio.php') ?: 0,
        filemtime(__FILE__) ?: 0
    ));
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($urls as $path) {
        $priority = $path === '/' ? '1.0' : '0.7';
        $frequency = $path === '/' ? 'weekly' : 'monthly';
        $xml .= '  <url><loc>' . htmlspecialchars(app_url_absolute($path), ENT_XML1)
            . '</loc><lastmod>' . $lastModified . '</lastmod><changefreq>' . $frequency
            . '</changefreq><priority>' . $priority . '</priority></url>' . "\n";
    }
    $xml .= '</urlset>' . "\n";

    return $xml;
}
