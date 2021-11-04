<?php

namespace App\Http\Controllers;

use App\Events\Message;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function attendIn(Request $request, $uid)
    {
        if (env("PUBLIC_KEY") != $request->key) {
            return response('', 401);
        }

        $user = User::where('card_uid', $uid)->first();

        if ($user) {
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'status' => 1,
            ]);

            event(new Message($user->username . " just checked in", "Arduino"));
            return response()->json(["message" => "Welcome " . $user->username]);
        } else {
            return response()->json(["message" => "Check card"]);
        }
    }

    public function leave(Request $request, $uid)
    {
        if (env("PUBLIC_KEY") != $request->key) {
            return response('', 401);
        }

        $user = User::where('card_uid', $uid)->first();

        if ($user) {
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'status' => 0,
            ]);

            event(new Message($user->username . " just checked out", "Arduino"));
            return response()->json(["message" => "Goodbye " . $user->username]);
        } else {
            return response()->json(["message" => "Check card"]);
        }

    }

    public function getAllAttendance(Request $request)
    {
        if (env("PUBLIC_KEY") != $request->key) {
            return response('', 401);
        }
        return response()->json(Attendance::with("user")->get());
    }
}
