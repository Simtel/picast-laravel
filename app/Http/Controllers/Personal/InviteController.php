<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;

class InviteController extends Controller
{
    public function index()
    {
        return view('personal.invite');
    }
}
