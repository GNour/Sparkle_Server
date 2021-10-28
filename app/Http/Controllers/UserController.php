<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     *
     * Protected using auth.role middleware.
     */
    public function getAllUsers()
    {
        return response()->json(User::all()->toArray());
    }

    /**
     * Display a listing of the users with their teams.
     *
     * @return \Illuminate\Http\Response
     *
     * Protected using auth.role middleware.
     */
    public function getUsersWithTeam()
    {
        return response()->json(User::with(["team"])->get());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     *
     * Policy Protected check UserPolicy
     */
    public function show(User $user)
    {
        if (auth()->user()->can('view', $user)) {
            return response()->json($user->load(["team", "tasks", "courses", "notes"]));
        }
        return response()->json(["message" => "Not Authorized!"], 403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     *
     * Policy Protected check UserPolicy
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|between:2,100',
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'gender' => 'required',
            'phone_number' => 'required|between:7,15',
            'email' => 'required|string|email|max:100|unique:users',
            'profile_picture' => 'nullable',
            'role' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        if (auth()->user()->can('update', $user)) {
            $user->update($validator->validated());

            return response()->json([
                'message' => 'User edited successfully',
                'updated' => $user,
            ], 200);
        }
        return response()->json(["message" => "Not Authorized!"], 403);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     *
     * Protected using auth.role middleware.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'message' => 'User successfully deleted',
            'user' => $user,
        ], 201);
    }

    /**
     * Fetch Users Basic Info
     * Public Route protected with a key
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUsersBasicInfo(Request $request)
    {
        if (env("PUBLIC_KEY") == $request->key) {
            return response()->json(
                User::get(["id", "username"])
            );
        }
    }
}
