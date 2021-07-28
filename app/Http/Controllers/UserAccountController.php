<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountDeleteRequest;
use App\Http\Requests\AccountUpdateRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     * Get authenticated user account
     * @param Request $request
     * @return JsonResponse
     */
    public function getAccount(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user()
        ]);
    }

    /**
     * @param AccountUpdateRequest $accountUpdateRequest
     * @return JsonResponse
     */
    public function updateAccount(AccountUpdateRequest $accountUpdateRequest): JsonResponse
    {
        $user = $accountUpdateRequest->user();
        if(Auth::guard('web')->attempt(['email' => $user->email, 'password' => $accountUpdateRequest->input('password')]))
        {
            $newPwd = $accountUpdateRequest->input('new_password');
            try
            {
                $user->name = $accountUpdateRequest->input('name');
                $user->email = $accountUpdateRequest->input('email');
                $user->password = Hash::make(
                    is_null($newPwd) || empty(trim($newPwd))
                        ? $accountUpdateRequest->input('password')
                        : $newPwd
                );
                $user->saveOrFail();
                return response()->json([
                    'message' => 'Account updated'
                ]);
            }
            catch(Exception)
            {
                return response()->json([
                    'message' => 'Error'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        return response()->json([
            'message' => 'Auth failed'
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param AccountDeleteRequest $deleteRequest
     * Deleting a user account
     * @return JsonResponse
     */
    public function deleteAccount(AccountDeleteRequest $deleteRequest): JsonResponse
    {
        $user = $deleteRequest->user();
        if(!Auth::guard('web')->attempt(['email' => $user->email, 'password' => $deleteRequest->input('password')]))
        {
            return response()->json([
                'message' => 'Auth failed'
            ], Response::HTTP_UNAUTHORIZED);
        }
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
