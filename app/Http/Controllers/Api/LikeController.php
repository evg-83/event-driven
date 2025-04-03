<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LikeController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'target_id' => 'required|integer',
            'target_type' => 'required|string|in:App\Models\News,App\Models\Comment',
        ]);

        $user = $request->user();

        $existingLike = $user->likes()
            ->where('target_id', $validated['target_id'])
            ->where('target_type', $validated['target_type'])
            ->exists();

        if ($existingLike) {
            return response()->json([
                'message' => 'Already liked',
            ], Response::HTTP_CONFLICT);
        }

        $like = $user->likes()->create([
            'target_id' => $validated['target_id'],
            'target_type' => $validated['target_type'],
        ]);

        return response()->json($like, Response::HTTP_CREATED);
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'target_id' => 'required|integer',
            'target_type' => 'required|string|in:App\Models\News,App\Models\Comment',
        ]);

        $user = $request->user();

        $like = $user->likes()
            ->where('target_id', $validated['target_id'])
            ->where('target_type', $validated['target_type'])
            ->firstOrFail();

        if (!$like) {
            return response()->json(['message' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $like->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
