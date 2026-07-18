<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponser
{
    /**
     * Return success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $status_code
     * @return JsonResponse
     */
    public function success($data, string $message = "Successful", int $status_code = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status_code);
    }

    /**
     * Return error response.
     *
     * @param string $message
     * @param int $status_code
     * @param array $errors
     * @return JsonResponse
     */
    public function error(string $message = 'Data is invalid', int $status_code = Response::HTTP_BAD_REQUEST, array $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'data' => null
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        return response()->json($response, $status_code);
    }
}