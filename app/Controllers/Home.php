<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Home extends BaseController
{
    public function index(): string|RedirectResponse
    {
        if (auth()->loggedIn()) {
            return redirect()->to('/admin');
        }

        return view('home', [
            'title' => lang('Home.title'),
        ]);
    }
}
