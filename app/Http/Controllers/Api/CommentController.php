<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Comment::latest()->paginate(10));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'news_id' => 'required|exists:news,id',
        ]);

        $comment = auth()->user()->comments()->create($validated);

        return response()->json($comment, Response::HTTP_CREATED);
    }

    public function show(Comment $comment): JsonResponse
    {
        return response()->json($comment);
    }

    public function update(Request $request, Comment $comment): JsonResponse
    {
        $validated = $request->validate([
            'text' => 'required|string',
        ]);

        $comment->update($validated);

        return response()->json($comment);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
