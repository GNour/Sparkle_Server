<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnswerController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Question $question)
    {
        $validator = Validator::make($request->all(), [
            'answer' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $answer = Answer::create(array_merge($validator->validated(), ["question_id" => $question->id]));

        if ($request->is_answer) {
            $question->update(["answer_id" => $answer->id]);
            return response()->json([
                'message' => 'Answer Created and Assigned as question answer Successfully',
                'answer' => $answer,
            ], 201);
        }
        return response()->json([
            'message' => 'Answer Created Successfully',
            'answer' => $answer,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function destroy(Answer $answer)
    {
        $answer->delete();

        return response()->json([
            'message' => 'Note Deleted Successfully',
            'answer' => $answer,
        ], 200);
    }
}
