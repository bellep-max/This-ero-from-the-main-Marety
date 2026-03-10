@extends('frontend.default.settings.layout.layout')

@section('title')
    Connected accounts
@endsection

@section('main-section')
    <div class="row gy-4">
        @if ($connections)
            <div class="col-12 d-flex flex-column justify-content-center align-items-center gap-4">
                <table class="table table-striped">
                    <thead class="thead-light">
                    <tr>
                        <th>Service</th>
                        <th>Username</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($connections as $connection)
                        <tr>
                            <td>{{ \Illuminate\Support\Str::ucfirst($connection->service) }}</td>
                            <td>{{ $connection->provider_name }}</td>
                            <td class="d-flex justify-content-between align-items-center px-3">
                                @if ($connection->service === \App\Constants\ProviderConstants::SPOTIFY)
                                    <label class="d-flex flex-row justify-content-start gap-3" for="autopost-to-{{ $connection->id }}">
                                        <span>Autopost</span>
                                        <input type="checkbox" id="autopost-to-{{ $connection->id }}" {{ $connection->autopost ? 'checked' : '' }} class="form-control autopost-select" data-id="{{ $connection->id }}">
                                    </label>
                                @endif
                                <button class="btn btn-danger delete-connection" data-id="{{ $connection->id }}">
                                    <svg class="p-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                        <path d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0L284.2 0c12.1 0 23.2 6.8 28.6 17.7L320 32l96 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 96C14.3 96 0 81.7 0 64S14.3 32 32 32l96 0 7.2-14.3zM32 128l384 0 0 320c0 35.3-28.7 64-64 64L96 512c-35.3 0-64-28.7-64-64l0-320zm96 64c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16l0 224c0 8.8 7.2 16 16 16s16-7.2 16-16l0-224c0-8.8-7.2-16-16-16z"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="d-flex flex-row justify-content-center align-items-center gap-4">
                    @if(auth()->user()->connects()->spotify()->exists())
                        <a class="btn-default btn-pink btn-wide"
                           @if(auth()->user()->activeSubscription())
                               href="/connect/redirect/spotify" target="_blank"
                           @else
                               href="{{ route('frontend.settings.subscription') }}"
                                @endif
                        >
                            Change Spotify Account
                        </a>
                    @else
                        <a class="btn-default btn-pink btn-wide"
                           @if(auth()->user()->activeSubscription())
                               href="/connect/redirect/spotify" target="_blank"
                           @else
                               href="{{ route('frontend.settings.subscription') }}"
                                @endif
                        >
                            Connect my Spotify Account
                        </a>
                    @endif
                </div>
            </div>
        @else
            <div class="col-12 d-flex flex-column justify-content-center align-items-center gap-4">
                <img src="/svg/services.svg" alt="">
                <div class="profile_page__content__subscription__desc">
                    Connect your accounts to share music with ease and enhance your experience.
                </div>
                <a class="btn-default btn-pink btn-wide" href="/connect/redirect/spotify" target="_blank">
                    Connect my Spotify Account
                </a>
            </div>
        @endif
    </div>
</div>
@endsection