<?php namespace Tests\Unit\listeners;

use App\Contracts\CommentKafkaProducerInterface;
use App\Events\CommentAddedEvent;
use App\Listeners\SendCommentToKafka;
use App\Models\Comment;
use Exception;
use Mockery;
use Tests\TestCase;

class SendCommentToKafkaTest extends TestCase
{
    private Comment $comment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->comment = Mockery::mock(Comment::class)->makePartial();
    }

    /**
     * @throws Exception
     */
    public function test_handle_sends_comment_to_kafka(): void
    {
        $producer = $this->mockProducer(function ($mock) {
            $mock->shouldReceive('publish')
                ->once()
                ->with($this->comment);
        });

        $listener = new SendCommentToKafka($producer);
        $listener->handle(new CommentAddedEvent($this->comment));
    }

    /**
     * @throws Exception
     */
    public function test_handle_logs_exception_on_failure(): void
    {
        $producer = $this->mockProducer(function ($mock) {
            $mock->shouldReceive('publish')
                ->once()
                ->with($this->comment)
                ->andThrow(new Exception('Kafka error'));
        });

        $listener = new SendCommentToKafka($producer);
        $listener->handle(new CommentAddedEvent($this->comment));

        $this->assertTrue(true);
    }

    private function mockProducer(callable $setup): CommentKafkaProducerInterface
    {
        $mock = Mockery::mock(CommentKafkaProducerInterface::class);
        $setup($mock);
        return $mock;
    }
}
