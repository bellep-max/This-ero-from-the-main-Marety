@extends('backend.index')
@section('content')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('backend.dashboard') }}">Control Panel</a>
    </li>
    <li class="breadcrumb-item active">Subscriptions ({{ $total }})</li>
</ol>
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-info">A plan’s billing cycle defines the recurring frequency with which the subscriber is charged.</div>
        <form>
            <div class="form-group input-group">
                <input type="text" class="form-control" name="term" value="{{ $term }}" placeholder="Enter customer name">
                <span class="input-group-append">
			        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
			    </span>
                <span class="input-group-append">
			        <button class="btn btn-primary"><i class="fa fa-filter"></i></button>
			    </span>
            </div>
        </form>
        <table class="table table-striped datatables table-hover">
            <thead>
            <tr>
                <th>PayPal ID</th>
                <th>Subscriber</th>
                <th>Status</th>
                <th>Plan</th>
                <th class="desktop">Amount</th>
                <th class="desktop">Last Payment</th>
                <th class="desktop">Next billing</th>
            </tr>
            </thead>
            @foreach ($subscriptions as $index => $subscription)
                @if ($subscription->user)
                    <tr>
                        <td>{{ $subscription->subscription_id }}</td>
                        <td><a href="{{ route('backend.users.edit', $subscription->user) }}">{{ $subscription->user->name }}</a></td>
                        <td>
                            @php
                                $badgeClass = match ($subscription->status) {
                                    \App\Enums\SubscriptionStatusEnum::Active => 'badge-success',
                                    \App\Enums\SubscriptionStatusEnum::Cancelled => 'badge-danger',
                                    default => 'badge-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ strtoupper($subscription->status->name) }}
                            </span>
                        </td>
                        <td class="desktop"><a href="{{ route('backend.plans.index') }}">{{ $subscription->plan->name }}</a></td>
                        <td class="text-success">
                            {{ $subscription->amount ? __('symbol.' . $subscription->currency) . number_format($subscription->amount) : '–' }}
                        </td>
                        <td>{{ $subscription->last_payment_date ? $subscription->last_payment_date->format('F d, Y') : '–' }}</td>
                        <td>{{ $subscription->next_billing_date ? $subscription->next_billing_date->format('F d, Y') : '–' }}</td>
                    </tr>
                @endif
            @endforeach
        </table>
        <div class="pagination pagination-right">
            {{ $subscriptions->appends(request()->input())->links() }}
        </div>
    </div>
</div>
@endsection
