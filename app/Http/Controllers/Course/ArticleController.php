<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{

    /**
     * Store resource in user_read_articles
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function readArticle(Article $article)
    {

        $alreadyWatched = auth()->user()->articles()->where('article_id', $article->id)->get();
        // Checks if article is already read before, else add it to read articles
        if (!$alreadyWatched->isEmpty()) {
            return $alreadyWatched;
        } else {
            auth()->user()->articles()->attach($article);
        }

        return response()->json([
            "message" => "Added to read articles",
        ]);
    }

    /**
     * Update completed user_read_articles to 1
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function completeArticle(Article $article)
    {

        if (auth()->user()->articles()->updateExistingPivot($article->id, ["completed" => 1])) {
            return response()->json([
                "message" => "Marked article as completed",
            ]);
        }

        return response()->json([
            "message" => "Couldn't mark article as completed",
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
            'body' => 'required|string',
            'title' => 'required|string',
            'course_id' => 'required',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $article = Article::create($validator->validated());

        return response()->json([
            'message' => 'Article Created Successfully',
            'article' => $article,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required|string',
            'title' => 'required|string',
            'course_id' => 'required',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $article->update($validator->validated());

        return response()->json([
            'message' => 'Article edited Successfully',
            'article' => $article,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        $article->delete();

        return response()->json([
            'message' => 'Article Deleted Successfully',
            'article' => $article,
        ], 200);
    }
}
