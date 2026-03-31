<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthController
{
    public function check(): JsonResponse
    {
        $postgres = $this->checkPostgres();
        $redis = $this->checkRedis();

        $status = ($postgres['status'] === 'ok' && $redis['status'] === 'ok') ? 'ok' : 'degraded';

        return response()->json([
            'status' => $status,
            'services' => [
                'postgres' => $postgres,
                'redis' => $redis,
            ],
        ], $status === 'ok' ? 200 : 503);
    }

    private function checkPostgres(): array
    {
        try {
            DB::connection()->getPdo();
            DB::statement('SELECT 1');

            return ['status' => 'ok'];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function checkRedis(): array
    {
        try {
            Redis::ping();

            return ['status' => 'ok'];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
