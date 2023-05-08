<?php

namespace App\Http\Controllers;
use App\Models\User;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class UsersController extends Controller

{
    public function login(Request $request)
    {
        $this->validate($request, [
            'EmployeeCode' => 'required',
            'Password' => 'required'
        ]);
        $user = User::where('EmpCode', $request->EmployeeCode)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'No user found with this email!'
            ],404);
        }
        $secret_Key = '8UfsvwRpqUkM1Zm8oKSuxblxYXKXr6FMkZppE2jEbI8qAxpaJikwc7oRCs1LPK7F';
        $customClaims = array(
            'alg' => 'HS256',
            'typ' => 'JWT',
            'iss' => $secret_Key,
            'exp' => time() + 600,
        );

        //$customClaims = JWTFactory::customClaims($customClaims);
        $payload = JWTFactory::make($customClaims);
        $token = JWTAuth::encode($payload);
        return $token;
        $date   = new DateTimeImmutable();
        $expire_at     = $date->modify('+6 minutes')->getTimestamp();
        $domainName = "your.domain.name";
        $username   = $request->EmployeeCode;
        $request_data = [
            'iat'  => $date->getTimestamp(),         // Issued at: time when the token was generated
            'iss'  => $domainName,                       // Issuer
            'nbf'  => $date->getTimestamp(),         // Not before
            'exp'  => $expire_at,                           // Expire
            'userName' => $username,                     // User name
        ];

        $token = JWT::encode(
            $request_data,
            $secret_Key,
            'HS512'
        );
        return $token;


        if ($token = JWTAuth::attempt(['EmpCode' => $request->EmployeeCode, 'password' => $request->Password])) {
            return response()->json([
                'status' => 'success',
                'token' => $token
            ],200);
        } else {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'Wrong email or password!'
            ],401);
        }

    }


}
