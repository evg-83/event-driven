<?php  namespace App\Contracts;

use App\Models\News;

interface NewsKafkaProducerInterface
{
    public function publish(News $news): void;
}
