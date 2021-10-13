<?php

namespace App\Http\Controllers\Todo;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{

    /**
     * Display the specified resource.
     * User fetch todo if he have it as a task, Others fetch any
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function show(Todo $todo)
    {
        if (auth()->user()->role == "Staff") {
            $isTodoInUserTasks = in_array($todo->id, auth()->user()->tasks()
                    ->wherePivotIn("task_id", $todo->tasks()->pluck("id"))
                    ->pluck("taskable_id")
                    ->toArray());

            if (!$isTodoInUserTasks) {
                return response()->json([
                    "message" => "You are not allowed to view this todo",
                ]);
            }
        }
        return response()->json($todo);
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
            'title' => 'required|string|between:1,100',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $todo = Todo::create(array_merge($validator->validated(), ["created_by" => auth()->user()->id]));

        return response()->json([
            'message' => 'Todo Created Successfully',
            'todo' => $todo,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Todo $todo)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:1,100',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        if (auth()->user()->can("deleteOrUpdate", $todo)) {
            $todo->update($validator->validated());

            return response()->json([
                'message' => 'Todo edited successfully',
                'updated' => $todo,
            ], 200);
        }

        return response()->json(["message" => "Not Authorized!"], 403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Todo $todo)
    {
        if (auth()->user()->can("deleteOrUpdate", $todo)) {
            $todo->delete();
            return response()->json([
                'message' => 'Todo successfully deleted',
                'todo' => $todo,
            ], 200);

        }

        return response()->json(["message" => "Not Authorized!"], 403);
    }
}
