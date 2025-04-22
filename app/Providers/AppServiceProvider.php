<?php namespace App\Providers;

use App\Contracts\CommentKafkaProducerInterface;
use App\Contracts\NewsKafkaProducerInterface;
use App\Services\Kafka\CommentKafkaProducer;
use App\Services\Kafka\NewsKafkaProducer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            CommentKafkaProducerInterface::class, CommentKafkaProducer::class,
        );
        $this->app->bind(
            NewsKafkaProducerInterface::class, NewsKafkaProducer::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
    }
}
