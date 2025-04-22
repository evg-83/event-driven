<?php namespace Tests\Feature\api;

use App\Events\CommentAddedEvent;
use App\Models\Comment;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected News $news;
    protected Comment $comment;

    private const SOME_TEXT = 'Test comment';

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->news = News::factory()->create();
        $this->comment = Comment::factory()->for($this->user)->for($this->news)->create([
            'text' => self::SOME_TEXT,
        ]);
    }

    public function test_user_can_create_comment(): void
    {
        $user = $this->authenticate($this->user);

        $response = $this->postJson('/api/comments', [
            'text' => self::SOME_TEXT,
            'news_id' => $this->news->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('comments', [
            'text' => self::SOME_TEXT,
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
                'data' => [[
                    'id', 'text', 'user' => ['id', 'name'], 'news_id', 'likes_count', 'created_at', 'updated_at'
                ]],
            ]);
    }

    public function test_user_can_get_single_comment(): void
    {
        $user = $this->authenticate($this->user);

        $response = $this->getJson("/api/comments/{$this->comment->id}");

        $response->assertOk()
            ->assertJson([
                'id' => $this->comment->id,
                'text' => self::SOME_TEXT,
                'news_id' => $this->news->id,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
                'likes_count' => 0,
            ]);
    }

    public function test_user_can_update_comment(): void
    {
        $this->authenticate($this->user);

        $response = $this->putJson("/api/comments/{$this->comment->id}", [
                'text' => self::SOME_TEXT,
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('comments', [
            'id' => $this->comment->id,
            'text' => self::SOME_TEXT,
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

    public function test_comment_added_event_is_dispatched_on_store(): void
    {
        Event::fake([CommentAddedEvent::class]);

        $this->authenticate($this->user);

        $this->postJson('/api/comments', [
            'text' => self::SOME_TEXT,
            'news_id' => $this->news->id,
        ])->assertCreated();

        Event::assertDispatched(CommentAddedEvent::class, function ($event) {
            return $event->comment->text === self::SOME_TEXT;
        });
    }
}
