<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountDeleteRequest;
use App\Http\Requests\AccountUpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

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
