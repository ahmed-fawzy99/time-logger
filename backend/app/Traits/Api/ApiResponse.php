<?php

namespace App\Traits\Api;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Returns JSON Response.
     *
     * @param  string  $message  Message to be returned
     * @param  mixed  $data  Data to be returned
     * @param  int  $code  HTTP status code
     */
    public function res(string $message, $data, int $code): JsonResponse
    {
        return response()->json(
            array_filter([
                'status' => $code,
                'message' => $message ?: null,
                'data' => $data,
            ], fn ($item) => $item !== null),
            $code);
    }

    /**
     * Returns JSON Response of the success.
     */
    public function ok(string $message, $data = null, int $code = 200): JsonResponse
    {
        return $this->res($message, $data, $code);
    }

    /**
     * Returns JSON Response of the error. Pass the Exception $e object to report the error in Sentry.
     */
    public function error(string $message, $data = null, int $code = 400): JsonResponse
    {
        return $this->res($message, $data, $code);
    }
}
