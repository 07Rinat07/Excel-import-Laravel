<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        // Handle domain validation exceptions
        $this->renderable(function (\App\Domain\Import\Exceptions\ValidationFailedException $e, $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'message' => $e->getMessage(),
                ], 422);
            }
        });

        // Handle invalid file exceptions
        $this->renderable(function (\App\Domain\Import\Exceptions\InvalidFileException $e, $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'Invalid file',
                    'message' => $e->getMessage(),
                ], 422);
            }
        });

        // Handle throttle exceptions
        $this->renderable(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'Too many requests',
                    'message' => 'Please wait before making another request',
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? 60,
                ], 429);
            }
        });

        // Handle validation exceptions
        $this->renderable(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // Default exception handler
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
