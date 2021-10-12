<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'weight' => 'required',
            'title' => 'required|string',
            'course_id' => 'required',
            'description' => 'required|string',
            'limit' => 'required|date_format:H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $quiz = Quiz::create($validator->validated());

        return response()->json([
            'message' => 'Quiz Created Successfully',
            'quiz' => $quiz,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function show(Quiz $quiz)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Quiz $quiz)
    {
        $validator = Validator::make($request->all(), [
            'weight' => 'required',
            'title' => 'required|string',
            'course_id' => 'required',
            'description' => 'required|string',
            'limit' => 'required|date_format:H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $quiz->update($validator->validated());

        return response()->json([
            'message' => 'Quiz edited Successfully',
            'quiz' => $quiz,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return response()->json([
            'message' => 'Quiz Deleted Successfully',
            'quiz' => $quiz,
        ], 200);
    }
}
