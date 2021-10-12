<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Task;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

        $task->taskable()->associate($taskable)->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Task Created Successfully',
            'task' => $task,
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json([
            'message' => 'Task Deleted Successfully',
            'task' => $task,
        ], 200);
    }
}
