<?php

namespace CustomLogger\Logger\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, string>
     */
    protected $levels = [
        HttpException::class => 'error',  // Default log level for HTTP exceptions
        QueryException::class => 'critical', // Log database query exceptions as critical
        ModelNotFoundException::class => 'error', // Log model not found exceptions as error
        NotFoundHttpException::class => 'error', // Log 404 errors as error
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<class-string<Throwable>>
     */
    protected $dontReport = [
        // Add exception types here that you do not want to report to logs or handle explicitly
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            $this->logException($e);
        });
    }

    /**
     * Report or log an exception.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws Throwable
     */
    public function report(Throwable $exception)
    {
        if ($this->shouldntReport($exception)) {
            return;
        }

        $level = $this->getExceptionLogLevel($exception);
        Log::channel('colored_log')->{$level}($exception->getMessage(), [
            'trace' => $exception->getTraceAsString(),
        ]);

        parent::report($exception); // Ensure Laravel's default reporting behavior
    }

    /**
     * Log an exception.
     *
     * @param Throwable $exception
     * @return void
     */
    protected function logException(Throwable $exception)
    {
        // Logging handled in the report method directly
    }

    /**
     * Determine the log level for the exception.
     *
     * @param Throwable $exception
     * @return string
     */
    protected function getExceptionLogLevel(Throwable $exception): string
    {
        foreach ($this->levels as $type => $level) {
            if ($exception instanceof $type) {
                return $level;
            }
        }

        return 'error'; // Default to 'error' if no specific match found
    }
}
