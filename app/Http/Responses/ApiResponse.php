<?php

namespace App\Http\Responses;

use Carbon\Carbon;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends Base
{
    public function __construct(Carbon $start_time, string $request_id)
    {
        $this->start_time = $start_time;
        $this->request_id = $request_id;
    }

    /**
     * Set OK Response
     *
     * @param array $data
     * @return JsonResponse
     * @throws Exception
     */
    public function setOKResponse(array $data): JsonResponse
    {
        $this->data = $data;
        $this->setElapseTime();
        $this->is_error = false;

        $response = [
            'data' => $this->data,
            'request_id' => $this->request_id,
            'elapse_time' => $this->elapse_time,
            'is_error' => $this->is_error
        ];

        return response()->json($response);
    }

    /**
     * Set Elapse Time
     * @throws Exception
     */
    private function setElapseTime()
    {
        $this->elapse_time = Carbon::parse(now())
                ->diffAsCarbonInterval($this->start_time, true)
                ->total('milliseconds') . 'ms';
    }

    /**
     * @param array $data
     * @param int $status
     * @param string $message [optional]
     * @return JsonResponse
     * @throws Exception
     */
    public function setBadResponse(array $data, int $status, string $message = 'Something went wrong.'): JsonResponse
    {
        $this->data = array_merge($data, ['error_message' => $message]);
        $this->setElapseTime();
        $this->is_error = true;

        $response = [
            'data' => $this->data,
            'request_id' => $this->request_id,
            'elapse_time' => $this->elapse_time,
            'is_error' => $this->is_error
        ];

        return response()->json($response, $status);
    }

    /**
     * @throws Exception
     */
    public function setValidateError(array $data, int $status, string $message = 'Invalid request.'): JsonResponse
    {
        $this->data = $data;
        $this->setElapseTime();
        $this->is_error = true;

        $response = [
            'data' => [
                'errors' => $this->data,
                'message' => $message
            ],
            'request_id' => $this->request_id,
            'elapse_time' => $this->elapse_time,
            'is_error' => $this->is_error
        ];

        return response()->json($response, $status);
    }
}
