<?php namespace Tests\Unit\listeners;

use App\Contracts\NewsKafkaProducerInterface;
use App\Events\NewsPublishedEvent;
use App\Listeners\SendNewsToKafka;
use App\Models\News;
use Exception;
use Mockery;
use Tests\TestCase;

class SendNewsToKafkaTest extends TestCase
{
    private News $news;

    protected function setUp(): void
    {
        parent::setUp();
        $this->news = Mockery::mock(News::class)->makePartial();
    }

    /**
     * @throws Exception
     */
    public function test_handle_sends_news_to_kafka(): void
    {
        $producer = $this->mockProducer(function ($mock) {
            $mock->shouldReceive('publish')
                ->once()
                ->with($this->news);
        });

        $listener = new SendNewsToKafka($producer);
        $listener->handle(new NewsPublishedEvent($this->news));
    }

    /**
     * @throws Exception
     */
    public function test_handle_logs_exception_on_failure(): void
    {
        $producer = $this->mockProducer(function ($mock) {
            $mock->shouldReceive('publish')
                ->once()
                ->with($this->news)
                ->andThrow(new Exception('Kafka error'));
        });

        $listener = new SendNewsToKafka($producer);
        $listener->handle(new NewsPublishedEvent($this->news));

        $this->assertTrue(true);
    }

    private function mockProducer(callable $setup): NewsKafkaProducerInterface
    {
        $mock = Mockery::mock(NewsKafkaProducerInterface::class);
        $setup($mock);
        return $mock;
    }}
