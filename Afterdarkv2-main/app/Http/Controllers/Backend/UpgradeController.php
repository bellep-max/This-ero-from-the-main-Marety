<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-25
 * Time: 09:01.
 */

namespace App\Http\Controllers\Backend;

use App\Constants\StatusConstants;
use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Upgrade\UpgradeProcessRequest;

class UpgradeController
{
    private const PERSONAL_TOKEN = 'vGdSqLV6lfIx8HkxbdBJMrA9rcOXjgV0';

    private const USER_AGENT = 'Purchase code verification';

    private const SUCCESS_CODE = '28641149';

    public function index()
    {
        return view('backend.upgrade.index');
    }

    public function process(UpgradeProcessRequest $request)
    {
        $code = trim($request->input('license'));

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://api.envato.com/v3/market/author/sale?code=$code",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . self::PERSONAL_TOKEN,
                'User-Agent: ' . self::USER_AGENT,
            ],
        ]);

        $response = @curl_exec($ch);

        if (curl_errno($ch) > 0) {
            return MessageHelper::redirectMessage('Error connecting to API: ' . curl_error($ch), '', StatusConstants::FAILED);
        }

        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($responseCode === 404) {
            return MessageHelper::redirectMessage('The purchase code was invalid', '', StatusConstants::FAILED);
        } elseif ($responseCode !== 200) {
            return MessageHelper::redirectMessage("Failed to validate code due to an error: HTTP $responseCode", '', StatusConstants::FAILED);
        }

        $body = @json_decode($response);

        if ($body === false && json_last_error() !== JSON_ERROR_NONE) {
            return MessageHelper::redirectMessage('Error parsing response', '', StatusConstants::FAILED);
        } elseif ($body->item->id !== self::SUCCESS_CODE) {
            return MessageHelper::redirectMessage('The purchase code was invalid', '', StatusConstants::FAILED);
        }

        return view('backend.upgrade.process');
    }
}
