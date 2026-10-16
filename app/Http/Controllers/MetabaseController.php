<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;

class MetabaseController extends Controller
{
    public function getToken()
    {
        try {
            $secretKey = "c259bfcd134ed181f5bbaf5b187537babf7f23f184f63044e211221ea43fc81a";

            $payload = [
                'resource' => ['dashboard' => 34],
                'params' => (object)[],
                'exp' => time() + (10 * 60)
            ];

            $token = JWT::encode($payload, $secretKey, 'HS256');

            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}