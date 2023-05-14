<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\ChangeAccountSettingsRequest;
use App\Http\Requests\Account\ChangeNewsPreferenceRequest;
use App\Http\Requests\Account\ChangePasswordRequest;
use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AccountController extends Controller
{
    /**
     * @throws Exception
     */
    public function me(Request $request): JsonResponse
    {
        $response = new ApiResponse(now(), $request->fingerprint());

        return $response->setOKResponse([
            'user' => $request->user()
        ]);
    }

    /**
     * @throws Exception
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $response = new ApiResponse(now(), $request->fingerprint());

        $request->validated();

        $user = $request->user();
        $new_password = $request->json('new_password');
        $old_password = $request->json('old_password');

        if (!($user and Hash::check($old_password, $user->password))) {
            return $response->setBadResponse([], ResponseAlias::HTTP_UNAUTHORIZED, "Old password is incorrect.");
        }

        $user->password = Hash::make($new_password);
        $user->save();

        return $response->setOKResponse([
            'user' => $request->user(),
            'message' => 'Password changed successfully.'
        ]);
    }

    /**
     * @throws Exception
     */
    public function changeNewsSettings(ChangeNewsPreferenceRequest $request): JsonResponse
    {
        $response = new ApiResponse(now(), $request->fingerprint());

        $request->validated();

        $user = $request->user();
        $user->news_preferences = $request->json('news_preference');
        $user->save();

        return $response->setOKResponse([
            'user' => $request->user(),
            'message' => 'News preference changed successfully.'
        ]);
    }

    /**
     * @throws Exception
     */
    public function changeAccountSettings(ChangeAccountSettingsRequest $request): JsonResponse
    {
        $response = new ApiResponse(now(), $request->fingerprint());

        $request->validated();

        $user = $request->user();
        $user->settings = $request->json('settings');
        $user->save();

        return $response->setOKResponse([
            'user' => $request->user(),
            'message' => 'Account settings changed successfully.'
        ]);
    }
}
