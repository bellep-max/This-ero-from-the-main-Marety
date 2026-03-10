<?php

namespace App\Http\Controllers\Frontend;

use App\Constants\ActionConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Reaction\ReactionRevokeRequest;
use App\Http\Requests\Frontend\Reaction\ReactionUpdateRequest;
use App\Models\Notification;
use App\Models\Reaction;
use Illuminate\Http\JsonResponse;

class ReactionController extends Controller
{
    public function react(ReactionUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $reaction = Reaction::updateOrCreate([
            'user_id' => auth()->id(),
            'reactionable_id' => $data['reaction_able_id'],
            'reactionable_type' => $data['reaction_able_type'],
        ], [
            'type' => $data['reaction_type'],
        ]);

        $reactionAble = $reaction->reactionable;
        $reactionAble->increment('reaction_count');

        if ($reactionAble->user_id != auth()->id()) {
            $notification = Notification::query()
                ->where('object_id', $data['reaction_able_id'])
                ->where('user_id', $reactionAble->user_id)
                ->where('notificationable_id', $reaction->id)
                ->where('notificationable_type', $reaction->getMorphClass())
                ->first();
            if (!isset($notification->id)) {
                pushNotification(
                    $reactionAble->user_id,
                    $reaction->id,
                    $reaction->getMorphClass(),
                    ActionConstants::COMMENT_REACT,
                    $data['reaction_able_id']
                );
            }
        }

        return response()->json(['success' => true]);
    }

    public function revoke(ReactionRevokeRequest $request): JsonResponse
    {
        $data = $request->validated();

        $reaction = Reaction::query()
            ->where('user_id', auth()->id())
            ->where('reactionable_id', $data['reaction_able_id'])
            ->where('reactionable_type', $data['reaction_able_type'])
            ->firstOrFail();

        $reaction->reactionable->decrement('reaction_count');
        $reaction->delete();

        return response()->json(['success' => true]);
    }
}
