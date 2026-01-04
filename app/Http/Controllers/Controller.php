<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected $token;

    public function __construct()
    {
        $this->token = session('api_token');
    }

    protected function isAuthenticated(): bool
    {
        return session()->has('api_token');
    }
}
