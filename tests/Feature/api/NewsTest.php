<?php namespace Tests\Feature\api;

use App\Events\NewsPublishedEvent;
use App\Models\News;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class NewsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected News|Collection|Model $news;

    private const SOME_COUNT_FACTORY = 5;
    private const SOME_TITLE = 'Title';
    private const SOME_CONTENT = 'Content';

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->news = News::factory()->for($this->user)->count(self::SOME_COUNT_FACTORY)->create();
    }

    /**
     * @desc Проверка создания новости
     */
    public function test_user_can_create_news(): void
    {
        $this->authenticate($this->user);

        $response = $this->postJson('/api/news/', [
            'title' => self::SOME_TITLE,
            'content' => self::SOME_CONTENT,
        ]);

        $response->assertCreated()
            ->assertJsonFragment([
                'title' => self::SOME_TITLE,
            ]);
    }

    /**
     * @desc Проверка получения списка
     */
    public function test_user_can_list_news(): void
    {
        $this->authenticate($this->user);

        $response = $this->getJson('/api/news');

        $response->assertOk()->assertJsonCount(self::SOME_COUNT_FACTORY, 'data');
    }

    /**
     * @desc Проверка изменения новости
     */
    public function test_user_can_update_news(): void
    {
        $news = $this->news->first();
        $this->authenticate($this->user);

        $response = $this->putJson("/api/news/{$news->id}", [
            'title' => self::SOME_TITLE,
            'content' => $news->content,
        ]);

        $response->assertOk()
            ->assertJsonFragment(['title' => self::SOME_TITLE]);
    }

    /**
     * @desc Проверка возможности просмотра одной новости
     */
    public function test_user_can_view_single_news(): void
    {
        $news = $this->news->first();
        $this->authenticate($this->user);

        $response = $this->getJson("/api/news/{$news->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $news->id]);
    }

    /**
     * @desc Удаление своей новости
     */
    public function test_user_can_delete_news(): void
    {
        $news = $this->news->first();
        $this->authenticate($this->user);

        $response = $this->deleteJson("/api/news/{$news->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('news', ['id' => $news->id]);
    }

    /**
     * @desc Защита от удаления чужой записи
     */
    public function test_user_cannot_delete_others_news(): void
    {
        $user = User::factory()->create();
        $news = News::factory()->create();

        $this->authenticate($user);
        $response = $this->deleteJson("/api/news/{$news->id}");

        $response->assertForbidden();
    }

    /**
     * @desc Проверка события
     */
    public function test_event_is_dispatched_on_news_create(): void
    {
        Event::fake();

        $this->authenticate($this->user);

        $this->postJson('/api/news/', [
            'title' => self::SOME_TITLE,
            'content' => self::SOME_CONTENT,
        ]);

        Event::assertDispatched(NewsPublishedEvent::class);
    }
}
