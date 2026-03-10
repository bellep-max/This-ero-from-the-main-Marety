<?php

namespace App\Http\Requests\Backend\Settings;

use Illuminate\Foundation\Http\FormRequest;

class SettingsStoreRequest extends FormRequest
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
            'save_con' => [
                'required',
                'array',
            ],
            'admin_path' => [
                'required',
                'string',
                'alpha_dash',
            ],
            'mail_driver' => $this->getDefaultRuleset(),
            'mail_host' => $this->getDefaultRuleset(),
            'mail_port' => $this->getDefaultRuleset(),
            'mail_username' => $this->getDefaultRuleset(),
            'mail_password' => $this->getDefaultRuleset(),
            'mail_encryption' => $this->getDefaultRuleset(),
            'amazon_s3_key_id' => $this->getDefaultRuleset(),
            'amazon_s3_secret' => $this->getDefaultRuleset(),
            'amazon_s3_region' => $this->getDefaultRuleset(),
            'amazon_s3_url' => $this->getDefaultRuleset(),
            'facebook_app_id' => $this->getDefaultRuleset(),
            'facebook_app_secret' => $this->getDefaultRuleset(),
            'facebook_app_callback_url' => $this->getDefaultRuleset(),
            'twitter_app_id' => $this->getDefaultRuleset(),
            'twitter_app_secret' => $this->getDefaultRuleset(),
            'twitter_app_callback_url' => $this->getDefaultRuleset(),
            'google_client_id' => $this->getDefaultRuleset(),
            'google_client_secret' => $this->getDefaultRuleset(),
            'google_app_callback_url' => $this->getDefaultRuleset(),
            'locale' => [
                'nullable',
                'string',
                'alpha_dash',
            ],
        ];
    }

    private function getDefaultRuleset(): array
    {
        return [
            'nullable',
            'string',
            'regex:/^\S*$/u',
        ];
    }
}
