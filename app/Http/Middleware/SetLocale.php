<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Mendapatkan locale dari session. Jika tidak ada, gunakan default aplikasi (biasanya 'en').
        $locale = session('locale', config('app.locale'));
        
        // Mengatur locale aplikasi
        App::setLocale($locale);
        
        return $next($request);
    }
}