<?php namespace App\Listeners;

use App\Contracts\CommentKafkaProducerInterface;
use App\Events\CommentAddedEvent;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Tests\Unit\listeners\SendCommentToKafkaTest;

/**
 * @see SendCommentToKafkaTest
 */
class SendCommentToKafka implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(protected CommentKafkaProducerInterface $producer) {}

    /**
     * @throws Exception
     */
    public function handle(CommentAddedEvent $event): void
    {
        try {
            $this->producer->publish($event->comment);

        } catch (Exception $e) {
            Log::error('Kafka publish failed', [
                'error' => $e->getMessage(),
                'comment_id' => $event->comment->id ?? null,
            ]);
        }
    }
}
