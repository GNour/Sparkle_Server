<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{

    public function getTeams()
    {
        return response()->json(Team::all()->toArray());
    }

    public function getTeamsWithMembers()
    {
        return response()->json(Team::with(["members", "leader", "manager"])->get());
    }

    public function getUserTeam()
    {
        return response()->json(auth()->user()->team);
    }

    public function createTeam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:1,100',
            'description' => 'required',
            'leader_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $team = Team::create(array_merge($validator->validated(), ["manager_id" => auth()->user()->id]));

        return response()->json([
            'message' => 'Team successfully created',
            'team' => $team,
        ], 201);
    }

    public function deleteTeam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $team = Team::find($request->team_id);
        $team->delete();

        return response()->json([
            'message' => 'Team successfully deleted',
            'team' => $team,
        ], 201);
    }

    public function updateTeam(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:1,100',
            'description' => 'required',
            'leader_id' => 'required',
            'manager_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $team = Team::where("id", $id)->update($validator->validated());

        return response()->json([
            'message' => 'Team edited successfully',
            'team' => $team,
        ], 201);
    }
}
