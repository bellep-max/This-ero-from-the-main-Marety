@extends('index')
@section('content')
    <div class="bg-gradient-default p-md-5 p-lg-6 min-vh-100">
        <div class="container">
            <div class="d-flex flex-column justify-content-start px-2 px-lg-0 gap-4">
                <div class="d-block">
                    <div class="block-title color-light text-truncate">
                        Adventure
                    </div>
                    <a href="{{ route('frontend.user.adventures', auth()->user()->username) }}" class="block-description color-light">
                        Go back
                    </a>
                </div>
            </div>
            <div class="diagram">
                @if (array_key_exists('children', $data))
                    <div class="diagram-row
                        top
                        @if(array_key_exists(4, $data['children']))
                            three-diagram-items
                        @elseif(array_key_exists(2, $data['children']))
                            two-diagram-items
                        @else
                            one-diagram-items
                        @endif
                    ">
                        @foreach ($data['children'] as $k => $child )
                            @if (in_array($k, [0, 2, 4]))
                                <div class="diagram-child-item">
                                    <div class="final-items-wrapper">
                                        <div class="final-items
                                            @if (count($child['finals']) == 3)
                                                three-finals has-middle-stick
                                            @elseif(count($child['finals']) == 2)
                                                two-finals
                                            @else
                                                has-middle-stick
                                            @endif
                                        ">
                                            @foreach ($child['finals'] as $final )
                                                <div class="final">
                                                    <div class="diagram-element">
                                                        <div class="diagram-element-background"
                                                             style="background-image: url('{{ $final['final_artwork_url'] }}')"></div>
                                                        <div class="diagram-content">
                                                            @if($final['title'])
                                                                <div class="adventure-title">{{ $data['adventure_title'] }}</div>
                                                                <div class="audio-title">{{ $final['title'] }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="diagram-element">
                                        <div class="diagram-element-background"
                                             style="background-image: url('{{ $child['child_artwork_url'] }}')"></div>
                                        <div class="diagram-content">
                                            @if($child['title'])
                                                <div class="adventure-title">{{ $child['title'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="diagram-row">
                        <div id="root" class="diagram-element {{ count($data['children']) > 1 ? "two-side" : "one-side" }}">
                            <div class="diagram-element-background"
                                 style="background-image: url('{{ $data['parent_artwork_url'] }}')"></div>
                            <div class="diagram-content">
                                @if($data['adventure_title'])
                                    <div class="adventure-title">{{ $data['adventure_title'] }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if (array_key_exists(1, $data['children']))
                        <div class="diagram-row bottom {{ array_key_exists(3, $data['children']) ? 'two-diagram-items' : 'one-diagram-items' }}">
                            @foreach ($data['children'] as $k => $child )
                                @if (in_array($k, [1, 3]))
                                    <div class="diagram-child-item">
                                        <div class="final-items-wrapper">
                                            <div class="final-items
                                                @if (count($child['finals']) == 3)
                                                    three-finals has-middle-stick
                                                @elseif(count($child['finals']) == 2)
                                                    two-finals
                                                @else
                                                    has-middle-stick
                                                @endif
                                            ">
                                                @foreach ($child['finals'] as $final )
                                                    <div class="final">
                                                        <div class="diagram-element">
                                                            <div class="diagram-element-background"
                                                                 style="background-image: url('{{ $final['final_artwork_url'] }}')"></div>
                                                            <div class="diagram-content">
                                                                @if($final['title'])
                                                                    <div class="adventure-title">{{ $data['adventure_title'] }}</div>
                                                                    <div class="audio-title">{{ $final['title'] }}</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="diagram-element">
                                            <div class="diagram-element-background"
                                                 style="background-image: url('{{ $child['child_artwork_url'] }}')"></div>
                                            <div class="diagram-content">
                                                @if($child['title'])
                                                    <div class="adventure-title">{{ $child['title'] }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
            <div class="diagram-buttons">
                <a href="{{ route('frontend.user.adventures', auth()->user()->username) }}" class="btn-default btn-outline">
                    Go back
                </a>
                <a href="" class="btn-default btn-outline" id="adventure-settings" data-id="{{ $data['adventure_id'] }}">
                    Edit Adventure
                </a>
            </div>
        </div>
    </div>
@endsection
