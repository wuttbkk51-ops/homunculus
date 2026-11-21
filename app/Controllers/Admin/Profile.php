<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Profile extends BaseController
{
    public function index(): string
    {
        return view('profile/index', [
            'title' => lang('Profile.title'),
        ]);
    }
}
