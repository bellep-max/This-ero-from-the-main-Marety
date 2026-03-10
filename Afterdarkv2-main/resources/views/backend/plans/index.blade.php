@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active">Plans</li>
    </ol>

    <form role="form" class="d-flex flex-column" action="{{ route('backend.plans.update') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-6">
                <p class="font-weight-bold text-center">Site Plan</p>
                <div class="form-group row">
                    <label for="site[name]" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="site[name]" id="site[name]" value="{{ old('site[name]', $sitePlan?->name) }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="site[description]" class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <textarea class="form-control editor" rows="3" id="site[description]" name="site[description]" required>{{ old('site[description]', $sitePlan?->description) }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="site[price]" class="col-sm-2 col-form-label">Price</label>
                    <div class="col-sm-10">
                        <div class="input-group col-xs-12">
                            <span class="input-group-addon"><i class="fas fa-fw fa-money-check"></i></span>
                            <input type="number" class="form-control input-lg" id="site[price]" name="site[price]" value="{{ old('site[price]', $sitePlan?->price) }}" placeholder="0.00" required>
                            <span class="input-group-btn"><button class="browse btn btn-primary input-lg" type="button"><i class="fas fa-fw fa-hand-holding-usd"></i> {{ __('currency.'. config('settings.currency', 'USD')) }}</button></span>
                        </div>
                        <small id="emailHelp" class="form-text text-muted">In {{ __('currency.'. config('settings.currency', 'USD')) }}, numeric only</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="site[percentage]" class="col-sm-2 col-form-label">Percentage</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control input-lg" id="site[percentage]" name="site[percentage]" value="{{ old('site[percentage]', $sitePlan?->percentage) }}" placeholder="%" min="0" max="100" required>
                        <small id="emailHelp" class="form-text text-muted">This percentage will be added to the price and recalculated</small>
                    </div>
                </div>
                @empty($sitePlan)
                    <div class="form-group row">
                        <label for="site[interval]" class="col-sm-2 col-form-label">Interval</label>
                        <div class="col-sm-10">
                            {!! \App\Helpers\Helper::makeDropdown(\App\Constants\DurationConstants::getNames(), old('site[interval]', $sitePlan?->interval) ) !!}
                        </div>
                    </div>
                @endempty
            </div>
            <div class="col-6">
                <p class="font-weight-bold text-center">User Plan</p>
                <div class="form-group row">
                    <label for="user[name]" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <input class="form-control" id="user[name]" name="user[name]" value="{{ old('user[name]', $userPlan?->name) }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="user[description]" class="col-sm-2 col-form-label">Description</label>
                    <div class="col-sm-10">
                        <textarea class="form-control editor" rows="3" id="user[description]" name="user[description]" required>{{ old('user[description]', $userPlan?->description) }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="user[price]" class="col-sm-2 col-form-label">Price</label>
                    <div class="col-sm-10">
                        <div class="input-group col-xs-12">
                            <span class="input-group-addon"><i class="fas fa-fw fa-money-check"></i></span>
                            <input type="number" class="form-control input-lg" id="user[price]" name="user[price]" value="{{ old('user[price]', $userPlan?->price) }}" placeholder="0.00" required>
                            <span class="input-group-btn"><button class="browse btn btn-primary input-lg" type="button"><i class="fas fa-fw fa-hand-holding-usd"></i> {{ __('currency.'. config('settings.currency', 'USD')) }}</button></span>
                        </div>
                        <small id="emailHelp" class="form-text text-muted">In {{ __('currency.'. config('settings.currency', 'USD')) }}, numeric only</small>
                    </div>
                </div>
                @empty ($userPlan)
                    <div class="form-group row">
                        <label for="user[interval]" class="col-sm-2 col-form-label">Interval</label>
                        <div class="col-sm-10">
                            {!! \App\Helpers\Helper::makeDropdown(\App\Constants\DurationConstants::getNames(), "user[interval]", old('user[interval]', $userPlan?->interval) ) !!}
                        </div>
                    </div>
                @endempty
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-info">Reset</button>
            </div>
        </div>

    </form>
@endsection
