@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active"><a href="{{ route('backend.orders.index') }}">Orders</a></li>
        <li class="breadcrumb-item active">{{ $subscription->user->name }}</li>
    </ol>

    <div class="row">
        <div class="col-lg-12">
            <form role="form" action="" enctype="multipart/form-data" method="POST">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-3">Payment Method</label>
                    <div class="col-sm-9">
                        {{ ucwords($subscription->payment) }}
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3">Plan</label>
                    <div class="col-sm-9">
                        {{ ucwords($subscription->plan->name) }}
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Transaction ID</label>
                    <div class="col-sm-9">
                        <input class="form-control" name="transaction_id" value="{{ old('transaction_id', $subscription->transaction_id) }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Amount</label>
                    <div class="col-sm-9">
                        <div class="input-group col-xs-12">
                            <span class="input-group-addon"><i class="fas fa-fw fa-money-check"></i></span>
                            <input type="text" class="form-control input-lg money" name="amount" value="{{ old('amount', $subscription->amount) }}" placeholder="0.00" required>
                            <span class="input-group-btn"><button class="browse btn btn-primary input-lg" type="button"><i class="fas fa-fw fa-hand-holding-usd"></i> USD</button></span>
                        </div>
                        <small id="emailHelp" class="form-text text-muted">In USD, numeric only</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Status</label>
                    <div class="col-sm-9">
                        {!! makeDropdown( \App\Constants\StatusConstants::getDefaultConstants(), "payment_status", old('payment_status', $subscription->payment_status) ) !!}
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputUser" class="col-sm-3 control-label">Customer</label>
                    <div class="col-sm-9">
                        <select class="form-control select-ajax" data-ajax--url="{{ route('api.search.user') }}" name="user_id">
                            <option value="{{ $subscription->user->id }}" selected="selected" data-artwork="{{ $subscription->user->artwork }}">{{ $subscription->user->name }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Move User to Group</label>
                    <div class="col-sm-9">
                        {!! makeRolesDropDown('role', $subscription->user->group->role_id, 'required') !!}
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">End at</label>
                    <div class="col-sm-9">
                        <input class="form-control" type="datetime-local" name="end_at" value="{{ \Carbon\Carbon::now()->format('Y/m/d H:i') }}" autocomplete="off">
                        <small class="form-text text-muted">Send user back to default group (the one new users will be placed after the registration) after this time.</small>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-info">Reset</button>
            </form>
        </div>
    </div>
@endsection