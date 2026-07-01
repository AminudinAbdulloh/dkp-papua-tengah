<?php

declare(strict_types=1);

if (! function_exists('sanitize_inline_style_attribute')) {
    /**
     * Membolehkan properti CSS terbatas pada atribut style (warna, font dasar, rata teks, indent aman).
     */
    function sanitize_inline_style_attribute(string $raw): string
    {
        $raw = html_entity_decode(trim($raw), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($raw === '' || preg_match('/\burl\s*\(|expression\s*\(|@import|javascript\s*:/i', $raw)) {
            return '';
        }

        $allowed = [
            'color'              => 'color',
            'background-color'   => 'color',
            'text-align'         => 'text-align',
            'font-size'          => 'font-size',
            'font-family'        => 'font-family',
            'line-height'        => 'line-height',
            'margin-left'        => 'indent',
            'padding-left'       => 'indent',
            'text-indent'        => 'indent',
        ];

        $out = [];
        foreach (explode(';', $raw) as $piece) {
            $piece = trim($piece);
            if ($piece === '' || ! str_contains($piece, ':')) {
                continue;
            }
            [$prop, $value] = array_map('trim', explode(':', $piece, 2));
            $propLower = strtolower($prop);
            if (! isset($allowed[$propLower])) {
                continue;
            }

            $valueLower = strtolower($value);
            if (str_contains($valueLower, 'url(') || str_contains($valueLower, 'expression(')) {
                continue;
            }

            if ($propLower === 'text-align' && in_array($valueLower, ['left', 'right', 'center', 'justify'], true)) {
                $out[] = 'text-align:' . $valueLower;
                continue;
            }
            if ($propLower === 'font-size' && preg_match('/^[\d.]+\s*(px|pt|em|rem)$/i', $value) === 1) {
                $out[] = 'font-size:' . $value;
                continue;
            }
            if ($propLower === 'line-height' && (
                preg_match('/^normal$/i', $value) === 1
                || preg_match('/^[\d.]+\s*(px|pt|em|rem|%)$/i', $value) === 1
                || preg_match('/^[\d.]+%$/i', $value) === 1
                || preg_match('/^[\d.]+$/', $value) === 1
            )) {
                $out[] = 'line-height:' . $value;
                continue;
            }
            if ($propLower === 'font-family' && preg_match('/^[\pL\pM0-9\s,"\'-]+$/u', $value) === 1 && strlen($value) < 400) {
                $out[] = 'font-family:' . $value;
                continue;
            }
            if (in_array($propLower, ['margin-left', 'padding-left', 'text-indent'], true)) {
                if (preg_match('/^0$/', $value) === 1 || preg_match('/^[\d.]+\s*(px|pt|em|rem|%)$/i', $value) === 1) {
                    $numeric = (float) preg_replace('/[^0-9.]/', '', $value);
                    if ($numeric <= 120) {
                        $out[] = $propLower . ':' . $value;
                    }
                }
                continue;
            }
            if (in_array($propLower, ['color', 'background-color'], true)) {
                if (preg_match('/url\s*\(/i', $value)) {
                    continue;
                }
                if (
                    preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $value) === 1
                    || preg_match('/^rgba?\(\s*[\d\s.,%]+\)$/i', $value) === 1
                    || preg_match('/^hsla?\(\s*[\d\s.,%\-]+\)$/i', $value) === 1
                    || preg_match('/^[a-z][-a-z]*$/i', $value) === 1
                ) {
                    $out[] = $propLower . ':' . $value;
                }
            }
        }

        return implode('; ', $out);
    }
}

if (! function_exists('sanitize_style_attributes_in_html')) {
    function sanitize_style_attributes_in_html(string $html): string
    {
        return preg_replace_callback(
            '/\sstyle\s*=\s*("([^"]*)"|\'([^\']*)\')/i',
            static function (array $m): string {
                $raw = $m[2] !== '' ? $m[2] : ($m[3] ?? '');
                $clean = sanitize_inline_style_attribute($raw);

                return $clean === '' ? '' : ' style="' . esc($clean, 'attr') . '"';
            },
            $html
        ) ?? $html;
    }
}

if (! function_exists('safe_admin_html')) {
    /**
     * Membersihkan HTML dari panel admin sebelum disimpan atau ditampilkan.
     */
    function safe_admin_html(string $html): string
    {
        $html = preg_replace('/<\s*(script|style)\b[^>]*>[\s\S]*?<\s*\/\s*\1\s*>/i', '', $html) ?? $html;
        $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = sanitize_style_attributes_in_html($html);

        $allowed = '<p><div><span><br><strong><b><em><i><u><sub><sup><s><strike><del><ul><ol><li><h2><h3><h4><h5><h6>'
            . '<blockquote><a><hr><pre><code><table><thead><tbody><tfoot><tr><th><td><caption><col><colgroup>'
            . '<img><figure><figcaption><iframe>';
        $html = strip_tags($html, $allowed);

        $html = preg_replace_callback(
            '/<img\s+[^>]*>/i',
            static function (array $m): string {
                $tag = $m[0];
                if (! preg_match('/\bsrc\s*=\s*("([^"]*)"|\'([^\']*)\')/i', $tag, $sm)) {
                    return '';
                }
                $src = trim($sm[1], '"\'');
                $src = html_entity_decode($src, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                if ($src === '' || preg_match('/^\s*javascript:/i', $src) || str_starts_with(strtolower($src), 'data:')) {
                    return '';
                }

                // Normalisasi path gambar editor ke bentuk kanonik /uploads/editor/namafile.
                // Ini memastikan path konsisten di semua environment (lokal XAMPP maupun Hostinger)
                // sehingga fungsi cleanup_unused_editor_uploads() tidak salah menghapus gambar.
                if (preg_match('#(?:^|/)uploads/editor/([a-zA-Z0-9._-]+\.(?:png|jpe?g|webp|gif))$#i', $src, $em)) {
                    $src = '/uploads/editor/' . $em[1];
                }

                if (! preg_match('#^(https?:)?//#i', $src) && ! str_starts_with($src, '/')) {
                    return '';
                }

                $alt = '';
                if (preg_match('/\balt\s*=\s*("([^"]*)"|\'([^\']*)\')/i', $tag, $am)) {
                    $alt = esc(html_entity_decode(trim($am[1], '"\''), ENT_QUOTES | ENT_HTML5, 'UTF-8'), 'attr');
                }

                $width = null;
                if (preg_match('/\bwidth\s*=\s*("([^"]*)"|\'([^\']*)\')/i', $tag, $wm)) {
                    $raw = trim($wm[2] !== '' ? $wm[2] : ($wm[3] ?? ''));
                    if (preg_match('/^\d{1,4}$/', $raw) === 1) {
                        $w = (int) $raw;
                        if ($w >= 1 && $w <= 2400) {
                            $width = (string) $w;
                        }
                    }
                }

                $height = null;
                if (preg_match('/\bheight\s*=\s*("([^"]*)"|\'([^\']*)\')/i', $tag, $hm)) {
                    $raw = trim($hm[2] !== '' ? $hm[2] : ($hm[3] ?? ''));
                    if (preg_match('/^\d{1,4}$/', $raw) === 1) {
                        $h = (int) $raw;
                        if ($h >= 1 && $h <= 2400) {
                            $height = (string) $h;
                        }
                    }
                }

                $attrs = ' src="' . esc($src, 'attr') . '" alt="' . $alt . '" loading="lazy"';
                if ($width !== null) {
                    $attrs .= ' width="' . esc($width, 'attr') . '"';
                }
                if ($height !== null) {
                    $attrs .= ' height="' . esc($height, 'attr') . '"';
                }

                return '<img' . $attrs . '>';
            },
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/<iframe\s+[^>]*>/i',
            static function (array $m): string {
                $tag = $m[0];
                if (! preg_match('/\bsrc\s*=\s*("([^"]*)"|\'([^\']*)\')/i', $tag, $sm)) {
                    return '';
                }
                $src = html_entity_decode(trim($sm[1], '"\''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $parts = parse_url($src);
                $host = strtolower($parts['host'] ?? '');
                $allowedHosts = [
                    'www.youtube.com',
                    'youtube.com',
                    'www.youtube-nocookie.com',
                    'youtube-nocookie.com',
                    'player.vimeo.com',
                    'www.google.com',
                    'maps.google.com',
                    'www.google.co.id',
                ];
                $ok = false;
                foreach ($allowedHosts as $h) {
                    if ($host === $h || str_ends_with($host, '.' . $h)) {
                        $ok = true;
                        break;
                    }
                }
                if (! $ok || ! str_starts_with(strtolower($parts['scheme'] ?? ''), 'http')) {
                    return '';
                }

                return '<iframe src="' . esc($src, 'attr') . '" title="Video" loading="lazy" allowfullscreen="true" '
                    . 'referrerpolicy="strict-origin-when-cross-origin"></iframe>';
            },
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/<a\s+[^>]*?href\s*=\s*("([^"]*)"|\'([^\']*)\')[^>]*>/i',
            static function (array $m): string {
                $href = trim($m[1], '"\'');
                $href = html_entity_decode($href, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $hrefLower = strtolower($href);
                if ($hrefLower === '' || str_contains($hrefLower, 'javascript:') || str_starts_with($hrefLower, 'data:')) {
                    return '';
                }
                if (
                    ! preg_match('#^https?://#i', $hrefLower)
                    && ! str_starts_with($hrefLower, '/')
                    && ! str_starts_with($hrefLower, '#')
                    && ! str_starts_with($hrefLower, 'mailto:')
                ) {
                    return '';
                }

                return '<a href="' . esc($href, 'attr') . '" rel="noopener noreferrer">';
            },
            $html
        ) ?? $html;

        $html = preg_replace('/javascript:/i', '', $html) ?? $html;

        return preg_replace('/<div\b[^>]*>/i', '<div>', $html) ?? $html;
    }
}

if (! function_exists('extract_editor_upload_filenames')) {
    /**
     * @return array<string, true> map filename => true
     */
    function extract_editor_upload_filenames(string $html): array
    {
        $out = [];
        if ($html === '') {
            return $out;
        }

        // Regex menangkap nama file dari semua varian path uploads/editor:
        // /uploads/editor/file.jpg, /subdir/uploads/editor/file.jpg, uploads/editor/file.jpg
        if (preg_match_all('#uploads/editor/([a-zA-Z0-9._-]+\.(?:png|jpe?g|webp|gif))#i', $html, $m)) {
            foreach ($m[1] as $name) {
                $out[(string) $name] = true;
            }
        }

        return $out;
    }
}

if (! function_exists('cleanup_unused_editor_uploads')) {
    /**
     * Hapus file upload editor yang tidak lagi direferensikan oleh konten manapun.
     *
     * @param int $graceSeconds Grace period dalam detik. Set 0 untuk hapus seketika.
     *                          Default 3600 aman untuk upload yang belum sempat disimpan.
     */
    function cleanup_unused_editor_uploads(int $graceSeconds = 3600): void
    {
        $dir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'editor';
        if (! is_dir($dir)) {
            return;
        }

        // Kumpulkan semua filename gambar yang masih direferensikan dari SEMUA model
        // yang menyimpan konten editor. Jika ada model baru, tambahkan di sini.
        $used = [];

        // 1. SitePageModel → kolom body (Struktur, Pejabat, Visi Misi, dst.)
        $siteRows = model(\App\Models\SitePageModel::class)->select('body')->findAll();
        foreach ($siteRows as $row) {
            foreach (extract_editor_upload_filenames((string) ($row['body'] ?? '')) as $name => $_) {
                $used[$name] = true;
            }
        }

        // 2. NewsArticleModel → kolom content (Berita)
        $newsRows = model(\App\Models\NewsArticleModel::class)->select('content')->findAll();
        foreach ($newsRows as $row) {
            foreach (extract_editor_upload_filenames((string) ($row['content'] ?? '')) as $name => $_) {
                $used[$name] = true;
            }
        }

        $now = time();
        foreach (new \DirectoryIterator($dir) as $fileInfo) {
            if (! $fileInfo->isFile()) {
                continue;
            }
            $name = $fileInfo->getFilename();
            if (! preg_match('/\.(png|jpe?g|webp|gif)$/i', $name)) {
                continue;
            }
            if (isset($used[$name])) {
                continue;
            }
            $mtime = (int) $fileInfo->getMTime();
            if ($graceSeconds > 0 && ($now - $mtime) < $graceSeconds) {
                continue;
            }
            @unlink($fileInfo->getPathname());
        }
    }
}

if (! function_exists('delete_editor_upload_file')) {
    /**
     * Hapus satu file upload editor berdasarkan nama file.
     * Hanya menghapus jika file tidak lagi digunakan di konten manapun.
     *
     * @param string $filename Nama file saja (tanpa path), misal: "abc123.jpg"
     * @return bool true jika berhasil dihapus atau memang tidak ada, false jika masih dipakai
     */
    function delete_editor_upload_file(string $filename): bool
    {
        // Validasi nama file: hanya huruf, angka, titik, strip, underscore
        if (! preg_match('/^[a-zA-Z0-9._-]+\.(png|jpe?g|webp|gif)$/i', $filename)) {
            return false;
        }

        $filepath = rtrim(FCPATH, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . 'uploads'
            . DIRECTORY_SEPARATOR . 'editor'
            . DIRECTORY_SEPARATOR . $filename;

        if (! is_file($filepath)) {
            return true; // sudah tidak ada, anggap berhasil
        }

        // Pastikan file tidak dipakai di konten manapun (SitePageModel dan NewsArticleModel)
        $siteRows = model(\App\Models\SitePageModel::class)->select('body')->findAll();
        foreach ($siteRows as $row) {
            $body = (string) ($row['body'] ?? '');
            if (str_contains($body, 'uploads/editor/' . $filename)) {
                return false; // masih dipakai
            }
        }

        $newsRows = model(\App\Models\NewsArticleModel::class)->select('content')->findAll();
        foreach ($newsRows as $row) {
            $content = (string) ($row['content'] ?? '');
            if (str_contains($content, 'uploads/editor/' . $filename)) {
                return false; // masih dipakai
            }
        }

        return @unlink($filepath);
    }
}

if (! function_exists('plain_text_to_editor_html')) {
    /**
     * Mengubah teks polos (paragraf dipisah baris kosong ganda) menjadi HTML sederhana untuk editor.
     */
    function plain_text_to_editor_html(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }

        $parts = preg_split("/\R{2,}/", $text) ?: [];
        $blocks = [];
        foreach ($parts as $part) {
            $part = trim((string) $part);
            if ($part === '') {
                continue;
            }
            $blocks[] = '<p>' . nl2br(esc($part), false) . '</p>';
        }

        return implode('', $blocks);
    }
}

if (! function_exists('is_html_string')) {
    function is_html_string(string $value): bool
    {
        return (bool) preg_match('/<\s*[a-z][\s\S]*>/i', $value);
    }
}