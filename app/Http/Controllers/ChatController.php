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
        $message = ModelsMessage::create(array_merge($request->all(), ["read" => 0]));
        event(new Message($request->message, $request->from, $request->to));

        return response()->json($message);
    }

    public function getMessages()
    {
        return response()->json(
            ModelsMessage::
                where("to", auth()->user()->id)
                ->orWhere("from", auth()->user()->id)
                ->get()
                ->load(["from"])
                ->groupBy("from")
        );
    }

    public function readMessages(User $user)
    {
        return response()->json(ModelsMessage::where('from', $user->id)->where('to', auth()->user()->id)->update(["read" => 1]));
    }
}
