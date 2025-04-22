<?php  namespace App\Contracts;

use App\Models\Comment;

interface CommentKafkaProducerInterface
{
    public function publish(Comment $comment): void;
}
