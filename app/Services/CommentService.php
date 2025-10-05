<?php namespace App\Services;

use App\Events\CommentAddedEvent;
use App\Models\Comment;
use App\Models\User;

/**
* @see CommentServiceTest
*/
class CommentService
{
    /**
     * @desc Создание комментария пользователем и публикация события
     */
    public function create(User $user, array $data): Comment
    {
        $comment = $user->comments()->create($data);

        // 🔥 Dispatch event
        event(new CommentAddedEvent($comment));
        return $comment;
    }

    /**
     * @desc Обновление комментария
     */
    public function update(Comment $comment, array $data): Comment
    {
        $comment->update($data);
        return $comment;
    }

    /**
     * @desc Удаление комментария
     */
    public function delete(Comment $comment): void
    {
        $comment->delete();
    }
}
