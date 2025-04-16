<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Services\NewsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * @see NewsTest
 */
class NewsController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return NewsResource::collection(News::latest()->paginate(10));
    }

    public function store(NewsRequest $request, NewsService $service): JsonResponse
    {
        $news = $service->create(auth()->user(), $request->validated());

        return response()->json(new NewsResource($news), Response::HTTP_CREATED);
    }

    public function show(News $news): NewsResource
    {
        return new NewsResource($news);
    }

    public function update(NewsRequest $request, News $news, NewsService $service): JsonResponse
    {
        $this->authorize('update', $news);

        $news = $service->update($news, $request->validated());

        return response()->json(new NewsResource($news));
    }

    public function destroy(News $news, NewsService $service): JsonResponse
    {
        $this->authorize('delete', $news);

        $service->delete($news);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
