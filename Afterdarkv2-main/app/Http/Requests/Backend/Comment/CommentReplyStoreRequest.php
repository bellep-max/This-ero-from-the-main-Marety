<?php

namespace App\Http\Requests\Backend\Comment;

use App\Services\CommentService;
use Illuminate\Foundation\Http\FormRequest;

class CommentReplyStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $service = new CommentService;

        $this->merge([
            'comment' => $service->sanitizeContent($this->input('comment')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'parent_id' => [
                'required',
                'exists:comments,id',
            ],
            'comment' => [
                'required',
                'string',
                'min:' . config('settings.comment_min_chars', 1),
                'max:' . config('settings.comment_max_chars'),
            ],
            'content' => [
                'required',
                'string',
            ],
        ];
    }
}
