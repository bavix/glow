<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AbilityResource;
use App\Models\Ability;
use App\Models\User;
use App\Http\Requests\Auth\TokenRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    /**
     * @param TokenRequest $request
     * @return array
     * @throws ValidationException
     */
    public function token(TokenRequest $request): array
    {
        $user = User::whereEmail($request->email)
            ->firstOrFail();

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $tokenValue = $user->createToken(
            $request->device,
            $request->input('abilities', ['*'])
        );

        return [
            'token' => $tokenValue->plainTextToken,
            'abilities' => $tokenValue->accessToken->abilities ?? ['*'],
            'device' => $request->device,
            'user_id' => $user->getKey(),
        ];
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function abilities(): AnonymousResourceCollection
    {
        return AbilityResource::collection(Ability::all());
    }

}
