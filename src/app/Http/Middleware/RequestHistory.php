<?php

namespace App\Http\Middleware;

use App\Events\RequestTerminated;
use App\Models\RequestHistory as RequestHistoryModel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestHistory
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        $data = [
            'user_id' => $request->user()->id ?? null,
            'route_name' => $request->route()->getName(),
            'request_body' => $request->all(),
            'response_body' => json_decode($response->getContent()),
            'response_code' => $response->getStatusCode(),
            'user_ip' => $request->ip()
        ];
        RequestTerminated::dispatch($data);
    }
}
