<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockBots
{
    private const BLOCKED_AGENTS = [
        'python-requests', 'python-urllib', 'python/',
        'scrapy', 'curl/', 'wget/', 'go-http-client',
        'libwww-perl', 'lwp-trivial', 'java/', 'jakarta commons',
        'httpclient', 'mechanize', 'nikto', 'masscan',
        'zgrab', 'nmap', 'sqlmap', 'dirbuster', 'nuclei',
        'semrushbot', 'ahrefsbot', 'mj12bot', 'dotbot',
        'blexbot', 'yandexbot', 'petalbot',
    ];

    public function handle(Request $request, Closure $next)
    {
        $ua = strtolower($request->userAgent() ?? '');

        if (empty($ua)) {
            abort(403, 'Forbidden');
        }

        foreach (self::BLOCKED_AGENTS as $pattern) {
            if (str_contains($ua, $pattern)) {
                abort(403, 'Forbidden');
            }
        }

        return $next($request);
    }
}
