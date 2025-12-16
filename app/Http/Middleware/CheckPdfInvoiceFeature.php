<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPdfInvoiceFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if PDF invoice feature is enabled
        if (!config('fitur.pdf_invoice')) {
            return redirect()
                ->back()
                ->with('error', 'Fitur download PDF tidak tersedia. Gunakan struk digital di web.');
        }

        return $next($request);
    }
}