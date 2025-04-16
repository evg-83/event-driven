<?php namespace App\Events;

use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @see NewsPublishedEventTest
 */
class NewsPublishedEvent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public News $news)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('news.published')
        ];
    }

    public function broadcastWith(): array
    {
        return (new NewsResource($this->news))->resolve();
    }

    public function broadcastAs(): string
    {
        return 'news.published';
    }
}
