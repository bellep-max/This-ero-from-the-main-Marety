<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-25
 * Time: 21:02.
 */

namespace App\Http\Controllers\Backend;

use App\Helpers\MessageHelper;
use App\Http\Requests\Backend\Coupon\CouponStoreRequest;
use App\Http\Requests\Backend\Coupon\CouponUpdateRequest;
use App\Models\Coupon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class CouponController
{
    private const DEFAULT_ROUTE = 'backend.coupons.index';

    public function index(): View|Application|Factory
    {
        return view('backend.coupons.index')
            ->with([
                'coupons' => Coupon::query()->paginate(20),
            ]);
    }

    public function create(): View|Application|Factory
    {
        return view('backend.coupons.create');
    }

    public function store(CouponStoreRequest $request): RedirectResponse
    {
        Coupon::create($request->input());

        return MessageHelper::redirectMessage('Coupon successfully created!', self::DEFAULT_ROUTE);
    }

    public function edit(Coupon $coupon): View|Application|Factory
    {
        return view('backend.coupons.form')
            ->with([
                'coupon' => $coupon,
            ]);
    }

    public function update(Coupon $coupon, CouponUpdateRequest $request): RedirectResponse
    {
        $coupon->update($request->input());

        return MessageHelper::redirectMessage('Coupon successfully updated!', self::DEFAULT_ROUTE);
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return MessageHelper::redirectMessage('Coupon successfully deleted!', self::DEFAULT_ROUTE);
    }
}
