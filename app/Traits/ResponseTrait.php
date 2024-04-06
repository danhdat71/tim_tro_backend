<?php

namespace App\Traits;

trait ResponseTrait
{
    public function responseDataSuccess($data = [])
    {
        return response()->json([
            'status' => 200,
            'data' => $data,
        ], 200);
    }

    public function responseMessageSuccess($message = 'success')
    {
        return response()->json([
            'status' => 200,
            'message' => $message,
        ], 200);
    }

    public function responseMessageValidate($errors = [])
    {
        return response()->json([
            'status' => 422,
            'errors' => $errors
        ], 422);
    }

    public function responseMessageBadrequest($message = 'badrequest')
    {
        return response()->json([
            'status' => 400,
            'message' => $message
        ], 400);
    }
}
