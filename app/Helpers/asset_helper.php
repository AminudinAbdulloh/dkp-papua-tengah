<?php

declare(strict_types=1);

if (! function_exists('asset_version')) {
    /**
     * Versi cache-bust untuk file statis di folder public/.
     * Development/testing: filemtime() per file.
     * Production: ASSET_VERSION dari .env (naikkan tiap deploy).
     */
    function asset_version(string $path): string
    {
        static $productionVersion = null;
        static $mtimeCache = [];

        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (ENVIRONMENT === 'production') {
            if ($productionVersion === null) {
                $productionVersion = trim((string) env('ASSET_VERSION', ''));
            }

            if ($productionVersion !== '') {
                return $productionVersion;
            }
        }

        if (isset($mtimeCache[$path])) {
            return $mtimeCache[$path];
        }

        $fullPath = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $path);
        if (is_file($fullPath)) {
            return $mtimeCache[$path] = (string) filemtime($fullPath);
        }

        return $mtimeCache[$path] = (string) time();
    }
}

if (! function_exists('asset')) {
    /**
     * URL file statis (css/js/font) dengan query versi untuk cache busting.
     */
    function asset(string $path): string
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');

        return base_url($path) . '?v=' . rawurlencode(asset_version($path));
    }
}
