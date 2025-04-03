<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class News extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'user_id',
    ];

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'target');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
