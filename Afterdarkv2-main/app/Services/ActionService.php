<?php

namespace App\Services;

use App\Enums\ActivityTypeEnum;
use App\Http\Requests\Frontend\ActionStoreRequest;

class ActionService
{
    public function parseAction(ActionStoreRequest $request): void
    {
        $actionEnum = ActivityTypeEnum::resolve($request->input('action'));

        switch ($actionEnum) {
            case ActivityTypeEnum::playSong:
                $track = $request->input('type')::query()->whereUuid($request->input('uuid'))->first();

                if ($track) {
                    $track->activities()->create([
                        'user_id' => auth()->id(),
                        'action' => ActivityTypeEnum::playSong,
                    ]);
                }

                break;
        }
    }
}
