<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $response = Http::api()->post('/user/login', [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if ($response->successful()) {
                $responseBody = json_decode($response->body());
                if (empty($responseBody->data)) {
                    return back()->withErrors(['message' => $responseBody->message ?? 'Ismeretlen hiba']);
                }

                session([
                    'api_token' => $responseBody->data->token,
                    'user_name' => $responseBody->data->name,
                    'user_email' => $responseBody->data->email,
                ]);

                return redirect()->intended(route('counties.index', absolute: false));
            }

            return back()->withErrors(['email' => $response->json('message') ?? 'Hibás bejelentkezési adatok.']);
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Hiba történt a bejelentkezés során.']);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        session()->forget('api_token');
        session()->forget('user_name');
        session()->forget('user_email');

        return redirect('/');
    }
}
