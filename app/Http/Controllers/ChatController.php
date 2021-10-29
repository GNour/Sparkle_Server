<?php

namespace App\Http\Controllers;

use App\Events\Message;
use App\Models\Message as ModelsMessage;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    public function sendMessage(Request $request)
    {
        $message = ModelsMessage::create(array_merge($request->all()));
        event(new Message($request->message, $request->from));

        return response()->json($message);
    }

    public function getMessages()
    {
        return response()->json(
            ModelsMessage::with(["from"])->get()
        );
    }

    public function readMessages(User $user)
    {
        return response()->json(ModelsMessage::where('from', $user->id)->where('to', auth()->user()->id)->update(["read" => 1]));
    }
}
