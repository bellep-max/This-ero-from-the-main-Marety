<?php

namespace App\Http\Controllers\Backend;

use App\Models\MESubscription;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class SubscriptionController
{
    public function index(Request $request): View|Application|Factory
    {
        $subscriptions = MESubscription::query()
            ->withoutGlobalScopes()
            ->when($request->has('term'), function ($query) use ($request) {
                $query->whereHas('user', function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->input('term') . '%');
                });
            })
            ->with('user', 'plan')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('backend.subscriptions.index')
            ->with([
                'total' => MESubscription::query()->count(),
                'subscriptions' => $subscriptions,
                'term' => $request->input('term'),
            ]);
    }

    public function edit(MESubscription $subscription): View|Application|Factory
    {
        return view('backend.subscriptions.edit')
            ->with([
                'subscription' => $subscription->load('user', 'plan'),
            ]);
    }
}
