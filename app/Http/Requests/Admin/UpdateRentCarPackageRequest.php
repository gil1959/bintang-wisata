<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRentCarPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'label' => ['nullable', 'string', 'max:30'],

            'price_per_day' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'rating_value' => ['nullable', 'integer', 'min:1', 'max:5'],
            'rating_count' => ['nullable', 'integer', 'min:0'],
            'features' => 'nullable|array',
            'features.*.name' => 'required|string|max:255',
            'features.*.available' => 'nullable',
            'category_id' => 'nullable|exists:rent_car_categories,id',
            'long_description' => 'nullable|string',
            'seo_title'        => 'nullable|string|max:255',
            'seo_keywords'     => 'nullable|string|max:500',
            'seo_description'  => 'nullable|string|max:300',

        ];
    }
}
