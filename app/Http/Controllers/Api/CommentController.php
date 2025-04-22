<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Tests\Feature\api\CommentTest;

/**
 * @see CommentTest
 */
class CommentController extends Controller
{
    public function __construct(private readonly CommentService $service) {}

    public function index(): AnonymousResourceCollection
    {
        $comments = Comment::with(['user'])->withCount('likes')->latest()->paginate(10);

        return CommentResource::collection($comments);
    }

    public function store(CommentRequest $request): JsonResponse
    {
        $comment = $this->service->create($request->user(), $request->validated());

        $comment->load(['user'])->loadCount('likes');

        return response()->json(new CommentResource($comment), Response::HTTP_CREATED);
    }

    public function show(Comment $comment): JsonResponse
    {
        $comment->load(['user'])->loadCount('likes');

        return response()->json(new CommentResource($comment));
    }

    public function update(CommentRequest $request, Comment $comment): JsonResponse
    {
        $this->authorize('update', $comment);

        $validated = $request->only(['text']);

        $updated = $this->service->update($comment, $validated);
        $updated->load(['user'])->loadCount('likes');

        return response()->json(new CommentResource($comment));
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $this->service->delete($comment);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
