<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

abstract class Controller
{
    /**
     * Return response success semua fungsi.
     */
    public function succesFunction(array $data = [], $response = 200)
    {
        if ($data) {
            return response()->json($data, $response);
        } else {

            return response()->json([
                'message' => 'Success',
            ], $response);
        }
    }

    /**
     * Return response fail semua fungsi.
     */
    public function failedFunction(array $data = [], $response = 400)
    {
        if ($data) {
            return response()->json($data, $response);
        } else {

            return response()->json([
                'message' => 'Something wrong!',
            ], $response);
        }
    }
}
