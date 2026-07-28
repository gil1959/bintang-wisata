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
    'app/Http/Controllers/Admin/ArticleController.php',
];

foreach ($controllers as $file) {
    if (!file_exists($file)) continue;
    
    $content = file_get_contents($file);
    
    $varName = strpos($file, 'ArticleController') !== false ? '$article' : '$package';
    // For TourPackageController, the variable is often $package or $pkg. Let's check $package first, if not $tourPackage.
    // Actually, in Bintang Wisata, they usually use $package. Wait, in store() it's $package = Model::create($validated).
    
    // We can just add the image upload BEFORE the create() or update() call.
    // But we need the old image path for delete, which is only available on update.
    
    // Instead of doing it in PHP, maybe I'll do it manually to ensure correctness.
}
