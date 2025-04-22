<?php namespace Tests\Unit\events;

use App\Events\CommentAddedEvent;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Date;
use Mockery;
use Tests\TestCase;

class CommentAddedEventTest extends TestCase
{
    private Comment $comment;
    private CommentAddedEvent $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->comment = Mockery::mock(Comment::class)->makePartial();
        $this->event = new CommentAddedEvent($this->comment);
    }

    public function test_broadcast_on_returns_expected_channel(): void
    {
        $channels = $this->event->broadcastOn();

        $this->assertIsArray($channels);
        $this->assertInstanceOf(Channel::class, $channels[0]);
        $this->assertEquals('comment.added', $channels[0]->name);
    }

    public function test_broadcast_as_returns_expected_event_name(): void
    {
        $this->assertEquals('comment.added', $this->event->broadcastAs());
    }

    public function test_broadcast_with_returns_expected_payload(): void
    {
        Date::setTestNow(now());

        $resourceArray = (new CommentResource($this->comment))->resolve();

        $this->assertEquals($resourceArray, $this->event->broadcastWith());
    }
}
