<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 *  This middleware compare a hash keys from our local and from request of git webhooks
 *  only verify to if match.
 */
class ValidateGithubWebhooksMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->headers->has('X-Hub-Signature')) {

            $secretKey = env('GITHUB_WEBHOOK_SECRET');
            $signature = $request->header('X-Hub-Signature');
            $payload = $request->getContent();

            list($hashAlgo, $signature) = explode('=', $signature);

            $compute = hash_hmac($hashAlgo, $payload, $secretKey);
            $valid = hash_equals($compute, $signature);

            if ($valid === false) {
                // return response()->json('Unauthorized request.', 401);
            }
        }

        return $next($request);
    }
}
