<?php namespace App\Services\Kafka;

use App\Contracts\NewsKafkaProducerInterface;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Exception;
use Junges\Kafka\Facades\Kafka;

class NewsKafkaProducer implements NewsKafkaProducerInterface
{
    /**
     * @throws Exception
     */
    public function publish(News $news): void
    {
        Kafka::publish()
            ->onTopic('news-topic')
            ->withBody((new NewsResource($news))->resolve())
            ->send();
    }
}
