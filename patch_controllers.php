<?php

$files = [
    'app/Http/Controllers/Admin/RestoranPackageController.php',
    'app/Http/Controllers/Admin/HotelPackageController.php',
    'app/Http/Controllers/Partner/RestoranPackageController.php',
    'app/Http/Controllers/Partner/HotelPackageController.php',
];

foreach ($files as $f) {
    if (file_exists($f)) {
        $content = file_get_contents($f);
        
        // 1. Update Validation Rules
        $content = preg_replace(
            "/'seo_keywords'\s*=>\s*'nullable\|string\|max:255',\s*'seo_description'\s*=>\s*'nullable\|string',/s",
            "'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'social_title' => 'nullable|string|max:255',
            'social_description' => 'nullable|string',
            'seo_image' => 'nullable|image|max:2048',",
            $content
        );

        // 2. Add File Upload Logic (For Edit)
        $var = strpos($f, 'Hotel') !== false ? '$hotel_package' : '$restoran_package';
        
        // In Store:
        $storeLogic = "
        if (\$request->hasFile('seo_image')) {
            \$data['seo_image_path'] = \$request->file('seo_image')->store('seo_images', 'public');
        }
        ";
        
        // In Update:
        $updateLogic = "
        if (\$request->hasFile('seo_image')) {
            if ({$var}->seo_image_path) {
                Storage::disk('public')->delete({$var}->seo_image_path);
            }
            \$data['seo_image_path'] = \$request->file('seo_image')->store('seo_images', 'public');
        }
        ";

        // Insert before `Model::create($data)`
        $modelClass = strpos($f, 'Hotel') !== false ? 'HotelPackage' : 'RestoranPackage';
        $content = preg_replace(
            "/{$modelClass}::create\(\\\$data\);/",
            $storeLogic . "\n        {$modelClass}::create(\$data);",
            $content
        );
        
        // Insert before `$model->update($data)`
        $content = preg_replace(
            "/\\{$var}->update\(\\\$data\);/",
            $updateLogic . "\n        {$var}->update(\$data);",
            $content
        );
        
        file_put_contents($f, $content);
        echo "Updated {$f}\n";
    }
}
