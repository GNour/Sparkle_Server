<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{

    /**
     * Store resource in user_take_quizzes
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function takeQuiz(Quiz $quiz)
    {

        $alreadyWatched = auth()->user()->quizzes()->where('quiz_id', $quiz->id)->get();
        // Checks if quiz is already taken before, else add it to take quizzes
        if (!$alreadyWatched->isEmpty()) {
            return $alreadyWatched;
        } else {
            auth()->user()->quizzes()->attach($quiz, ["grade" => 0]);
        }

        return response()->json([
            "message" => "Added to take quizzes",
        ]);
    }

    /**
     * Update completed in user_take_quizzes to 1
     *
     * @param  \App\Models\Quiz  $quiz
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function completeQuiz(Request $request, Quiz $quiz)
    {

        if (auth()->user()->quizzes()->updateExistingPivot($quiz->id, ["completed" => 1, "grade" => $request->grade])) {
            return response()->json([
                "message" => "Marked quiz as completed and assigned grade " . $request->grade,
            ]);
        }

        return response()->json([
            "message" => "Couldn't mark quiz as completed",
        ], 400);

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
