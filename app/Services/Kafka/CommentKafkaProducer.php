<?php namespace App\Services\Kafka;

use App\Contracts\CommentKafkaProducerInterface;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Exception;
use Junges\Kafka\Facades\Kafka;

class CommentKafkaProducer implements CommentKafkaProducerInterface
{
    /**
     * @throws Exception
     */
    public function publish(Comment $comment): void
    {
        Kafka::publish()
            ->onTopic('comment-topic')
            ->withBody((new CommentResource($comment))->resolve())
            ->send();
    }
}
