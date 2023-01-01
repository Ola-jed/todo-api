<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountDeleteRequest;
use App\Http\Requests\AccountUpdateRequest;
use App\Models\User;
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
        return response()->json($request->user());
    }

    /**
     * @param AccountUpdateRequest $accountUpdateRequest
     * @return \Illuminate\Http\Response
     */
    public function updateAccount(AccountUpdateRequest $accountUpdateRequest): \Illuminate\Http\Response
    {
        $user = $accountUpdateRequest->user();
        $credentials = ['email' => $user->email, 'password' => $accountUpdateRequest->input('password')];
        if (Auth::guard('web')->attempt($credentials))
        {
            $newPwd = $accountUpdateRequest->input('new_password');
            $user->name = $accountUpdateRequest->input('name');
            $user->email = $accountUpdateRequest->input('email');
            $user->password = Hash::make(
                is_null($newPwd) || empty(trim($newPwd))
                    ? $accountUpdateRequest->input('password')
                    : $newPwd
            );
            $user->saveOrFail();
            return response()->noContent();
        }
        return response()->noContent(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param AccountDeleteRequest $deleteRequest
     * Deleting a user account
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function deleteAccount(AccountDeleteRequest $deleteRequest): \Illuminate\Http\Response
    {
        $user = $deleteRequest->user();
        $credentials = ['email' => $user->email, 'password' => $deleteRequest->input('password')];
        if (!Auth::guard('web')->attempt($credentials))
        {
            return response()->noContent(Response::HTTP_UNAUTHORIZED);
        }
        User::whereEmail($user->email)->deleteOrFail();
        $user->tokens()->delete();
        return response()->noContent();
    }
}
