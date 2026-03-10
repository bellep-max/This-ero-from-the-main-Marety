<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-08-06
 * Time: 23:14.
 */

namespace App\Http\Controllers\Backend;

use App\Http\Requests\Backend\Order\OrderIndexRequest;
use App\Models\Album;
use App\Models\Order;
use App\Models\Song;
use DB;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class OrderController
{
    public function index(OrderIndexRequest $request): View|Application|Factory
    {
        $orders = Order::query()
            ->withoutGlobalScopes()
            ->when($request->input('term'), function ($query) use ($request) {
                $query->whereHas('user', function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->input('term') . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        $stats = Order::query()
            ->select(DB::raw('sum(amount) AS revenue'), DB::raw('sum(commission) AS commission'))
            ->first();

        $stats->album = Order::query()
            ->select(DB::raw('count(*) AS count'), DB::raw('sum(amount) AS revenue'))
            ->where('orderable_type', Album::class)
            ->first();

        $stats->song = Order::query()
            ->select(DB::raw('count(*) AS count'), DB::raw('sum(amount) AS revenue'))
            ->where('orderable_type', Song::class)
            ->first();

        return view('backend.orders.index')
            ->with([
                'orders' => $orders,
                'stats' => $stats,
            ]);
    }
}
