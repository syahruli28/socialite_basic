<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProvideCallback($provider)
    {
        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (Exception $e) {
            return redirect()->back();
        }

        // cari/buat user dan kirimkan parameter user dari socialite dan provider
        $authUser = $this->findOrCreateUser($user, $provider);
        // login user
        Auth()->login($authUser, true);
        // setelah login redirect ke dashboard
        return redirect()->route('dashboard');
    }

    public function findOrCreateUser($socialUser, $provider)
    {
        // Get Social Account
        $socialAccount = SocialAccount::where('provider_id', $socialUser->getId())
            ->where('provider_name', $provider)->first();

        // jika sudah ada
        if ($socialAccount) {
            // return user
            return $socialAccount->user;
        } else { // jika belum ada
            // user berdasarkan email
            $user = User::where('email', $socialUser->getEmail())->first();

            // jika tidak ada user
            if (!$user) {
                // buat user baru
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                ]);
            }

            // buat social account baru
            $user->socialAccount()->create([
                'provider_id' => $socialUser->getId(),
                'provider_name' => $provider,
            ]);

            // return user
            return $user;
        }
    }
}
