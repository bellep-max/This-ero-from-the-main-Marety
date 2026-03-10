@php use App\Services\Backend\BackendService; @endphp
@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.coupons.index') }}">Coupons</a></li>
        <li class="breadcrumb-item active">Add new coupon</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <form method="POST" action="{{ route('backend.coupons.store') }}" class="form-horizontal">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Code:</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="code" value="{{ old('code') }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Description:</label>
                    <div class="col-sm-9">
                        <textarea name="description" class="form-control" rows="5">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Discount Type</label>
                    <div class="col-sm-9">
                        {!! BackendService::makeDropdown(
                            \App\Constants\CouponConstants::getFullList(),
                            "type",
                            null,
                            true
                        ) !!}
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Coupon amount:</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="number" name="amount" value="{{ old('amount') }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Minimum spend (integer, in {{ config('settings.currency', 'USD') }}):</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="number" name="minimum_spend" value="{{ old('minimum_spend') }}" autocomplete="off">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Maximum spend (integer, in {{ config('settings.currency', 'USD') }}):</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="number" step="1" name="maximum_spend" value="{{ old('maximum_spend') }}" autocomplete="off">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Expiry date:</label>
                    <div class="col-sm-9">
                        <input class="form-control datetimepicker-with-form" type="datetime-local" name="expired_at" value="{{ old('expired_at') }}" autocomplete="off">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Usage limit:</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="number" step="1" name="usage_limit" value="{{ old('usage_limit') }}" autocomplete="off">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Active this coupon</label>
                    <div class="col-sm-9">
                        <label class="switch">
                            {!! BackendService::makeCheckbox('approved', old('approved') ) !!}
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-info">Reset</button>
            </form>
        </div>
    </div>
@endsection