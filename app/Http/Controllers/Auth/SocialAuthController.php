<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        $socialUser = Socialite::driver($provider)->user();

        $account = SocialAccount::where('provider', $provider)
                                ->where('provider_id', $socialUser->getId())
                                ->first();

        if ($account) {
            Auth::login($account->user);
            return redirect('/');
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if (! $user) {
            $defaultRole = Role::where('name', 'user')->first();
            $user = User::create([
                'name'              => $socialUser->getName() ?? $socialUser->getNickname(),
                'email'             => $socialUser->getEmail(),
                'password'          => bcrypt(str()->random(32)),
                'role_id'           => $defaultRole?->id,
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);
        }

        $user->socialAccounts()->create([
            'provider'    => $provider,
            'provider_id' => $socialUser->getId(),
            'token'       => $socialUser->token,
        ]);

        Auth::login($user);
        return redirect('/');
    }

    private function validateProvider(string $provider): void
    {
        abort_unless(in_array($provider, ['google', 'github']), 404);
    }
}
