<?php

namespace App\Http\Controllers;

use App\Events\Message;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function attendIn(Request $request, User $user)
    {
        if (env("PUBLIC_KEY") != $request->key) {
            return response('', 401);
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'status' => 1,
        ]);

        event(new Message($user->username . " just checked in", "ARDUINO<FINGERPRINT>"));
        return response()->json(["message" => "Welcome " . $user->username]);
    }

    public function leave(Request $request, User $user)
    {
        if (env("PUBLIC_KEY") != $request->key) {
            return response('', 401);
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'status' => 0,
        ]);

        event(new Message($user->username . " just checked out", "ARDUINO<FINGERPRINT>"));
        return response()->json(["message" => "Goodbye " . $user->username]);
    }

    public function getAllAttendance(Request $request)
    {
        if (env("PUBLIC_KEY") != $request->key) {
            return response('', 401);
        }
        return response()->json(Attendance::with("user")->get());
    }
}
