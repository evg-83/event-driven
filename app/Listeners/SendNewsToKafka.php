<?php namespace App\Listeners;

use App\Events\NewsPublishedEvent;
use App\Http\Resources\NewsResource;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Junges\Kafka\Facades\Kafka;

/**
 * @see SendNewsToKafkaTest
 */
class SendNewsToKafka implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * @throws Exception
     */
    public function handle(NewsPublishedEvent $event): void
    {
        Kafka::publish()
            ->onTopic('news-topic')
            ->withBody((new NewsResource($event->news))->resolve())
            ->send();
    }
}
