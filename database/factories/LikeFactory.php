<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Like;
use App\Models\News;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Like>
 */
class LikeFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Like::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $likeableTypes = [
            News::class,
            Comment::class,
        ];

        $likeableType = $this->faker->randomElement($likeableTypes);
        $likeableId = $likeableType::inRandomOrder()->value('id') ?? $likeableType::factory();

        return [
            'user_id' => User::factory(),
            'target_id' => $likeableId,
            'target_type' => $likeableType,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
