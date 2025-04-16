<?php namespace App\Services;

use App\Events\NewsPublishedEvent;
use App\Models\News;
use App\Models\User;

/**
* @see NewsServiceTest
*/
class NewsService
{
    /**
     * @desc Создание новости пользователем и публикация события
     */
    public function create(User $user, array $data): News
    {
        $news = $user->news()->create($data);

        // 🔥 Dispatch event
        event(new NewsPublishedEvent($news));
        return $news;
    }

    /**
     * @desc Обновление новости
     */
    public function update(News $news, array $data): News
    {
        $news->update($data);
        return $news;
    }

    /**
     * @desc Удаление новости
     */
    public function delete(News $news): void
    {
        $news->delete();
    }
}
