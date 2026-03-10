@extends('index')
@section('content')
    <div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
        <div class="container">
            <div class="row">
                <div class="col-12 col-xl-3 pb-3 pb-xl-0">
                    @include('frontend.default.profile.layout.menu')
                </div>
                <div class="col col-xl-9">
                    <div class="d-flex flex-column w-100 gap-4 bg-light rounded-5 p-3 p-lg-5 vh-100">
                        <div class="font-default fs-4">Notifications</div>
                        <div id="user-page-notifications" class="d-flex flex-column w-100 overflow-y-auto p-2 infinity-load-more" data-type="notifications" data-element="notification" data-total-page="5">
                            @if(count($profile->notifications))
                                @foreach($profile->notifications as $index => $notification)
                                    @include('frontend.components.notification', ['notification' => $notification, 'index' => $index, 'type' => 'full'])
                                @endforeach
                            @else
                                <p class="no-notifications" data-translate-text="YOU_HAVE_NO_NOTIFS">{{ __('web.YOU_HAVE_NO_NOTIFS') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection