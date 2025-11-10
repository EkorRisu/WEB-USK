<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App; // Opsional, tapi baik untuk kejelasan

class LanguageController extends Controller
{
    /**
     * Menyimpan pilihan bahasa pengguna ke dalam sesi dan mengarahkan kembali.
     *
     * @param string $locale Kode bahasa ('en' atau 'id').
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLang($locale)
    {
        // 1. Validasi locale (opsional, tapi disarankan)
        if (!in_array($locale, ['en', 'id'])) {
            // Jika kode bahasa tidak valid, kembalikan ke default
            $locale = config('app.locale'); 
        }

        // 2. Simpan bahasa pilihan ke session
        session()->put('locale', $locale);
        
        // 3. Arahkan kembali ke halaman sebelumnya
        return redirect()->back();
    }
}