<?php namespace Database\Seeders;

use App\Models\Comment;
use App\Models\News;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        News::all()->each(function (News $news) {
            Comment::factory(3)->create([
                'news_id' => $news->id,
                'user_id' => User::inRandomOrder()->first()->id,
            ]);
        });
    }
}
