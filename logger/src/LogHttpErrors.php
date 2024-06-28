<?php

namespace CustomLogger;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Exceptions\HttpResponseException; // Import HttpResponseException
use Symfony\Component\HttpKernel\Exception\HttpException; // Import HttpException

class LogHttpErrors
{

    public function handle(Request $request, Closure $next)
{
    try {
        $response = $next($request);
        
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true); // Get the data as an associative array
            if (isset($data['status']) && $data['status'] == 'failed') {
                throw new \App\Exceptions\AppSpecificException($data['message']);
            }
        }

    } catch (\Throwable $e) {
        return $this->handleException($e);
    }

    // Check response status code for logging
    if ($response->getStatusCode() >= 400) {
        $this->logResponseError($response);
    }

    return $response;
}


    protected function handleException($e)
    {
        $this->reportException($e);
        
        if ($e instanceof \Illuminate\Database\QueryException) {
            throw new HttpException(503, 'SQL-Error 503 - Error while creating records', [
                'message' => $e->getMessage(),
                'internalCode' => 'Micro-1',
                'more' => null
            ]);
        }

        return response()->json([
            'status' => 'failed',
            'data' => null,
            'message' => $e->getMessage()
        ], 500);
    }

    protected function reportException($e)
    {
        if (App::environment('production')) {
            report($e);
        } else {
            Log::channel('colored_log')->error($e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'exception' => get_class($e),
                'code' => $e->getCode(),
            ]);
        }
    }

    protected function logResponseError($response)
    {
        $level = 'error';
        if ($response->getStatusCode() >= 500) {
            $level = 'critical';
        } elseif ($response->getStatusCode() >= 400) {
            $level = 'warning';
        }

        Log::channel('colored_log')->$level($response->getStatusCode() . ' ' . $response->getContent());
    }
}
