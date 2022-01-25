<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgottenPasswordRequest;
use App\Mail\ForgottenPasswordMail;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ForgotPasswordController
 * Controller to handle user forgotten password
 * @package App\Http\Controllers\Auth
 */
class ForgotPasswordController extends Controller
{
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
