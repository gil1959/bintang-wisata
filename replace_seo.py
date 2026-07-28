import os
import re

files = [
    'resources/views/partner/tour-packages/_form.blade.php',
    'resources/views/partner/restoran/_form.blade.php',
    'resources/views/partner/ship-packages/_form.blade.php',
    'resources/views/partner/rentcar/_form.blade.php',
    'resources/views/partner/hotel/_form.blade.php',
    'resources/views/admin/mice-packages/_form.blade.php',
    'resources/views/admin/ship-packages/_form.blade.php',
    'resources/views/admin/umrah-packages/_form.blade.php',
    'resources/views/admin/tour-packages/_form.blade.php',
    'resources/views/admin/restoran/_form.blade.php',
    'resources/views/admin/rentcar/_form.blade.php',
    'resources/views/admin/hotel/_form.blade.php',
    'resources/views/admin/articles/_form.blade.php',
]

def replace_seo_block(content, filename):
    # We look for <div x-data="{ open: false }"... <span>SEO...</div></div></div>
    # A robust regex for the SEO block:
    # It starts with <div x-data=... > \n <button ... <span>SEO
    # And ends with the matching </div> for that main div.
    
    pattern = r'<div x-data="\{ open: (?:false|true) \}"[^>]*>\s*<button[^>]*>.*?<span>SEO.*?</div>\s*</div>\s*</div>'
    
    # Wait, some blocks might have more nested divs. 
    # Another approach: find the index of "SEO Paket" or "SEO Artikel", then backtrack to the previous `<div x-data`
    
    # Let's try regex with non-greedy match that ends at the end of the SEO block.
    # The SEO block usually ends just before `@if($pkg && method_exists` or `{{-- KONTEN` or `@include` or `@endsection`
    
    # Since regex can be error prone for HTML, let's just use string finding.
    
    start_idx = content.find('<span>SEO')
    if start_idx == -1:
        start_idx = content.find('SEO Paket') # Some might not have span
    
    if start_idx == -1:
        start_idx = content.find('seo_title')
    
    if start_idx != -1:
        # backtrack to <div x-data
        div_start = content.rfind('<div x-data', 0, start_idx)
        if div_start == -1:
            return content
        
        # Now find the matching end </div>
        # Keep track of <div and </div
        depth = 0
        i = div_start
        while i < len(content):
            if content[i:i+4] == '<div':
                depth += 1
                i += 4
            elif content[i:i+5] == '</div':
                depth -= 1
                i += 5
                if depth == 0:
                    end_idx = i + 1 # include the >
                    
                    var_name = '$article' if 'article' in filename else '$package'
                    if 'tour-packages' in filename or 'mice' in filename or 'umrah' in filename:
                        var_name = '$pkg'
                        
                    replacement = f"@include('partials._seo_form', ['model' => {var_name} ?? null])\n"
                    
                    new_content = content[:div_start] + replacement + content[end_idx:]
                    return new_content
            else:
                i += 1
                
    return content

for f in files:
    if os.path.exists(f):
        with open(f, 'r', encoding='utf-8') as file:
            content = file.read()
            
        new_content = replace_seo_block(content, f)
        
        with open(f, 'w', encoding='utf-8') as file:
            file.write(new_content)
        print(f"Updated {f}")
    else:
        print(f"Not found: {f}")
