<?php

namespace App\Http\Requests\Backend\Post;

use App\Constants\DefaultConstants;
use App\Constants\LogActionConstants;
use App\Constants\PermissionConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if (!$this->input('poll_title')) {
            $this->offsetUnset('poll_answers');
            $this->offsetUnset('poll_multiple');
            $this->offsetUnset('poll_ended_at');
            $this->offsetUnset('poll_visibility');
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
            ],
            'published_at' => [
                'required',
                'date',
            ],
            'category' => [
                'sometimes',
                'required',
                'array',
            ],
            'category.*' => [
                'sometimes',
                'required',
                'exists:categories,id',
            ],
            'short_content' => [
                'required',
                'string',
            ],
            'full_content' => [
                'required',
                'string',
            ],
            'alt_name' => [
                'sometimes',
                'required',
                'string',
                'unique:posts,alt_name,' . $this->postVisible->id,
            ],
            'tags' => [
                'sometimes',
                'required',
                'array',
            ],
            'tags.*' => [
                'sometimes',
                'required',
                'string',
                //                'alpha_num',
            ],
            'log_expires' => [
                'sometimes',
                'required',
                'date',
            ],
            'log_action' => [
                'required_with:log_expires',
                Rule::in(LogActionConstants::getList(false)),
            ],
            'meta_title' => [
                'nullable',
                'string',
                'max:200',
            ],
            'meta_description' => [
                'nullable',
                'string',
                'max:300',
            ],
            'meta_keywords' => [
                'array',
            ],
            'meta_keywords.*' => [
                'string',
            ],
            'poll_title' => [
                'nullable',
                'string',
            ],
            'poll_answers' => [
                'sometimes',
                Rule::requiredIf($this->input('poll_title')),
                'string',
            ],
            'poll_multiple' => [
                'sometimes',
                Rule::requiredIf($this->input('poll_title')),
                Rule::in(DefaultConstants::getCodesList()),
            ],
            'poll_ended_at' => [
                'sometimes',
                Rule::requiredIf($this->input('poll_title')),
                'date',
            ],
            'poll_visibility' => [
                'sometimes',
                Rule::requiredIf($this->input('poll_title')),
                Rule::in(DefaultConstants::getCodesList()),
            ],
            'group_extra' => [
                'required',
                'array',
            ],
            'group_extra.*' => [
                'required',
                Rule::in(PermissionConstants::getList(false)),
            ],
            'is_visible' => [
                'sometimes',
                'required',
                Rule::in(DefaultConstants::getCodesList()),
            ],
            'approved' => [
                'sometimes',
                'required',
                Rule::in(DefaultConstants::getCodesList()),
            ],
            'allow_main' => [
                'sometimes',
                'required',
                Rule::in(DefaultConstants::getCodesList()),
            ],
            'fixed' => [
                'sometimes',
                'required',
                Rule::in(DefaultConstants::getCodesList()),
            ],
            'allow_comments' => [
                'sometimes',
                'required',
                Rule::in(DefaultConstants::getCodesList()),
            ],
            'disable_index' => [
                'sometimes',
                'required',
                Rule::in(DefaultConstants::getCodesList()),
            ],
            'artwork' => [
                'sometimes',
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:' . config('settings.image_max_file_size'),
            ],
        ];
    }

    protected function passedValidation()
    {
        $this->merge([
            'alt_name' => $this->input('alt_name')
                ? str_slug($this->input('alt_name'))
                : str_slug($this->input('title')),
            'category' => $this->input('category')
                ? implode(',', $this->input('category'))
                : null,
            'tags_uncompressed' => $this->input('tags'),
            'tags' => $this->input('tags')
                ? implode(',', $this->input('tags'))
                : null,
            'meta_keywords' => $this->input('meta_keywords')
                ? implode(',', $this->input('meta_keywords'))
                : null,
        ]);

        if ($this->input('group_extra')) {
            $groupRegel = [];

            foreach ($this->input('group_extra') as $key => $value) {
                if ($value) {
                    $groupRegel[] = intval($key) . ':' . intval($value);
                }
            }

            $this->merge([
                'access' => count($groupRegel)
                    ? implode('||', $groupRegel)
                    : null,
            ]);
        }
    }
}
