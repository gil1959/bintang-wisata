<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TranslateService
{
    public function toEnText(?string $text): ?string
    {
        $text = is_string($text) ? trim($text) : '';
        if ($text === '') return null;

        $driver = config('translate.driver', 'deepl');
        if ($driver !== 'deepl') {
            // fallback: kalau driver lain belum dipakai
            return $text;
        }

        return $this->deeplTranslate($text, 'text');
    }

    public function toEnHtml(?string $html): ?string
    {
        $html = is_string($html) ? trim($html) : '';
        if ($html === '') return null;

        $driver = config('translate.driver', 'deepl');
        if ($driver !== 'deepl') {
            return $html;
        }

        $html = $this->normalizeQuillHtml($html);

        return $this->deeplTranslate($html, 'html');
    }


    /**
     * Translate array string (includes/excludes, dll).
     * Return array yang sudah ditranslate, item kosong dibuang.
     */
    public function toEnArray($items): array
    {
        if (!is_array($items)) return [];

        $items = array_values(array_filter(array_map(function ($v) {
            $s = is_string($v) ? trim($v) : '';
            return $s !== '' ? $s : null;
        }, $items)));

        if (!count($items)) return [];

        // pakai batch biar hemat request dan cepat
        $out = $this->toEnBatch($items, 'text');

        // bersihin null/empty
        return array_values(array_filter(array_map(function ($v) {
            $s = is_string($v) ? trim($v) : '';
            return $s !== '' ? $s : null;
        }, $out)));
    }

    /**
     * Translate batch items dalam 1 request ke DeepL (multi "text").
     * Output array ukurannya SAMA dengan input.
     * - item kosong/null => output null di index yang sama
     */
    public function toEnBatch(array $items, string $mode = 'text'): array
    {
        $driver = config('translate.driver', 'deepl');
        if ($driver !== 'deepl') {
            return $items;
        }

        $key = config('translate.deepl.key');
        if (!$key) throw new \RuntimeException('DEEPL_API_KEY belum diset.');

        $endpoint = config('translate.deepl.endpoint');
        if (!$endpoint) throw new \RuntimeException('DEEPL_ENDPOINT belum diset.');

        $texts = [];
        $map = []; // original index => compact index in $texts

        foreach ($items as $i => $v) {
            $s = is_string($v) ? trim($v) : '';
            if ($s === '') {
                $map[$i] = null;
                continue;
            }
            $map[$i] = count($texts);
            $texts[] = $s;
        }

        if (!count($texts)) {
            return array_map(fn() => null, $items);
        }

        // Build body manual biar jadi: text=...&text=... (tanpa text[0])
        $parts = [];
        $parts[] = 'source_lang=ID';
        $parts[] = 'target_lang=EN';

        if ($mode === 'html') {
            $parts[] = 'tag_handling=html';
        }

        foreach ($texts as $t) {
            if ($mode === 'html') {
                $t = $this->normalizeQuillHtml($t);
            }
            $parts[] = 'text=' . rawurlencode($t);
        }


        $body = implode('&', $parts);

        $resp = Http::timeout(25)
            ->withHeaders([
                'Authorization' => 'DeepL-Auth-Key ' . $key,
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ])
            ->withBody($body, 'application/x-www-form-urlencoded')
            ->post($endpoint);


        if (!$resp->ok()) {
            throw new \RuntimeException('DeepL error: ' . $resp->status() . ' ' . $resp->body());
        }

        $data = $resp->json();
        $trs = $data['translations'] ?? null;

        if (!is_array($trs) || !count($trs)) {
            throw new \RuntimeException('DeepL response kosong.');
        }

        $outTexts = [];
        foreach ($trs as $t) {
            $outTexts[] = is_array($t) ? ($t['text'] ?? null) : null;
        }

        // rebuild output sesuai index asli
        $result = [];
        foreach ($items as $i => $_) {
            if ($map[$i] === null) {
                $result[$i] = null;
            } else {
                $result[$i] = $outTexts[$map[$i]] ?? null;
            }
        }

        return $result;
    }

    private function normalizeQuillHtml(string $html): string
    {
        // Align classes -> inline style (lebih stabil setelah translate)
        $replacements = [
            'ql-align-center'  => 'text-align:center;',
            'ql-align-right'   => 'text-align:right;',
            'ql-align-justify' => 'text-align:justify;',
        ];

        foreach ($replacements as $class => $style) {
            // Tambah style ke tag yang punya class tersebut
            $html = preg_replace_callback(
                '/<(p|div|h1|h2|h3|li)([^>]*\bclass="[^"]*\b' . preg_quote($class, '/') . '\b[^"]*"[^>]*)>/i',
                function ($m) use ($style) {
                    $tag = $m[1];
                    $attrs = $m[2];

                    if (preg_match('/\bstyle="([^"]*)"/i', $attrs, $sm)) {
                        // append ke style existing
                        $newStyle = rtrim($sm[1], ';') . ';' . $style;
                        $attrs = preg_replace('/\bstyle="[^"]*"/i', 'style="' . $newStyle . '"', $attrs);
                    } else {
                        $attrs .= ' style="' . $style . '"';
                    }
                    return '<' . $tag . $attrs . '>';
                },
                $html
            );
        }

        // Indent classes -> padding-left (optional tapi bikin output lebih mirip editor)
        $html = preg_replace_callback(
            '/<(p|div|li)([^>]*\bclass="[^"]*\bql-indent-(\d+)\b[^"]*"[^>]*)>/i',
            function ($m) {
                $tag = $m[1];
                $attrs = $m[2];
                $level = (int)$m[3];
                $pad = (2 * $level) . 'em'; // 1->2em, 2->4em, dst.

                if (preg_match('/\bstyle="([^"]*)"/i', $attrs, $sm)) {
                    $newStyle = rtrim($sm[1], ';') . ';padding-left:' . $pad . ';';
                    $attrs = preg_replace('/\bstyle="[^"]*"/i', 'style="' . $newStyle . '"', $attrs);
                } else {
                    $attrs .= ' style="padding-left:' . $pad . ';"';
                }
                return '<' . $tag . $attrs . '>';
            },
            $html
        );

        return $html;
    }

    private function deeplTranslate(string $text, string $mode): string
    {
        $key = config('translate.deepl.key');
        if (!$key) throw new \RuntimeException('DEEPL_API_KEY belum diset.');

        $endpoint = config('translate.deepl.endpoint');
        if (!$endpoint) throw new \RuntimeException('DEEPL_ENDPOINT belum diset.');

        $payload = [
            'text'        => $text,
            'source_lang' => 'ID',
            'target_lang' => 'EN',
        ];

        if ($mode === 'html') {
            $payload['tag_handling'] = 'html';
        }

        $resp = Http::timeout(25)
            ->withHeaders([
                'Authorization' => 'DeepL-Auth-Key ' . $key,
            ])
            ->asForm()
            ->post($endpoint, $payload);

        if (!$resp->ok()) {
            throw new \RuntimeException('DeepL error: ' . $resp->status() . ' ' . $resp->body());
        }

        $data = $resp->json();
        $out = $data['translations'][0]['text'] ?? null;

        if (!is_string($out) || trim($out) === '') {
            throw new \RuntimeException('DeepL response kosong.');
        }

        return $out;
    }
}
