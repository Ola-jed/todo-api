<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgottenPasswordRequest;
use App\Http\Requests\SignInRequest;
use App\Http\Requests\SignUpRequest;
use App\Mail\ForgottenPasswordMail;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class UserAuthController extends Controller
{
    /**
     * Creates a new user
     * @param SignUpRequest $signUpRequest
     * @return JsonResponse
     */
    public function signup(SignUpRequest $signUpRequest): JsonResponse
    {
        try
        {
            $user = User::createFromData($signUpRequest->validated());
            $token = $user->createToken($signUpRequest->input('device_name'))->plainTextToken;
            return response()->json([
                'user'       => $user,
                'token'      => $token,
                'token_type' => 'Bearer'
            ]);
        }
        catch(Exception)
        {
            return response()->json([
                'message' => 'Could not create the user'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Connect the user in the app
     * We should remember the user
     * @param SignInRequest $signInRequest
     * @return JsonResponse
     */
    public function signin(SignInRequest $signInRequest): JsonResponse
    {
        if(Auth::attempt($signInRequest->only(['email', 'password']), true))
        {
            $user = Auth::user();
            $token = $user->createToken($signInRequest->input('device_name'))->plainTextToken;
            return response()->json([
                'user'  => $user,
                'token' => $token
            ]);
        }
        return response()->json([
            'message' => 'Auth failed'
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Logout the authenticated user
     * @param Request $logoutRequest
     * @return JsonResponse
     */
    public function logout(Request $logoutRequest): JsonResponse
    {
        $user = $logoutRequest->user();
        $user->tokens()->delete();
        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    /**
     * Handle password reset
     * Create a new random password and send a mail to the user
     * @param ForgottenPasswordRequest $forgottenPasswordRequest
     * @return JsonResponse
     */
    public function passwordReset(ForgottenPasswordRequest $forgottenPasswordRequest): JsonResponse
    {
        $email = $forgottenPasswordRequest->input('email');
        $userExists = User::whereEmail($email)
            ->exists();
        if(!$userExists)
        {
            return response()->json([
                'message' => 'User does not exists'
            ], Response::HTTP_NOT_FOUND);
        }
        try
        {
            $pwd = Str::random();
            User::whereEmail($email)
                ->update(['password' => Hash::make($pwd)]);
            Mail::to($email)
                ->send(new ForgottenPasswordMail($pwd));
            return response()->json([
                'message' => 'Password reset'
            ]);
        }
        catch(Exception $exception)
        {
            return response()->json([
                'message' => 'Something weird happened ' . $exception->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
