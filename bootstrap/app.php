<?php

use App\Http\Middleware\CheckBlockedIp;
use App\Http\Middleware\CheckRole;
use App\Models\SystemError;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            CheckBlockedIp::class,
        ]);

        $middleware->alias([
            'role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $e) {
            if (
                $e instanceof AuthenticationException
                || $e instanceof AuthorizationException
                || $e instanceof AccessDeniedHttpException
                || $e instanceof NotFoundHttpException
                || ($e instanceof HttpExceptionInterface && $e->getStatusCode() < 500)
            ) {
                return;
            }

            try {
                $request = request();

                SystemError::create([
                    'error_level' => 'error',
                    'error_code' => get_class($e),
                    'message' => mb_substr($e->getMessage() ?: get_class($e), 0, 5000),
                    'file' => mb_substr($e->getFile(), 0, 255),
                    'line' => $e->getLine(),
                    'trace' => mb_substr($e->getTraceAsString(), 0, 10000),
                    'url' => mb_substr($request->fullUrl(), 0, 255),
                    'ip_address' => $request->ip(),
                    'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
                    'user_id' => Auth::id(),
                    'created_at' => now(),
                ]);
            } catch (\Throwable) {
                // Jangan sampai mekanisme audit error membuat error baru.
            }
        });

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $redirectToDashboard = function () {
            return match (Auth::user()?->role?->nama_role) {
                'admin' => route('admin.dashboard'),
                'guru' => route('guru.dashboard'),
                'siswa' => route('siswa.dashboard'),
                'kepala_sekolah' => route('kepsek.dashboard'),
                default => route('login'),
            };
        };

        $maintenanceResponse = function (Request $request, int $status = 500) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return null;
            }

            return response()->view('errors.maintenance', [], $status);
        };

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return null;
            }

            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) use ($redirectToDashboard) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return null;
            }

            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }

            return redirect($redirectToDashboard())->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        });

        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) use ($redirectToDashboard) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return null;
            }

            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }

            return redirect($redirectToDashboard())->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($maintenanceResponse) {
            return $maintenanceResponse($request, 404);
        });

        // Tangani duplicate key constraint violation
        $exceptions->render(function (UniqueConstraintViolationException $e, Request $request) {
            $message = $e->getMessage();

            // Format MySQL: "Duplicate entry 'xxx' for key 'tabel.kolom'"
            if (preg_match("/for key '(.+?)\.(.+?)'/", $message, $m)) {
                $table = $m[1];
                $column = $m[2];
                $errorMsg = "Data gagal disimpan: duplikasi pada {$column} di tabel {$table}.";
            } elseif (preg_match("/for key '(.+?)'/", $message, $m)) {
                $errorMsg = "Data gagal disimpan: data duplikat terdeteksi ({$m[1]}).";
            } else {
                $errorMsg = 'Data gagal disimpan: data duplikat terdeteksi.';
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                abort(422, $errorMsg);
            }

            return back()->withInput()->with('error', $errorMsg);
        });

        $exceptions->render(function (\Throwable $e, Request $request) use ($maintenanceResponse) {
            if (
                $e instanceof AuthenticationException
                || $e instanceof AuthorizationException
                || $e instanceof AccessDeniedHttpException
                || $e instanceof ValidationException
                || $e instanceof UniqueConstraintViolationException
            ) {
                return null;
            }

            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

            return $maintenanceResponse($request, $status);
        });
    })->create();
