<?php namespace App\Policies;

use App\Models\News;
use App\Models\User;

class NewsPolicy
{
    public function update(User $user, News $news): bool
    {
        return $user->id === $news->user_id;
    }

    public function delete(User $user, News $news): bool
    {
        return $user->id === $news->user_id;
    }
}
