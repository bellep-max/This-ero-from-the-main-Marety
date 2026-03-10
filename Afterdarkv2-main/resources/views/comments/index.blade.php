<div class="page-post-comment w-100">
    <form class="comment-form flex-row justify-content-between align-items-center gap-4" method="POST" action="{{ route('frontend.comments.add') }}" novalidate>
        <input type="hidden" name="commentable_type" value="{{ $object->type }}">
        <input type="hidden" name="commentable_id" value="{{ $object->id }}">
        <div class="comment-input-container">
            <div class="comment-feed-msg" contenteditable="true" placeholder="Talk about {{ $object->title }}..."></div>
            <a class="insert-emoji">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                    <path d="M0 0h24v24H0z" fill="none"/>
                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                </svg>
            </a>
        </div>
        <input id="comment-submit-button" class="hide" type="submit">
        <label class="btn-default btn-rounded btn-pink" for="comment-submit-button">
{{--            <svg xmlns="http://www.w3.org/2000/svg" width="39" height="40" viewBox="0 0 512 512">--}}
{{--                <!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->--}}
{{--                <path d="M16.1 260.2c-22.6 12.9-20.5 47.3 3.6 57.3L160 376l0 103.3c0 18.1 14.6 32.7 32.7 32.7c9.7 0 18.9-4.3 25.1-11.8l62-74.3 123.9 51.6c18.9 7.9 40.8-4.5 43.9-24.7l64-416c1.9-12.1-3.4-24.3-13.5-31.2s-23.3-7.5-34-1.4l-448 256zm52.1 25.5L409.7 90.6 190.1 336l1.2 1L68.2 285.7zM403.3 425.4L236.7 355.9 450.8 116.6 403.3 425.4z"/>--}}
{{--            </svg>--}}
            <svg xmlns="http://www.w3.org/2000/svg" width="39" height="40" viewBox="13 21 38 22">
                <path d="M48.3105 17.915C48.1514 17.6903 47.9317 17.5154 47.6771 17.4108C47.4224 17.3061 47.1432 17.276 46.8721 17.3239L14.3178 23.1488L13.614 25.7752L24.4582 33.888L29.0373 47.9616L31.6643 48.6655L48.3838 19.4686C48.52 19.2293 48.5855 18.9562 48.5725 18.6812C48.5595 18.4061 48.4687 18.1404 48.3105 17.915ZM30.7136 45.7259L26.7776 33.6288L40.1067 25.1034L38.8751 23.1778L25.4418 31.7696L16.5008 25.0805L45.5078 19.89L30.7136 45.7259Z"/>
            </svg>
        </label>
    </form>
</div>
{{--@if($showComments)--}}
    <div class="more-comments hide">
        <span>View more comments</span>
        <div class="loading"></div>
    </div>
    <div data-comment-init="true"
         data-commentable-type="{{ $object->type }}"
         data-commentable-id="{{ $object->id }}"
         class="comments-grid d-flex flex-column bg-light rounded-4 p-3 gap-4 mt-4"
         @if(isset($mod))
             data-mod="{{ $mod }}"
         @endif
    ></div>
{{--@endif--}}
