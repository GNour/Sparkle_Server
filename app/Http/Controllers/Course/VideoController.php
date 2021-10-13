<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{

    /**
     * Store resource in user_watch_videos
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function watchVideo(Video $video)
    {

        $alreadyWatched = auth()->user()->videos()->where('video_id', $video->id)->get();
        // Checks if video is already watched before, else added it to watched videos
        if (!$alreadyWatched->isEmpty()) {
            return $alreadyWatched;
        } else {
            auth()->user()->videos()->attach($video, ["left_at" => null]);
        }

        return response()->json([
            "message" => "Added to watched videos",
            "video" => $video,
        ]);
    }

    /**
     * Update completed in user_watch_videos to 1
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function completeVideo(Video $video)
    {

        if (auth()->user()->videos()->updateExistingPivot($video->id, ["completed" => 1])) {
            return response()->json([
                "message" => "Marked video as completed",
            ]);
        }

        return response()->json([
            "message" => "Couldn't mark video as completed",
        ], 400);

    }

    /**
     * Update left at timestamp on user video.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function editVideoLeftAt(Request $request, Video $video)
    {
        $validator = Validator::make($request->all(), [
            'leftAt' => 'required|date_format:H:m:s',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        if (auth()->user()->videos()->updateExistingPivot($video->id, ["left_at" => $request->leftAt])) {
            return response()->json([
                "message" => "Video left at " . $request->leftAt,
            ]);
        }

        return response()->json([
            "message" => "Couldn't modify left at to video",
        ], 400);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadVideo(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'video' => 'required|mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts',
                'title' => 'required|string',
                'description' => 'required|string',
                'course_id' => 'required',
            ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        if ($request->hasFile('video')) {

            $video = $request->file('video');
            $name = $request->title . "_" . date('YmdHis') . "." . $video->getClientOriginalExtension();
            $destinationPath = public_path('/videos');
            $video->move($destinationPath, $name);

            $videoUploaded = new Video();
            $videoUploaded->title = $request->title;
            $videoUploaded->description = $request->description;
            $videoUploaded->course_id = $request->course_id;
            $videoUploaded->video = $name;
            $videoUploaded->save();

            return response()->json([
                'message' => "Uploaded Successfully!",
                'video' => $videoUploaded,
            ], 201);
        }

        return response()->json([
            'message' => "Couldn't Upload Video",
        ], 401);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadVideoViaUrl(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'video' => 'required|url',
                'title' => 'required|string',
                'description' => 'required|string',
                'course_id' => 'required',
            ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $videoUploaded = Video::create($validator->validated());

        return response()->json([
            'message' => "Uploaded Successfully!",
            'video' => $videoUploaded,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $video)
    {
        $validator = Validator::make($request->all(),
            [
                'title' => 'required|string',
                'description' => 'required|string',
                'course_id' => 'required',
            ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $video->update($validator->validated());

        return response()->json([
            'message' => 'Video edited Successfully',
            'video' => $video,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function destroy(Video $video)
    {
        $video->delete();

        return response()->json([
            'message' => 'video Deleted Successfully',
            'video' => $video,
        ], 200);
    }
}
