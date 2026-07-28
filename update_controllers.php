<?php

$controllers = [
    'app/Http/Controllers/Partner/TourPackageController.php',
    'app/Http/Controllers/Partner/RestoranPackageController.php',
    'app/Http/Controllers/Partner/ShipPackageController.php',
    'app/Http/Controllers/Partner/RentCarPackageController.php',
    'app/Http/Controllers/Partner/HotelPackageController.php',
    'app/Http/Controllers/Admin/MicePackageController.php',
    'app/Http/Controllers/Admin/ShipPackageController.php',
    'app/Http/Controllers/Admin/UmrahPackageController.php',
    'app/Http/Controllers/Admin/TourPackageController.php',
    'app/Http/Controllers/Admin/RestoranPackageController.php',
    'app/Http/Controllers/Admin/RentCarPackageController.php',
    'app/Http/Controllers/Admin/HotelPackageController.php',
];

foreach ($controllers as $f) {
    if (file_exists($f)) {
        $content = file_get_contents($f);
        
        // 1. Add social fields
        $content = preg_replace(
            "/'seo_keywords'\s*=>\s*\\\$request->seo_keywords,/",
            "'seo_keywords' => \$request->seo_keywords,\n                'social_title' => \$request->social_title,\n                'social_description' => \$request->social_description,",
            $content
        );

        // 2. Add seo_image logic before Translate dispatch
        // Most package controllers dispatch translation at the end of store() and update()
        // Wait, the variable name for the package is $tour_package in TourPackageController update, but in store() it's $package.
        
        // Let's find the variable name used for dispatch:
        // \App\Jobs\Translate\XXXToEn::dispatch($VAR_NAME->id)
        
        $lines = explode("\n", $content);
        $new_lines = [];
        
        foreach ($lines as $line) {
            if (strpos($line, '\App\Jobs\Translate\\') !== false && strpos($line, '::dispatch(') !== false) {
                // Extract var name
                preg_match('/dispatch\(\s*\\$([a-zA-Z0-9_]+)->id\s*\)/', $line, $matches);
                if ($matches) {
                    $var = $matches[1];
                    $seo_logic = "
            if (\$request->hasFile('seo_image')) {
                if (\${$var}->seo_image_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete(\${$var}->seo_image_path);
                }
                \${$var}->update(['seo_image_path' => \$request->file('seo_image')->store('seo_images', 'public')]);
            }
";
                    $new_lines[] = $seo_logic;
                }
            }
            $new_lines[] = $line;
        }
        
        file_put_contents($f, implode("\n", $new_lines));
        echo "Updated {$f}\n";
    }
}
