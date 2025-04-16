<?php namespace Tests\Unit\services;

use App\Events\NewsPublishedEvent;
use App\Models\News;
use App\Models\User;
use App\Services\NewsService;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class NewsServiceTest extends TestCase
{
    private NewsService $service;

    private const SOME_TITLE = 'Title';
    private const SOME_CONTENT = 'Content';

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new NewsService();
    }

    /**
     * @desc Проверка что создаётся новость и выбрасывается нужное событие
     */
    public function test_it_creates_news_and_dispatches_event(): void
    {
        Event::fake();

        $user = Mockery::mock(User::class);
        $news = Mockery::mock(News::class);

        $data = ['title' => self::SOME_TITLE, 'content' => self::SOME_CONTENT];

        $user->shouldReceive('news->create')
            ->once()
            ->with($data)
            ->andReturn($news);

        Event::shouldReceive('dispatch')
            ->once()
            ->with(Mockery::on(fn($event) => $event instanceof NewsPublishedEvent && $event->news === $news));

        $created = $this->service->create($user, $data);

        $this->assertSame($news, $created);
    }

    /**
     * @desc Проверяет, что метод update() вызывается с нужными параметрами
     */
    public function test_it_updates_news(): void
    {
        $news = Mockery::mock(News::class)->makePartial();

        $news->shouldReceive('update')
            ->once()
            ->with([
                'title' => self::SOME_TITLE,
                'content' => self::SOME_CONTENT,
            ])
            ->andReturnTrue();

        $news->title = self::SOME_TITLE;
        $news->content = self::SOME_CONTENT;

        $result = $this->service->update($news, [
            'title' => self::SOME_TITLE,
            'content' => self::SOME_CONTENT,
        ]);

        $this->assertSame($news, $result);
    }

    /**
     * @desc Проверяет факт вызова метода delete()
     */
    public function test_it_deletes_news(): void
    {
        $news = Mockery::mock(News::class);

        $news->shouldReceive('delete')
            ->once()
            ->andReturnTrue();

        $this->service->delete($news);

        $this->assertTrue(true);
    }
}
