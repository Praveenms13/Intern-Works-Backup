<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

class AuthenticatedSessionController extends Controller
{
       public function create(): View
    {
        return view('auth.login');
    }
    public function store(LoginRequest $request): RedirectResponse
    {
        print_r($_POST);
        $client = new Client();
        $options = [
            'multipart' => [
                [
                    'name' => 'username',
                    'contents' => 'praveen2003'
                ],
                [
                    'name' => 'password',
                    'contents' => 'pass133'
                ]
            ]
        ];
        $guzzleRequest = new GuzzleRequest('POST', 'https://restapi.praveenms.live/api/auth/login');
        $res = $client->sendAsync($guzzleRequest, $options)->wait();
        echo "<pre>";
        echo $res->getBody();
        echo "</pre>";
        exit;
        $request->authenticate();
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
