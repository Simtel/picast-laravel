<?php

namespace App\Http\Controllers\Personal\Telegram;

use App\Models\Images;
use Illuminate\Http\Request;
use App\Http\Controllers\Personal\Controller;

class TelegramController extends Controller
{
    public function test () {
        $r = \Telegram::getUpdates();
        dd($r);
    }
}
