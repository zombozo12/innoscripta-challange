<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Requests\Authentication\RegisterRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthenticationController extends Controller
{
    /**
     * Login API
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $response = new ApiResponse(now(), $request->fingerprint());

        // validate request
        $request->validated();

        $email = $request->json('email');
        $password = $request->json('password');

        try {
            $user = User::where('email', $email)->first();
            if (!($user and Hash::check($password, $user->password))) {
                return $response->setBadResponse([], ResponseAlias::HTTP_UNAUTHORIZED, "Email/password is incorrect.");
            }
        } catch (Exception $ex) {
            return $response->setBadResponse([
                'error_detail' => $ex->getMessage()
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        $token = $user->createToken('user-auth', [''])->plainTextToken;
        $split = explode('|', $token);

        $token_id = $split[0];
        $access_token = $split[1];

        return $response->setOKResponse([
            'user' => $user,
            'access_token' => $access_token
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function logout(Request $request): JsonResponse
    {
        $response = new ApiResponse(now(), $request->fingerprint());

        $request->user()->currentAccessToken()->delete();
        return $response->setOKResponse([
            'logout' => 'OK'
        ]);
    }

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $response = new ApiResponse(now(), $request->fingerprint());

        $request->validated();

        $name = $request->json('name');
        $email = $request->json('email');
        $password = $request->json('password');

        try {
            $user = new User;
            $user->name = $name;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->settings = json_encode([
                'theme' => 'dark'
            ]);
            $user->news_preferences = json_encode([
                'language' => 'en',
                'country' => 'us',
                'sources' => [],
                'categories' => [],
                'authors' => [],
            ]);
            $user->save();
        } catch (Exception $ex) {
            return $response->setBadResponse([
                'error_detail' => $ex->getMessage()
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response->setOKResponse([
            'user' => $user,
            'message' => 'Account has been created.'
        ]);
    }
}
