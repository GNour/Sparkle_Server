<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Task;
use App\Models\Team;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{

    /**
     * Display the specified resource for the user logged in to start the task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        // If user is staff, only his assigned task details will be returned
        if (auth()->user()->role == "Staff") {
            return response()->json([
                "task" => $task->users()
                    ->wherePivot("task_id", $task->id)
                    ->wherePivot("user_id", auth()->user()->id)
                    ->get(["id"]),
                "taskable" => $task->taskable()->get(),
            ]);
        }

        // Else all users assigned tasks will be returned
        return response()->json([
            "task" => $task->users()
                ->wherePivot("task_id", $task->id)
                ->get(["id"]),
            "taskable" => $task->taskable()->get(),
        ]);

    }

    /**
     * Fetch all tasks.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllTasks()
    {
        return response()->json(Task::all()->toArray());
    }

    /**
     * Fetch unassigned tasks.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUnassignedTasks()
    {
        return response()->json(Task::doesntHave('users')->get());
    }

    /**
     * Fetch unfinished tasks with users that completed and not completed the task.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUnfinishedTasks()
    {
        return response()->json(Task::whereRelation('users', 'completed', 0)->with('users:id,username,profile_picture,team_id')->get());
    }

    /**
     * Fetch finished tasks.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFinishedTasks()
    {
        return response()->json(Task::whereRelation('users', 'completed', 0)->with('users:id,username,profile_picture,team_id')->get());
    }

    /**
     * Fetch assigned tasks.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAssignedTasks()
    {
        if (auth()->user()->role == "Staff") {
            return response()->json(auth()->user()->tasks->wherePivot("complete", 0));
        }
        return response()->json(Task::whereRelation('users', 'completed', 0)->with('users:id,username,profile_picture,team_id')->get());
    }

    /**
     * Change completed from 0 to 1 after user finish a task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function completeTask(Task $task)
    {
        $completed = $task->users()->updateExistingPivot(auth()->user()->id, ["completed" => 1]);

        if ($completed) {
            return response()->json([
                "message" => "Task completed",
                "task" => $task,
            ], 200);
        } else {
            return response()->json([
                "message" => "Task couldn't be completed",
                "task" => $task,
            ], 200);
        }
    }

    /**
     * Assign a task to users or/and teams.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function assignTask(Request $request, Task $task)
    {

        $usersArray = json_decode($request->users) ? json_decode($request->users) : [];
        $teamsArray = json_decode($request->teams) ? json_decode($request->teams) : [];

        // Confirm reciving only one array
        if ($usersArray != [] && $teamsArray != []) {
            return response()->json([
                "message" => "You can't assign simultaneously",
            ], 403);
        }

        $assignedTo = $task->users()->whereIn('id', $usersArray)->orWhereIn('team_id', $teamsArray)->pluck('id')->toArray();

        /*
         * Get the users,teams that are lead by the leader logged in, OR find all if the user is a manager/admin
         * Check Below functions
         */
        $users = $this->findUsers($usersArray, $assignedTo);
        $teams = $this->findTeams($teamsArray, $assignedTo);

        if ($users->isEmpty() && $teams->isEmpty()) {
            return response()->json([
                "message" => "Task wasn't assigned, Check the selected options",
            ], 406);
        }

        $task->users()->attach($users, ["deadline" => $request->deadline]);
        $task->users()->attach($teams, ["deadline" => $request->deadline]);

        return response()->json([
            "message" => "Assigned Task successfully",
        ], 200);
    }

    /**
     * Unassign a task to users or/and teams.
     *
     *
     * @param  \App\Models\Task  $task
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function unassignTask(Request $request, Task $task)
    {
        $usersArray = json_decode($request->users) ? json_decode($request->users) : [];
        $teamsArray = json_decode($request->teams) ? json_decode($request->teams) : [];
        /*
         * Get the users,teams that are lead by the leader logged in, OR find all if the user is a manager/admin
         * Check Below functions
         */

        $assignedTo = $task->users()->whereIn('id', $usersArray)->orWhereIn('team_id', $teamsArray)->pluck('id')->toArray();

        // Inverse of the parent function find... Returns users with task assigned for them
        $users = $this->findUsersInverse($usersArray, $assignedTo);
        $teams = $this->findTeamsInverse($teamsArray, $assignedTo);

        $task->users()->detach($users);
        $task->users()->detach($teams);

        return response()->json([
            "message" => "Unassigned Task successfully",
        ], 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'taskable_type' => 'required|string',
            'taskable_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        switch ($request->taskable_type) {
            case "Course":
                $taskable = Course::find($request->taskable_id);
                break;
            case "Todo":
                $taskable = Todo::find($request->taskable_id);
                break;
            default:
                $taskable = null;
        }

        $task = $taskable->tasks()->create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => auth()->user()->id,
        ]);

        return response()->json([
            'message' => 'Task Created Successfully',
            'task' => $task,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     *
     * Protected by a TaskPolicy@deleteOrUpdate
     *
     */
    public function update(Request $request, Task $task)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'taskable_type' => 'required|string',
            'taskable_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        switch ($request->taskable_type) {
            case "Course":
                $taskable = Course::find($request->taskable_id);
                break;
            case "Todo":
                $taskable = Todo::find($request->taskable_id);
                break;
            default:
                $taskable = null;
        }

        if (auth()->user()->can("deleteOrUpdate", $task)) {
            $task->taskable()->associate($taskable)->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'message' => 'Task Edited Successfully',
                'task' => $task,
            ], 200);
        }

        return response()->json(["message" => "Not Authorized!"], 403);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     *
     * Protected by a TaskPolicy@deleteOrUpdate
     *
     */
    public function destroy(Task $task)
    {
        if (auth()->user()->can("deleteOrUpdate", $task)) {
            $task->delete();

            return response()->json([
                'message' => 'Task Deleted Successfully',
                'task' => $task,
            ], 200);
        }

        return response()->json(["message" => "Not Authorized!"], 403);
    }

    protected function findUsers($usersArray, $assignedTo)
    {

        if (auth()->user()->role == "Leader") {
            return User::where('team_id', auth()->user()->team_id)->whereIn('id', $usersArray)->whereNotIn('id', $assignedTo)->get();
        }
        return User::whereIn('id', $usersArray)->whereNotIn('id', $assignedTo)->get();
    }

    protected function findTeams($teamsArray, $assignedTo)
    {
        if (auth()->user()->role == "Leader") {
            // Get the teams that are leaded by the logged in user
            $teamsArrayLeadedByUser = Team::where('leader_id', auth()->user()->id)->whereIn('id', $teamsArray)->pluck('id');

            return User::whereIn('team_id', $teamsArrayLeadedByUser)->whereNotIn('id', $assignedTo)->get();
        }
        return User::whereIn('team_id', $teamsArray)->whereNotIn('id', $assignedTo)->get();
    }

    protected function findUsersInverse($usersArray, $assignedTo)
    {

        if (auth()->user()->role == "Leader") {
            return User::where('team_id', auth()->user()->team_id)->whereIn('id', $usersArray)->whereIn('id', $assignedTo)->get();
        }
        return User::whereIn('id', $usersArray)->whereIn('id', $assignedTo)->get();
    }

    protected function findTeamsInverse($teamsArray, $assignedTo)
    {
        if (auth()->user()->role == "Leader") {
            // Get the teams that are leaded by the logged in user
            $teamsArrayLeadedByUser = Team::where('leader_id', auth()->user()->id)->whereIn('id', $teamsArray)->pluck('id');

            return User::whereIn('team_id', $teamsArrayLeadedByUser)->whereIn('id', $assignedTo)->get();
        }
        return User::whereIn('team_id', $teamsArray)->whereIn('id', $assignedTo)->get();
    }
}
