<?php namespace App\Listeners;

use App\Contracts\NewsKafkaProducerInterface;
use App\Events\NewsPublishedEvent;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * @see SendNewsToKafkaTest
 */
class SendNewsToKafka implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(protected NewsKafkaProducerInterface $producer) {}

    /**
     * Handle the event.
     * @throws Exception
     */
    public function handle(NewsPublishedEvent $event): void
    {
        try {
            $this->producer->publish($event->news);

        } catch (Exception $e) {
            Log::error('Kafka publish failed', [
                'error' => $e->getMessage(),
                'news_id' => $event->news->id ?? null,
            ]);
        }
    }
}
