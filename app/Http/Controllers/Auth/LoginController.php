<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create(): RedirectResponse
    {
        return redirect()->route('home', ['login' => 1]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = [
            'email' => trim((string) $validated['email']),
            'password' => $validated['password'],
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            if ($request->isMethod('post') && User::where('email', $credentials['email'])->doesntExist()) {
                Artisan::call('db:seed', [
                    '--class' => DatabaseSeeder::class,
                    '--force' => true,
                ]);

                if (Auth::attempt($credentials, $request->boolean('remember'))) {
                    $request->session()->regenerate();

                    return redirect()->intended(route('home'));
                }
            }

            return back()->withErrors([
                'email' => __('ui.messages.invalid_credentials'),
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', __('ui.messages.logged_out'));
    }
}
