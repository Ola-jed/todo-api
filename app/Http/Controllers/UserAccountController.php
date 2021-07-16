<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountDeleteRequest;
use App\Http\Requests\AccountUpdateRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserAccountController
 * @package App\Http\Controllers
 */
class UserAccountController extends Controller
{
    /**
     * @param AccountUpdateRequest $accountUpdateRequest
     * @return JsonResponse
     */
    public function updateAccount(AccountUpdateRequest $accountUpdateRequest): JsonResponse
    {
        $user = $accountUpdateRequest->user();
        if(Auth::attempt(['password' => $accountUpdateRequest->input('password'),'email' => $user->email]))
        {
            $newPwd = $accountUpdateRequest->input('new_password');
            try
            {
                User::whereEmail($user->email)
                    ->updateOrFail([
                    'name' => $accountUpdateRequest->input('name'),
                    'email' => $accountUpdateRequest->input('email'),
                    'password' => is_null($newPwd) || empty(trim($newPwd))
                        ? Hash::make($accountUpdateRequest->input('password'))
                        : Hash::make($newPwd)
                ]);
                return response()->json([
                    'message' => 'Account updated'
                ],Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            catch (Exception)
            {
                return response()->json([
                    'message' => 'Error'
                ],Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        return response()->json([
            'message' => 'Auth failed'
        ],Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param AccountDeleteRequest $deleteRequest
     * Deleting a user account
     * @return JsonResponse
     */
    public function deleteAccount(AccountDeleteRequest $deleteRequest): JsonResponse
    {
        $user = $deleteRequest->user();
        $hasDeleted = User::whereEmail($user->email)->delete();
        if(!$hasDeleted)
        {
            return response()->json([
               'message' => 'Could not delete the user'
            ]);
        }
        $user->tokens()->delete();
        return response()->json([
            'message' => 'User deleted'
        ]);
    }
}
