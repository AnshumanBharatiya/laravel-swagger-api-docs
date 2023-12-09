<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
 * @OA\Post(
 *     path="/api/register",
 *     operationId="registerUser",
 *     tags={"Register"},
 *     summary="Register a new user",
 *     description="User Registration Endpoint",
 *     @OA\RequestBody(
 *         @OA\JsonContent(),
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"name","email","password","password_confirmation"},
 *                 @OA\Property(property="name",type="text"),
 *                 @OA\Property(property="email",type="text"),
 *                 @OA\Property(property="password",type="password"),
 *                 @OA\Property(property="password_confirmation",type="password"),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response="201",
 *         description="User Registered Successfully",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *       response="200",
 *       description="Registered Successfull",
 *       @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response="422",
 *         description="Unprocessable Entity",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response="400",
 *         description="Bad Request",
 *         @OA\JsonContent()
 *     ),
 * )
 */



    public function register(Request $request){
        $validated = $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed'
            ]
        );
        $data = $request->all();
        $data['password']=Hash::make($data['password']);
        $user = User::create($data);
        $sucess['token'] = $user->createToken('authToken')->accessToken;
        $sucess['name'] = $user->name;
        return response()->json(['sucess'=>$sucess]);
    }

     //  Login API 
    /**
     * @OA\Post(
     *     path="/api/login",
     *     operationId="loginUser",
     *     tags={"Login"},
     *     summary="Login a user",
     *     description="User Login Endpoint",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email","password"},
     *                 @OA\Property(property="email",type="text"),
     *                 @OA\Property(property="password",type="password"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="User Login Successfully",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *       response="200",
     *       description="Login Successfull",
     *       @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad Request",
     *         @OA\JsonContent()
     *     ),
     * )
     */

    public function login(Request $request){
        $validated = $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required'
            ]
        );
        if(!auth()->attempt($validated)){
                return response()->json(["error" => "Unauthorised"], 401);
        }else{
            $sucess['token'] = auth()->user()->createToken('authToken')->accessToken;
            $sucess['user'] = auth()->user();

            return response()->json(["success" => $sucess], 200);
        }
    }
    
}