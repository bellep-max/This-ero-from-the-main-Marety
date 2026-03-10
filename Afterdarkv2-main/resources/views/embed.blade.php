<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ MetaTag::get('title') }}</title>
    {!! MetaTag::tag('description') !!}
    {!! MetaTag::tag('keywords') !!}
    {!! MetaTag::get('image') ? MetaTag::tag('image') : '' !!}
    {!! MetaTag::openGraph() !!}
    {!! MetaTag::twitterCard() !!}
    <style>
        body {
            padding: 0;
            margin: 0;
        }
    </style>
</head>
<body @class([
        $theme === 'light' => 'light-theme'
      ])
>

{{--<script>--}}
{{--    var youtube_api_key = '{{ config('settings.youtube_api_key') }}';--}}
{{--    var youtube_search_endpoint = '{{ route('frontend.song.stream.youtube', ['id' => 'SONG_ID']) }}';--}}
{{--</script>--}}
@php
    $themeColor = $theme == 'light' ? '555' : 'fff';
@endphp
@if ($type != 'station')
    <script type="text/javascript" src="{{ asset("assets/embed/embed.js?preload=true&type={$type}&icon_set=radius&id={$id}&skin=widget&visualizer=true&visualizerColor={$themeColor}") }}"></script>
@else
    <script type="text/javascript" src="{{ asset("assets/embed/embed.js?preload=true&type={$type}&icon_set=radius&id={$id}&skin=widget") }}"></script>
@endif
</body>
</html>