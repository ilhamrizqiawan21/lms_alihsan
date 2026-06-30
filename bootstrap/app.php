<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;

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
        $exceptions->render(
            fn (UniqueConstraintViolationException $e, Request $request) => handleDuplicateError($e, $request),
        );
    })->create();

/**
 * Handler global untuk error duplikasi data.
 * Mengekstrak info dari pesan error MySQL dan menampilkan flash message yang ramah.
 */
function handleDuplicateError(UniqueConstraintViolationException $e, Request $request): RedirectResponse
{
    $message = $e->getMessage();

    // Ekstrak nama kolom yang konflik dari pesan error
    // Format: "Duplicate entry 'xxx' for key 'tabel.kolom'"
    if (preg_match("/for key '(.+?)\.(.+?)'/", $message, $m)) {
        $table = $m[1];
        $column = $m[2];
        $errorMsg = "Data gagal disimpan: duplikasi pada {$column} di tabel {$table}.";
    } elseif (preg_match("/for key '(.+?)'/", $message, $m)) {
        $errorMsg = "Data gagal disimpan: data duplikat terdeteksi ({$m[1]}).";
    } else {
        $errorMsg = 'Data gagal disimpan: data duplikat terdeteksi.';
    }

    // Untuk AJAX / API request
    if ($request->expectsJson() || $request->is('api/*')) {
        abort(422, $errorMsg);
    }

    // Untuk web request — redirect back dengan error
    return back()->withInput()->with('error', $errorMsg);
}
