<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return redirect()->to('https://remsana.com');
        // return view('welcome_message');
    }
}
