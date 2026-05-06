<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HealthCheckController extends Controller
{
    use ApiTrait;

    public function check() : JsonResponse
    {

        $secret = config('app.healthSecret');
        $xHealthCheck = request()->header('Health-Check') ?? '';

        $signature = hash_hmac('sha256', request()->token ?? '', $secret);

        if (!hash_equals($xHealthCheck, $signature)) {
            return $this->failure('Unauthorized.', 401);
        }

        $results = [
            'php_version'   => PHP_VERSION,
            'app_version'   => 'laravel-' . app()->version(),
            'db_connection' => 'failed',
            'insert'        => 'failed',
            'get'           => 'failed',
            'error'         => null,
        ];

        $status = 'error';

        try {
            // 1. Test Database Connection
            DB::connection()->getPdo();
            $results['db_connection'] = 'successful';

            // 2. Test Insert
            $randomData = ['source' => request()->getClientIp(), 'created_at' => now()];
            DB::table('health_checks')->insert($randomData);
            $results['insert'] = 'successful';

            // test data retrieval from db
            $checks = DB::table('health_checks')->limit(10)->get();

            if (count($checks) > 0) {
                $results['get'] = 'successful';
            }

            $status = 'success';

        } catch (\Exception $e) {
            // Log error and return
            $results['error'] = $e->getMessage();
            Log::error('DB Health Check Failed: ' . $e->getMessage());
        }

        return response()->json([
            'status'    =>    $status,
            'data'      => $results,
        ]);
    }
}
