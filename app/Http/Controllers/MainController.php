<?php

namespace App\Http\Controllers;

use App\Bot\Received;
use App\Jobs\BotHandler;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function receive(Request $request)
    {
        $receiveds = Received::parseEntries($request);
        foreach ($receiveds as $received) {
            dispatch(new BotHandler($received));
        }

        return response();
    }

}
