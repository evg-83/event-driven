<?php namespace Tests\Feature\api;

use App\Models\Comment;
use App\Models\Like;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected News $news;
    protected Comment $comment;
    protected Like $like;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->news = News::factory()->for($this->user)->create();
        $this->comment = Comment::factory()->for($this->user)->create();
        $this->like = Like::factory()->for($this->user)->create([
            'target_id' => $this->news->id,
            'target_type' => News::class,
        ]);
    }

    public function test_user_can_like_news():void
    {
        $newNews = News::factory()->for($this->user)->create();
        $user = $this->authenticate($this->user);

        $response = $this->postJson('/api/likes', [
            'target_id' => $newNews->id,
            'target_type' => News::class,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'target_id' => $newNews->id,
            'target_type' => News::class,
        ]);
    }

    public function test_user_cannot_like_same_news_twice():void
    {
        $this->authenticate($this->user);

        $response = $this->postJson('/api/likes', [
            'target_id' => $this->news->id,
            'target_type' => News::class,
        ]);

        $response->assertStatus(409)
            ->assertJson(['message' => 'Already liked']);
    }

    public function test_user_can_unlike_news():void
    {
        $this->authenticate($this->user);

        $response = $this->deleteJson('/api/likes/', [
            'target_id' => $this->news->id,
            'target_type' => News::class,
        ]);

        $response->assertNoContent();
        $this->assertDatabaseMissing('likes', ['id' => $this->like->id]);
    }

    public function test_user_cannot_unlike_nonexistent_like():void
    {
        $this->authenticate($this->user);

        $response = $this->deleteJson('/api/likes/', [
            'target_id' => $this->news->id,
            'target_type' => News::class,
        ]);

        $response->assertNoContent();
    }
}
