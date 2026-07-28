<?php

$files = [
    'resources/views/partner/restoran/_form.blade.php',
    'resources/views/partner/rentcar/_form.blade.php',
    'resources/views/partner/hotel/_form.blade.php',
    'resources/views/admin/restoran/_form.blade.php',
    'resources/views/admin/rentcar/_form.blade.php',
    'resources/views/admin/hotel/_form.blade.php',
    'resources/views/admin/articles/_form.blade.php',
];

foreach ($files as $f) {
    if (file_exists($f)) {
        $content = file_get_contents($f);
        
        // Find {{-- SEO --}}
        $start_idx = strpos($content, '{{-- SEO --}}');
        if ($start_idx !== false) {
            $div_start = strpos($content, '<div', $start_idx);
            
            if ($div_start !== false) {
                // Find matching closing div
                $depth = 0;
                $i = $div_start;
                $end_idx = -1;
                while ($i < strlen($content)) {
                    if (substr($content, $i, 4) === '<div') {
                        $depth++;
                        $i += 4;
                    } elseif (substr($content, $i, 5) === '</div') {
                        $depth--;
                        $i += 5;
                        if ($depth === 0) {
                            $end_idx = $i + 1;
                            break;
                        }
                    } else {
                        $i++;
                    }
                }
                
                if ($end_idx !== -1) {
                    $var_name = strpos($f, 'article') !== false ? '$article' : '$package';
                    
                    $replacement = "@include('partials._seo_form', ['model' => {$var_name} ?? null])\n";
                    $new_content = substr($content, 0, $start_idx) . $replacement . substr($content, $end_idx);
                    
                    file_put_contents($f, $new_content);
                    echo "Updated {$f}\n";
                }
            }
        } else {
            echo "No {{-- SEO --}} found in {$f}\n";
        }
    }
}
