@extends('backend.index')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('backend.dashboard') }}">Control Panel</a>
        </li>
        <li class="breadcrumb-item active">Comments ({{ $comments->total() }})</li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <div class="btn-group mb-4" role="group" aria-label="Basic example">
                <a href="{{ route('backend.comments.index') }}" class="btn @if(Route::currentRouteName() == 'backend.comments.index')) btn-warning @else btn-dark @endif">Awaiting for moderation ({{ \App\Models\Comment::withoutGlobalScopes()->notApproved()->count() }})</a>
                <a href="{{ route('backend.comments.approved') }}" class="btn @if(Route::currentRouteName() == 'backend.comments.approved') btn-success @else btn-dark @endif">Approved ({{ \App\Models\Comment::withoutGlobalScopes()->approved()->count() }})</a>
            </div>
            <table class="table datatables table-hover">
                <thead>
                <tr>
                    <th class="desktop">Avatar</th>
                    <th class="desktop">Author</th>
                    <th class="desktop">Content</th>
                    <th class="desktop">Object</th>
                    <th class="desktop">Created</th>
                    <th class="desktop">Approved</th>
                    <th class="th-2action">Action</th>
                </tr>
                </thead>
                <tbody>
                    @include('backend.commons.comment', ['comments' => $comments])
                </tbody>
            </table>
            <div class="pagination pagination-right">
                {{ $comments->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
@endsection