<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{

    /**
     * Store resource in user_take_courses
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function takeCourse(Course $course)
    {

        $alreadyTaken = auth()->user()->courses()->where('course_id', $course->id)->get();
        // Checks if course is already taken before, else add it to take courses
        if (!$alreadyTaken->isEmpty()) {
            return response()->json([
                "message" => "Good luck",
                "course" => $course,
                "videos" => $course->videos()->get(),
                "articles" => $course->articles()->get(),
                "quizzes" => $course->quizzes()->get(),
            ]);
        } else {
            auth()->user()->courses()->attach($course, ["grade" => 0]);
        }

        return response()->json([
            "message" => "Added to take courses",
            "course" => $course,
            "videos" => $course->videos()->get(),
            "articles" => $course->articles()->get(),
            "quizzes" => $course->quizzes()->get(),
        ]);
    }

    /**
     * Update completed in user_take_courses to 1
     *
     * @param  \App\Models\Course  $course
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function completeCourse(Request $request, Course $course)
    {

        if (auth()->user()->courses()->updateExistingPivot($course->id, ["completed" => 1, "grade" => $request->grade])) {
            return response()->json([
                "message" => "Marked course as completed and assigned grade " . $request->grade,
            ]);
        }

        return response()->json([
            "message" => "Couldn't mark course as completed",
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
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $course = Course::create(array_merge($validator->validated(), ["created_by" => auth()->user()->id]));

        return response()->json([
            'message' => 'Course Created Successfully',
            'Course' => $course,
        ], 201);
    }

    /**
     * Display the specified resource.
     * User fetch course if he have it as a task, Others fetch any
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {

        if (auth()->user()->role == "Staff") {
            $isCourseInUserTask = in_array($course->id, auth()->user()->tasks()
                    ->wherePivotIn("task_id", $course->tasks()->pluck("id"))
                    ->pluck("taskable_id")
                    ->toArray());

            if (!$isCourseInUserTask) {
                return response()->json([
                    "message" => "You are not allowed to view this course",
                ]);
            }

        }
        return response()->json([
            "course" => $course,
            "videos" => $course->videos()->with("user")->get(),
            "articles" => $course->articles()->with("user")->get(),
            "quizzes" => $course->quizzes()->with("user")->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        if (auth()->user()->can("deleteOrUpdate", $course)) {
            $course->update($validator->validated());

            return response()->json([
                'message' => 'Course edited Successfully',
                'Course' => $course,
            ], 200);
        }

        return response()->json(["message" => "Not Authorized!"], 403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        if (auth()->user()->can("deleteOrUpdate", $course)) {
            $course->delete();
            return response()->json([
                'message' => 'Course successfully deleted',
                'course' => $course,
            ], 200);
        }

        return response()->json(["message" => "Not Authorized!"], 403);
    }

    /**
     * Fetch Courses to assign to tasks.
     * Public Route protected with a key
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCoursesForTask(Request $request)
    {
        if (env("PUBLIC_KEY") == $request->key) {
            return response()->json(
                Course::withCount(["quizzes", "articles", "videos"])->get(["id", "name", "description"])
            );
        }
    }
}
