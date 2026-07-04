<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Database\UniqueConstraintViolationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

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
    })->create();
