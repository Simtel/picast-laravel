<?php

namespace App\Http\Controllers\Telegram;


use App\Http\Controllers\Controller;

class TelegramController extends Controller
{
    public function test () {
        $r = \Telegram::getUpdates();
        dd($r);
    }
}
