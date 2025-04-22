<?php namespace Tests\Unit\events;

use App\Events\NewsPublishedEvent;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Date;
use Mockery;
use Tests\TestCase;

class NewsPublishedEventTest extends TestCase
{
    private News $news;
    private NewsPublishedEvent $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->news = Mockery::mock(News::class)->makePartial();
        $this->event = new NewsPublishedEvent($this->news);
    }

    public function test_broadcast_on_returns_expected_channel(): void
    {
        $channels = $this->event->broadcastOn();

        $this->assertIsArray($channels);
        $this->assertInstanceOf(Channel::class, $channels[0]);
        $this->assertEquals('news.published', $channels[0]->name);
    }

    public function test_broadcast_as_returns_expected_event_name(): void
    {
        $this->assertEquals('news.published', $this->event->broadcastAs());
    }

    public function test_broadcast_with_returns_expected_payload(): void
    {
        Date::setTestNow(now());

        $resourceArray = (new NewsResource($this->news))->resolve();

        $this->assertEquals($resourceArray, $this->event->broadcastWith());
    }
}
