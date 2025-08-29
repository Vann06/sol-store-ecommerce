<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin.reports.index', function (User $user) {
            return $user->hasRole('admin');
        });

        Gate::define('admin.reports.export', function (User $user) {
            return $user->hasRole('admin');
        });
        // Temporary instrumentation: when DEBUG_PEDIDOS=true in .env, log any INSERTs into pedidos
        // This helps trace duplicate order creations during runtime. Remove or disable when finished.
        if (env('DEBUG_PEDIDOS', false)) {
            DB::listen(function ($query) {
                $sql = $query->sql ?? '';
                if (stripos($sql, 'insert into "pedidos"') !== false || stripos($sql, 'insert into pedidos') !== false) {
                    // Collect a short backtrace
                    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 6);
                    $bt = [];
                    foreach ($trace as $t) {
                        if (isset($t['file'])) {
                            $bt[] = ($t['file'] ?? '') . ':' . ($t['line'] ?? '');
                        } elseif (isset($t['function'])) {
                            $bt[] = $t['function'];
                        }
                    }

                    Log::info('PEDIDOS INSERT DETECTED', [
                        'sql' => $sql,
                        'bindings' => $query->bindings ?? null,
                        'time' => $query->time ?? null,
                        'request' => [
                            'method' => Request::method() ?? 'N/A',
                            'url' => Request::fullUrl() ?? 'N/A',
                            'ip' => Request::ip() ?? 'N/A',
                            'user_id' => auth()->id() ?? null,
                        ],
                        'backtrace' => $bt,
                    ]);
                }
            });
        }
    }
}
