<?php namespace Tests\Feature\api;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected News|Collection|Model $news;
    private const SOME_COUNT_FACTORY = 5;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->news = News::factory()->for($this->user)->count(self::SOME_COUNT_FACTORY)->create();
    }

    public function test_user_can_create_news(): void
    {
        $user = $this->authenticate($this->user);

        $response = $this->postJson('/api/news/', [
            'title' => 'New Post',
            'content' => 'This is a test news post.',
            'user_id' => $user->id,
        ]);

        $response->assertCreated()
            ->assertJson([
                'title' => 'New Post',
                'user_id' => $user->id
            ]);
    }

    public function test_user_can_list_news(): void
    {
        $this->authenticate($this->user);

        $response = $this->getJson('/api/news');

        $response->assertOk()->assertJsonCount(self::SOME_COUNT_FACTORY, 'data');
    }

    public function test_user_can_delete_news(): void
    {
        $news = $this->news->first();
        $this->authenticate($this->user);

        $response = $this->deleteJson("/api/news/{$news->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('news', ['id' => $news->id]);
    }
}
