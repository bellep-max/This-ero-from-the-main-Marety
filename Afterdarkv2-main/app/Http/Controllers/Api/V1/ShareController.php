<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\LoveableObjectEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShareController extends ApiController
{
    public function embed(Request $request): JsonResponse
    {
        $modelClass = LoveableObjectEnum::fromName($request->route('type'));

        $modelId = $modelClass::query()
            ->where('uuid', $request->route('uuid'))
            ->value('id');

        if (!$modelId) {
            return $this->notFound('Resource not found');
        }

        return $this->success([
            'id' => $modelId,
            'theme' => $request->route('theme'),
            'type' => $request->route('type'),
        ]);
    }
}
