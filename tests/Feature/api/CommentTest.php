<?php namespace Tests\Feature\api;

use App\Models\Comment;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected News $news;
    protected Comment $comment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->news = News::factory()->create();
        $this->comment = Comment::factory()->for($this->user)->for($this->news)->create([
            'text' => 'Initial comment',
        ]);
    }

    public function test_user_can_create_comment(): void
    {
        $user = $this->authenticate($this->user);

        $response = $this->postJson('/api/comments', [
            'text' => 'This is a test comment',
            'news_id' => $this->news->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('comments', [
            'text' => 'This is a test comment',
            'user_id' => $user->id,
            'news_id' => $this->news->id,
        ]);
    }

    public function test_user_can_get_comments_list(): void
    {
        $this->authenticate($this->user);

        $response = $this->getJson('/api/comments');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'text', 'user_id', 'news_id', 'created_at', 'updated_at']],
            ]);
    }

    public function test_user_can_get_single_comment(): void
    {
        $user = $this->authenticate($this->user);

        $response = $this->getJson("/api/comments/{$this->comment->id}");

        $response->assertOk()
            ->assertJson([
                'id' => $this->comment->id,
                'text' => 'Initial comment',
                'user_id' => $user->id,
                'news_id' => $this->news->id,
            ]);
    }

    public function test_user_can_update_comment(): void
    {
        $this->authenticate($this->user);

        $response = $this->putJson("/api/comments/{$this->comment->id}", [
                'text' => 'This is a test comment',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('comments', [
            'id' => $this->comment->id,
            'text' => 'This is a test comment',
        ]);
    }

    public function test_user_can_delete_comment(): void
    {
        $this->authenticate($this->user);

        $response = $this->deleteJson("/api/comments/{$this->comment->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('comments', [
            'id' => $this->comment->id,
        ]);
    }
}
