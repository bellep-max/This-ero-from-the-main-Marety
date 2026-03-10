@extends('index')
@section('content')
    <div class="bg-gradient-default py-3 p-md-5 p-lg-6 min-vh-100">
        <div class="container">
            <div class="row">
                <div class="col-12 col-xl-3 pb-3 pb-xl-0">
                    @include('frontend.default.settings.layout.menu')
                </div>
                <div class="col col-xl-9">
                    <div class="d-flex flex-column w-100 gap-4 bg-light rounded-5 p-3 p-lg-5">
                        <div class="font-default fs-4">
                            @yield('title')
                        </div>
                        <div class="container-fluid">
                            @yield('main-section')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection