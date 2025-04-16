<?php namespace Tests\Feature\api\events;

use App\Events\NewsPublishedEvent;
use App\Listeners\SendNewsToKafka;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NewsPublishedEventTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected News $news;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->news = News::factory()->for($this->user)->create();
    }

    public function test_news_published_event_is_dispatched_and_listener_queued(): void
    {
        Event::fake();

        event(new NewsPublishedEvent($this->news));

        Event::assertDispatched(NewsPublishedEvent::class, fn ($event) =>
            $event->news->is($this->news)
        );
    }
}
