<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PageView;

class TrackPageView
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only track GET requests that return HTML
        if ($request->isMethod('GET') && ! $request->ajax()) {
            $ua          = $request->userAgent() ?? '';
            $deviceType  = $this->detectDevice($ua);
            $browser     = $this->detectBrowser($ua);

            PageView::create([
                'page'        => $request->route()?->getName() ?? $request->path(),
                'url'         => $request->fullUrl(),
                'ip_address'  => $request->ip(),
                'user_agent'  => $ua,
                'referrer'    => $request->headers->get('referer'),
                'device_type' => $deviceType,
                'browser'     => $browser,
                'session_id'  => session()->getId(),
            ]);
        }

        return $response;
    }

    private function detectDevice(string $ua): string
    {
        $ua = strtolower($ua);
        if (str_contains($ua, 'mobile') || str_contains($ua, 'android')) {
            return 'mobile';
        }
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) {
            return 'tablet';
        }
        return 'desktop';
    }

    private function detectBrowser(string $ua): string
    {
        if (str_contains($ua, 'Firefox'))  return 'Firefox';
        if (str_contains($ua, 'Chrome'))   return 'Chrome';
        if (str_contains($ua, 'Safari'))   return 'Safari';
        if (str_contains($ua, 'Edge'))     return 'Edge';
        if (str_contains($ua, 'Opera'))    return 'Opera';
        return 'Other';
    }
}
