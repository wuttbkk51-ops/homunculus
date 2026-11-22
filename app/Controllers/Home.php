<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class Home extends BaseController
{
    public function index(): ResponseInterface|string
    {
        if (auth()->loggedIn()) {
            return redirect()->to('/dashboard');
        }

        return view('home');
    }
}
