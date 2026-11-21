<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Settings extends BaseController
{
    public function index(): string
    {
        return view('settings/index', [
            'title' => lang('Settings.title'),
        ]);
    }
}
