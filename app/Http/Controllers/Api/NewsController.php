<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NewsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(News::latest()->paginate(10));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $news = auth()->user()->news()->create($validated);

        return response()->json($news, Response::HTTP_CREATED);
    }

    public function show(News $news): JsonResponse
    {
        return response()->json($news);
    }

    public function update(Request $request, News $news): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $news->update($validated);

        return response()->json($news);
    }

    public function destroy(News $news): JsonResponse
    {
        $news->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
