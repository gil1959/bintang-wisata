<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTourPackageRequest extends FormRequest
{
    public function authorize()
    {
        return true; // sudah dilindungi middleware auth + role di route
    }

    public function rules()
    {
        return [
            // ========= BASIC INFO =========
            'title'            => ['required', 'string', 'max:255'],
            'label' => ['nullable', 'string', 'max:30'],

            'slug'             => ['required', 'string', 'max:255', 'unique:tour_packages,slug'],
            'duration_text'    => ['required', 'string', 'max:255'],
            'destination'      => ['nullable', 'string', 'max:255'],
            'category_id'      => ['required', 'exists:tour_categories,id'],
            'long_description' => ['nullable', 'string'],

            'rating_value' => ['nullable', 'integer', 'min:1', 'max:5'],
            'rating_count' => ['nullable', 'integer', 'min:0'],
            // ========= INCLUDES / EXCLUDES =========
            'includes'         => ['nullable', 'array'],
            'includes.*'       => ['nullable', 'string', 'max:500'],

            'excludes'         => ['nullable', 'array'],
            'excludes.*'       => ['nullable', 'string', 'max:500'],

            // ========= ITINERARIES =========
            'itineraries'         => ['nullable', 'array'],
            'itineraries.*.title' => ['nullable', 'string', 'max:500'],


            // ========= TIERS (DOMESTIC + INTERNATIONAL) =========
            'tiers'                     => ['required', 'array'],

            // Domestic tiers
            'tiers.domestic'            => ['required', 'array'],
            'tiers.domestic.*.min_people' => ['required', 'integer', 'min:1'],
            'tiers.domestic.*.max_people' => ['nullable', 'integer', 'gte:tiers.domestic.*.min_people'],
            'tiers.domestic.*.price'      => ['required', 'integer', 'min:0'],
            'tiers.domestic.*.type'       => ['required', Rule::in(['domestic'])],
            'tiers.domestic.*.is_custom'  => ['required', 'boolean'],

            // International tiers
            'tiers.international'            => ['required', 'array'],
            'tiers.international.*.min_people' => ['required', 'integer', 'min:1'],
            'tiers.international.*.max_people' => ['nullable', 'integer', 'gte:tiers.international.*.min_people'],
            'tiers.international.*.price'      => ['required', 'integer', 'min:0'],
            'tiers.international.*.type'       => ['required', Rule::in(['international'])],
            'tiers.international.*.is_custom'  => ['required', 'boolean'],

            // ========= FLIGHT INFO =========
            'flight_info' => ['required', Rule::in(['included', 'not_included'])],
            'thumbnail' => 'nullable|image|max:2048',
            'gallery.*' => 'nullable|image|max:2048',
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:500'],



        ];
    }

    public function prepareForValidation()
    {
        // Bersihkan array supaya tidak ada baris kosong
        $includes = array_filter($this->includes ?? [], fn($v) => $v !== null && $v !== '');
        $excludes = array_filter($this->excludes ?? [], fn($v) => $v !== null && $v !== '');

        // Bersihkan itineraries yang kosong
        $itineraries = collect($this->itineraries ?? [])
            ->filter(fn($row) => isset($row['title']) && trim($row['title']) !== '')
            ->values()
            ->all();


        $this->merge([
            'includes'    => $includes,
            'excludes'    => $excludes,
            'itineraries' => $itineraries,
        ]);
    }

    public function messages()
    {
        return [
            'slug.unique' => 'Slug sudah digunakan paket lain.',
        ];
    }
}
