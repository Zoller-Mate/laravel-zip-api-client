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
            $response = Http::api()->post('/login', [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            // Debug: Log the response
            \Log::info('Login response status: ' . $response->status());
            \Log::info('Login response body: ' . $response->body());

            if ($response->successful()) {
                $responseBody = json_decode($response->body());
                
                // Debug: Check response structure
                \Log::info('Response data: ' . json_encode($responseBody));
                
                // Check if token exists (either directly or in data object)
                $token = $responseBody->token ?? $responseBody->data->token ?? null;
                
                if (empty($token)) {
                    \Log::warning('Missing token in response');
                    return back()->withErrors(['email' => $responseBody->message ?? 'A szerver nem adott vissza érvényes tokent.']);
                }

                // Store session data
                session([
                    'api_token' => $token,
                    'user_name' => $responseBody->name ?? $responseBody->data->name ?? $responseBody->user->name ?? 'User',
                    'user_email' => $responseBody->email ?? $responseBody->data->email ?? $responseBody->user->email ?? $request->email,
                ]);

                // Regenerate session for security
                $request->session()->regenerate();

                \Log::info('Login successful, token: ' . substr($token, 0, 20) . '...');
                
                return redirect()->intended(route('counties.index', absolute: false));
            }

            \Log::warning('Login failed with status: ' . $response->status());
            return back()->withErrors(['email' => $response->json('message') ?? 'Hibás bejelentkezési adatok.']);
            
        } catch (\Exception $e) {
            \Log::error('Login exception: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Hiba történt a bejelentkezés során: ' . $e->getMessage()]);
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
