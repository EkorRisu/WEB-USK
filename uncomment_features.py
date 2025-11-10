#!/usr/bin/env python3
"""
Script untuk mengaktifkan kembali semua fitur yang dikomentari
"""
import re

def uncomment_blade_file(filepath):
    """Uncomment blade comments {{-- ... --}}"""
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Pattern untuk menghapus komentar blade yang mengandung "TIDAK ADA DI"
    # Ini akan menghapus {{-- dan --}} tapi tetap menjaga konten di dalamnya
    
    # Hapus opening {{--
    content = re.sub(r'\{\{--\s*(FITUR[^}]*?)--\}\}', r'', content, flags=re.DOTALL)
    
    # Uncomment blok yang dikomentari
    lines = content.split('\n')
    result_lines = []
    in_comment_block = False
    
    for line in lines:
        # Cek jika ini adalah komentar fitur yang tidak ada di requirements
        if '{{--' in line and ('TIDAK ADA' in line or 'DIKOMENTARI' in line):
            # Hapus opening comment
            line = line.replace('{{--', '').strip()
            in_comment_block = True
            # Jika ada juga closing tag di baris yang sama
            if '--}}' in line:
                line = line.replace('--}}', '').strip()
                in_comment_block = False
            if line:  # Tambahkan hanya jika baris tidak kosong
                result_lines.append(line)
        elif in_comment_block and '--}}' in line:
            # Hapus closing comment
            line = line.replace('--}}', '').strip()
            in_comment_block = False
            if line:
                result_lines.append(line)
        elif in_comment_block:
            # Dalam blok komentar, hapus {{-- dan --}} saja
            line = line.replace('{{--', '').replace('--}}', '')
            result_lines.append(line)
        else:
            result_lines.append(line)
    
    return '\n'.join(result_lines)

def uncomment_js_comments(filepath):
    """Uncomment JavaScript comments /* ... */"""
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    lines = content.split('\n')
    result_lines = []
    in_comment_block = False
    
    for line in lines:
        # Cek jika ini adalah komentar fitur
        if '// FITUR' in line and 'TIDAK ADA' in line:
            # Biarkan komentar deskriptif, tapi uncomment kode di bawahnya
            result_lines.append(line)
        elif '/*' in line and ('TIDAK ADA' in line or 'DIKOMENTARI' in line):
            in_comment_block = True
            # Hapus opening comment marker
            line = line.replace('/*', '//').strip()
            result_lines.append(line)
        elif in_comment_block and '*/' in line:
            in_comment_block = False
            # Hapus closing comment marker
            line = line.replace('*/', '').strip()
            if line:
                result_lines.append(line)
        elif in_comment_block:
            # Uncomment baris dalam blok
            if line.strip().startswith('//'):
                result_lines.append(line)
            else:
                result_lines.append(line)
        else:
            result_lines.append(line)
    
    return '\n'.join(result_lines)

# File yang akan diproses
files_to_process = [
    'resources/views/user/dashboard.blade.php',
    'resources/views/admin/produk/index.blade.php',
    'resources/views/admin/transactions/index.blade.php',
    'resources/views/layouts/navbar.blade.php',
    'resources/views/layouts/admin.blade.php',
    'resources/views/layouts/user.blade.php',
]

print("Mengaktifkan kembali semua fitur yang dikomentari...")
for file in files_to_process:
    try:
        content = uncomment_blade_file(file)
        with open(file, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"✓ {file}")
    except Exception as e:
        print(f"✗ {file}: {e}")

print("\nSelesai!")
