<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
        ->with(['prompt' => 'select_account'])
        ->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::UpdateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]
        );

        Auth::login($user);

        
        return response()->view('loading-login'); // view untuk close popup
        
        }
    
    // Fungsi untuk logout
    public function logout(Request $request)
    {
        // Logout dari aplikasi Laravel
        Auth::logout();

        // Menghapus sesi yang terkait dengan Google (optional, bisa juga dihapus sessionnya)
        $request->session()->flush(); // Hapus seluruh session, termasuk token Google

        // Redirect ke halaman utama atau login
        return redirect('/'); // Ganti ke route yang sesuai
    }
}
