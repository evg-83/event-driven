<?php namespace Tests\Unit\services;

use App\Events\CommentAddedEvent;
use App\Models\Comment;
use App\Models\User;
use App\Services\CommentService;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class CommentServiceTest extends TestCase
{
    private CommentService $service;

    private const SOME_TEXT = 'Test comment';
    private const SOME_NEWS_ID = 123;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CommentService();
    }

    /**
     * @desc Проверка что создаётся комментарий и выбрасывается нужное событие
     */
    public function test_create_dispatches_event_and_returns_comment(): void
    {
        Event::fake();

        $user = Mockery::mock(User::class);
        $comment = Mockery::mock(Comment::class);

        $data = ['text' => self::SOME_TEXT, 'news_id' => self::SOME_NEWS_ID];

        $user->shouldReceive('comments->create')
            ->once()
            ->with($data)
            ->andReturn($comment);

        $result = $this->service->create($user, $data);

        Event::assertDispatched(CommentAddedEvent::class, fn ($event) => $event->comment === $comment);

        $this->assertSame($comment, $result);
    }

    /**
     * @desc Проверка что метод update() вызывается с нужными параметрами
     */
    public function test_update_updates_and_returns_comment(): void
    {
        $comment = Mockery::mock(Comment::class);
        $data = ['text' => self::SOME_TEXT];

        $comment->shouldReceive('update')
            ->once()
            ->with($data)
            ->andReturn(true);

        $result = $this->service->update($comment, $data);

        $this->assertSame($comment, $result);
    }

    /**
     * @desc Проверка: факт вызова метода delete()
     */
    public function test_it_deletes_comment(): void
    {
        $comment = Mockery::mock(Comment::class);

        $comment->shouldReceive('delete')
            ->once()
            ->andReturnTrue();

        $this->service->delete($comment);

        $this->assertTrue(true);
    }
}
