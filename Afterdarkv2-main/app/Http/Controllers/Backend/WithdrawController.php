<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-08-06
 * Time: 23:14.
 */

namespace App\Http\Controllers\Backend;

use App\Constants\DefaultConstants;
use App\Constants\WithdrawActionConstants;
use App\Http\Requests\Backend\Withdraw\WithdrawIndexRequest;
use App\Http\Requests\Backend\Withdraw\WithdrawUpdateRequest;
use App\Models\Withdraw;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;

class WithdrawController
{
    public function index(WithdrawIndexRequest $request): View|Application|Factory
    {
        $term = $request->has('term')
            ? $request->get('term')
            : '';

        $withdraws = Withdraw::query()
            ->withoutGlobalScopes()
            ->when($term, function ($query) use ($term) {
                $query->whereHas('user', function ($query) use ($term) {
                    $query->where('name', 'LIKE', "%$term%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('backend.withdraws.index')
            ->with([
                'term' => $term,
                'withdraws' => $withdraws,
            ]);
    }

    public function update(Withdraw $withdraw, WithdrawUpdateRequest $request): JsonResponse
    {
        $action = $request->input('action');

        if ($action === WithdrawActionConstants::UNPAID) {
            $withdraw->update([
                'paid' => DefaultConstants::FALSE,
            ]);
        } elseif ($action === WithdrawActionConstants::MARK_PAID) {
            $withdraw->update([
                'paid' => DefaultConstants::TRUE,
            ]);
        } elseif ($action === WithdrawActionConstants::DECLINE) {
            $withdraw->delete();
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
