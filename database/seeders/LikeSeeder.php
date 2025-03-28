<?php namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Like;
use App\Models\News;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (News::count() === 0 || Comment::count() === 0) {
            return;
        }

        News::all()->each(function (News $news) {
            $users = User::inRandomOrder()->take(rand(1, 5))->get();

            foreach ($users as $user) {
                Like::factory()->create([
                    'user_id' => $user->id,
                    'target_id' => $news->id,
                    'target_type' => News::class,
                ]);
            }
        });

        Comment::all()->each(function (Comment $comment) {
            $users = User::inRandomOrder()->take(rand(1, 5))->get();

            foreach ($users as $user) {
                Like::factory()->create([
                    'user_id' => $user->id,
                    'target_id' => $comment->id,
                    'target_type' => Comment::class,
                ]);
            }
        });
    }
}
