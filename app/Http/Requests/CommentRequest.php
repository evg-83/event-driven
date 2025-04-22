<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->method()) {
            'POST' => [
                'text' => 'required|string',
                'news_id' => 'required|exists:news,id',
            ],
            'PUT', 'PATCH' => [
                'text' => 'required|string',
            ],
            default => [],
        };
    }
}
