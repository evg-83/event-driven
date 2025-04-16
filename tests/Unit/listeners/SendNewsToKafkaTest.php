<?php namespace Tests\Unit\listeners;

use App\Events\NewsPublishedEvent;
use App\Listeners\SendNewsToKafka;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Junges\Kafka\Facades\Kafka;
use Tests\TestCase;

class SendNewsToKafkaTest extends TestCase
{
    use RefreshDatabase;

    public function test_kafka_is_called_with_correct_payload(): void
    {
        Kafka::fake();

        $news = News::factory()->make();

        $event = new NewsPublishedEvent($news);
        $listener = new SendNewsToKafka();

        $listener->handle($event);

        Kafka::assertPublishedOn('news-topic', null, function ($message) use ($news) {
            $body = $message->getBody();

            return $body['id'] === $news->id
                && $body['title'] === $news->title
                && $body['content'] === $news->content;
        });
    }
}
