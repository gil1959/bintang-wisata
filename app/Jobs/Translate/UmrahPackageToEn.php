<?php

namespace App\Jobs\Translate;

use App\Models\UmrahPackage;
use App\Services\TranslateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UmrahPackageToEn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $id) {}

    public function handle(TranslateService $tx): void
    {
        $p = UmrahPackage::with('category')->find($this->id);
        if (!$p) return;

        $dirty = false;

        $textMap = [
            'title_en'         => [$p->title_en ?? null, $p->title ?? null],
            'label_en'         => [$p->label_en ?? null, $p->label ?? null],
            'duration_text_en' => [$p->duration_text_en ?? null, $p->duration_text ?? null],
            'seo_title_en'     => [$p->seo_title_en ?? null, $p->seo_title ?? null],
            'seo_description_en' => [$p->seo_description_en ?? null, $p->seo_description ?? null],
            'seo_keywords_en'  => [$p->seo_keywords_en ?? null, $p->seo_keywords ?? null],
        ];

        $keys = [];
        $inputs = [];
        foreach ($textMap as $enField => [$cur, $src]) {
            if ((!$cur || trim((string)$cur) === '') && is_string($src) && trim($src) !== '') {
                $keys[] = $enField;
                $inputs[] = $src;
            }
        }

        if ($inputs) {
            $out = $tx->toEnBatch($inputs, 'text');
            foreach ($keys as $i => $field) {
                $val = $out[$i] ?? null;
                if (is_string($val) && trim($val) !== '') {
                    $p->{$field} = $val;
                    $dirty = true;
                }
            }
        }

        if ((!$p->long_description_en || trim((string)$p->long_description_en) === '')
            && is_string($p->long_description) && trim($p->long_description) !== ''
        ) {
            $val = $tx->toEnBatch([$p->long_description], 'html')[0] ?? null;
            if (is_string($val) && trim($val) !== '') {
                $p->long_description_en = $val;
                $dirty = true;
            }
        }

        if (
            $p->category && (!($p->category->name_en ?? null) || trim((string)$p->category->name_en) === '')
            && is_string($p->category->name) && trim($p->category->name) !== ''
        ) {
            $val = $tx->toEnBatch([$p->category->name], 'text')[0] ?? null;
            if (is_string($val) && trim($val) !== '') {
                $p->category->name_en = $val;
                $p->category->save();
            }
        }

        if ($dirty) $p->save();
    }
}
