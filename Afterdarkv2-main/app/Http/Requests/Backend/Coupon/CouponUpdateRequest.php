<?php

namespace App\Http\Requests\Backend\Coupon;

use App\Constants\CouponConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponUpdateRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'alpha',
                'unique:coupons,code,' . $this->coupon->id,
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'type' => [
                'string',
                Rule::in(CouponConstants::getCodesList()),
            ],
            'amount' => [
                'required',
                'int',
            ],
            'minimum_spend' => [
                'required',
                'int',
                'nullable',
            ],
            'maximum_spend' => [
                'required',
                'int',
                'nullable',
            ],
            'meta_title' => [
                'nullable',
                'int',
            ],
            'expired_at' => [
                'required',
                'date',
            ],
            'usage_limit' => [
                'required',
                'int',
                'nullable',
            ],
            'approved' => [
                'required',
                'boolean',
            ],
        ];
    }
}
