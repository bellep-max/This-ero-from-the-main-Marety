@php use App\Services\Backend\BackendService; @endphp
@extends('backend.index')
@section('content')
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('backend.dashboard') }}">Control Panel</a>
            </li>
            <li class="breadcrumb-item active"><a href="{{ route('backend.groups.index') }}">User Groups</a></li>
            <li class="breadcrumb-item active">{{ $group->name }}</li>
        </ol>
        <div class="row">
            <div class="col-lg-12">
                <ul class="nav nav-tabs nav-justified">
                    <li class="nav-item"><a href="#general" class="nav-link active show"  data-toggle="pill"><i class="fas fa-fw fa-cog"></i> General Settings</a></li>
                    @if(request()->route('id') != 6)
                        <li class="nav-item"><a href="#upload" class="nav-link"  data-toggle="pill"><i class="fas fa-fw fa-microphone"></i> Music Options</a></li>
                        <li class="nav-item"><a href="#podcast" class="nav-link"  data-toggle="pill"><i class="fas fa-fw fa-microphone"></i> Podcast Options</a></li>
                        <li class="nav-item"><a href="#monetization" class="nav-link"  data-toggle="pill"><i class="fas fa-fw fa-money"></i> Monetization</a></li>
                        <li class="nav-item"><a href="#blog" class="nav-link"  data-toggle="pill"><i class="fas fa-fw fa-newspaper"></i> Blog</a></li>
                        <li class="nav-item"><a href="#comment" class="nav-link" data-toggle="pill"><i class="fas fa-fw fa-comment"></i> Comments</a></li>
                        <li class="nav-item"><a href="#admin" class="nav-link" data-toggle="pill"><i class="fas fa-fw fa-lock"></i> Administration panel</a></li>
                    @endif
                </ul>
                <form method="POST" action="{{ route('backend.groups.update', $group) }}">
                    @csrf
                    @method('PATCH')
                    <div class="tab-content mt-4" id="role-content">
                        <div id="general" class="tab-pane fade active show">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <div class="col-sm-8 ">
                                                    <label>Group name</label>
                                                    <p class="small mb-0">Short name of the group. Not more than 20 characters.</p>
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="group_name" value="{{ $group->name }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Limit the Duration of Downloaded Files (minutes)
                                                    <span class="small mb-0"></span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[duration_limit]" value="{{ array_get($group->permissions, 'duration_limit') ?? config('settings.podcast_min_audio_duration') }}">
                                                </div>
                                            </div>
                                            @if(request()->route('id') != \App\Constants\RoleConstants::GUEST)
                                                <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                    <div class="col-sm-8 ">
                                                        <label>Group badge</label>
                                                        <p class="small mb-0">This is a visual feature designed to help people learn about other group members. There are badges for admins, moderators, new members, for a member's group anniversary, members identified as conversation starters, and for founding members. HTML is accepted.</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control" name="save_role[group_badge]" value="{{ array_get($group->permissions, 'group_badge') }}">
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <div class="col-sm-8 col-9">
                                                    <label class=" mb-0">Ad support</label>
                                                    <p class="small mb-0">The member of this group can listen to unlimited streaming music for free. Unfortunately, they’ll have to see advertisements.</p>
                                                </div>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[ad_support]" value="1" @if(array_get($group->permissions, 'ad_support')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <div class="col-sm-8 col-9 ">
                                                    <label class=" mb-0">Allow to view a disabled site</label>
                                                    <p class="small mb-0">You can enable or disable this group to see the website when it is disabled.</p>
                                                </div>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[allow_offline]" value="1" @if(array_get($group->permissions, 'allow_offline')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <div class="col-sm-8 col-9">
                                                    <label class=" mb-0">Use feedback</label>
                                                    <p class="small mb-0">Allow a user to use the feedback of the site to send e-mails to registered users. If denied, he/she will be able to send letters only to the website Administrators.</p>
                                                </div>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[option_feedback]" value="1" @if(array_get($group->permissions, 'option_feedback')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            @if(request()->route('id') != \App\Constants\RoleConstants::GUEST)
                                                <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                    <div class="col-sm-8 col-9">
                                                        <label class=" mb-0">Allow change the avatar</label>
                                                        <p class="small mb-0">Allow a user to change his/her avatar. If denied, he/she will be not able to upload a new avatar.</p>
                                                    </div>
                                                    <div class="col-sm-4 col-3">
                                                        <label class="switch"><input type="checkbox" name="save_role[option_avatar]" value="1" @if(array_get($group->permissions, 'option_avatar')) checked="checked" @endif><span class="slider round"></span></label>
                                                    </div>
                                                </div>
                                                <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                    <div class="col-sm-8 ">
                                                        <label class=" mb-0">The maximum number of characters in the brief information</label>
                                                        <p class="small mb-0">Enter the maximum number of characters in the brief information about a user. If you want to remove the restriction on the characters number, enter 0.</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control" name="save_role[option_max_info_chars]" value="{{ array_get($group->permissions, 'option_max_info_chars') }}">
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <div class="col-sm-8 col-9 ">
                                                    <label class=" mb-0">Allow to play (stream) music/podcast</label>
                                                    <p class="small mb-0">You can enable or disable this group to able to play music/podcast. For example you can force guest to register an account and login before they can play music/podcast.</p>
                                                </div>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[option_stream]" value="1" @if(array_get($group->permissions, 'option_stream')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <div class="col-sm-8 col-9">
                                                    <label class=" mb-0">Allow to stream high quality version of the audio</label>
                                                    <p class="small mb-0">If there is a HD version of the song, allow this group listen to it.</p>
                                                </div>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[option_hd_stream]" value="1" @if(array_get($group->permissions, 'option_hd_stream')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <div class="col-sm-8 col-9">
                                                    <label class=" mb-0">Allow downloads media (audio mp3/post attachment ...)</label>
                                                    <p class="small mb-0">Allow the user to download music file.</p>
                                                </div>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[option_download]" value="1" @if(array_get($group->permissions, 'option_download')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <div class="col-sm-8 col-9">
                                                    <label class=" mb-0">Allow downloads high quality version of audio</label>
                                                    <p class="small mb-0">Allow the user to download the high quality version of music file. <span class="text-danger">To have this feature works, you have to enable option "Allow to storage Lossless high definition audio
" which is located at script settings.</span></p>
                                                </div>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[option_download_hd]" value="1" @if(array_get($group->permissions, 'option_download_hd')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <div class="col-sm-8 ">
                                                    <label class=" mb-0">Maximum download speed (kb/s)</label>
                                                    <p class="small mb-0">You can limit the maximum download speed. The maximum speed is indicated in kb/s. If you do not want to put restrictions on the speed of the files downloading, enter 0.</p>
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[option_download_speed]" value="{{ array_get($group->permissions, 'option_download_speed') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <div class="col-sm-8 col-9">
                                                    <label class=" mb-0">Enable resuming of download when downloading files</label>
                                                    <p class="small mb-0">You can enable or disable resuming files downloading for your users. It allows to resume the files downloading if there was a disconnection.</p>
                                                </div>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[option_download_resume]" value="1" @if(array_get($group->permissions, 'option_download_resume')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="upload" class="tab-pane fade">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <div class="col-sm-8 col-9">
                                                    <label class=" mb-0">Allow upload media</label>
                                                    <p class="small mb-0">Allow the user to upload music file.</p>
                                                </div>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[artist_allow_upload]" value="1" @if(array_get($group->permissions, 'artist_allow_upload')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Publish without verification
                                                    <span class="small mb-0">If ‘Yes’, then the user will be able to upload in the trusted sections without verifying by the site administrator.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[artist_mod]" value="1" @if(array_get($group->permissions, 'artist_mod')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <!--
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Trusted sections
                                                    <p class="small mb-0">If user is allowed to upload music without verification, you can specify sections where user can add upload without a moderation and sections.</p>
                                                </label>
                                                <div class="col-sm-4">
                                                    <select multiple="" class="form-control select2-active" name="save_role[artist_trusted_genre][]">
                                                        <optgroup label="----- Select All -----">
{{--                                                            {!! genreSelection(array_get($group->permissions, 'artist_trusted_genre')) !!}--}}
                                                        </optgroup>
                                                    </select>
                                                </div>
                                            </div>
                                            -->
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Maximum number of uploading songs
                                                    <span class="small mb-0">If users are allowed to uploading song, you can also specify a maximum number of uploading files.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[artist_num_files_allow]" value="{{ array_get($group->permissions, 'artist_num_files_allow') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Maximum number of uploading songs per week
                                                    <span class="small mb-0">If users are allowed to uploading song, you can also specify a maximum number of uploading files. To remove this limitation, leave the field blank or enter 0.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[artist_max_songs_per_week]" value="{{ array_get($group->permissions, 'artist_max_songs_per_week', 3) }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">The number of days during which it is allowed to edit the song
                                                    <span class="small mb-0">If this group is allowed to upload song/album, you can specify a period of days after the publication of the song/album it is allowed to edit it. After the end of this period the member of the group will not be able to edit the song/album. To disable this period, specify 0.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[artist_day_edit_limit]" value="{{ array_get($group->permissions, 'artist_day_edit_limit') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">The maximum number of mp3 files to be uploaded for each time
                                                    <span class="small mb-0">If user is allowed to upload mp3 files you can set a limit for the number of images to be loaded for each publication. To remove this limitation, leave the field blank or enter 0.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[artist_files_upload_each_time]" value="{{ array_get($group->permissions, 'artist_files_upload_each_time') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">The maximum number of files to be uploaded for each album
                                                    <span class="small mb-0">If user is allowed to create album you can set a limit for the number of song to be loaded for each publication. To remove this limitation, leave the field blank or enter 0.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[artist_files_each_album]" value="{{ array_get($group->permissions, 'artist_files_each_album') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">The maximum number of songs
                                                    <span class="small mb-0">If user is allowed to upload music, you can set a limit for the number of audios can be to be uploaded. To remove this limitation, leave the field blank or enter 0.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[artist_max_songs]" value="{{ array_get($group->permissions, 'artist_max_songs') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">The maximum number of albums
                                                    <span class="small mb-0">If user is allowed to create album, you can set a limit for the number of album can be created. To remove this limitation, leave the field blank or enter 0.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[artist_max_albums]" value="{{ array_get($group->permissions, 'artist_max_albums') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">The maximum number of playlists
                                                    <span class="small mb-0">If user is allowed to create playlists, you can set a limit for the number of playlists can be created. To remove this limitation, leave the field blank or enter 0.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[artist_max_playlists]" value="{{ array_get($group->permissions, 'artist_max_playlists') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">The maximum number of playlist's songs
                                                    <span class="small mb-0">If user is allowed to create playlists, you can set a limit for the number of song in the playlist. To remove this limitation, leave the field blank or enter 0.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[artist_max_playlist_songs]" value="{{ array_get($group->permissions, 'artist_max_playlist_songs') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="podcast" class="tab-pane fade">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <div class="col-sm-8 col-9">
                                                    <label class=" mb-0">Allow Create/Update podcast show</label>
                                                    <p class="small mb-0">Allow the user to create podcast show by import rss or upload episodes.</p>
                                                </div>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[artist_allow_podcast]" value="1" @if(array_get($group->permissions, 'artist_allow_podcast')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <!--
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Available Categories
                                                    <p class="small mb-0">List of Categories where the users are allowed to access. They will be able to publish podcast show from these.</p>
                                                </label>
                                                <div class="col-sm-4">
                                                    <select multiple="" class="form-control select2-active" name="save_role[artist_podcast_allow_categories][]">
                                                        <optgroup label="----- Select All -----">
{{--                                                            {!! podcastCategorySelection(array_get($group->permissions, 'artist_podcast_allow_categories')) !!}--}}
                                                        </optgroup>
                                                    </select>
                                                </div>
                                            </div>
                                            -->
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Publish without verification
                                                    <span class="small mb-0">If ‘Yes’, then the user will be able to create show in the trusted sections without verifying by the site administrator.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[artist_podcast_mod]" value="1" @if(array_get($group->permissions, 'artist_podcast_mod')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <!--
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Trusted sections
                                                    <p class="small mb-0">If user is allowed to create podcast show without verification, you can specify sections where user can add upload without a moderation and sections.</p>
                                                </label>
                                                <div class="col-sm-4">
                                                    <select multiple="" class="form-control select2-active" name="save_role[artist_podcast_trusted_categories][]">
                                                        <optgroup label="----- Select All -----">
{{--                                                            {!! podcastCategorySelection(array_get($group->permissions, 'artist_podcast_trusted_categories')) !!}--}}
                                                        </optgroup>
                                                    </select>
                                                </div>
                                            </div>
                                            -->
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Maximum number of episode per podcast
                                                    <span class="small mb-0">If user is allowed to create podcast you can set a limit for the number of episodes to be loaded for each publication. To remove this limitation, leave the field blank or enter 0.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[artist_podcast_max_episodes]"  value="{{ array_get($group->permissions, 'artist_podcast_max_episodes') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">The number of days during which it is allowed to edit the podcast
                                                    <span class="small mb-0">If this group is allowed to create podcast, you can specify a period of days after the publication of the podcast it is allowed to edit it. After the end of this period the member of the group will not be able to edit the podcast. To disable this period, specify 0.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[artist_podcast_day_edit_limit]" value="{{ array_get($group->permissions, 'artist_podcast_day_edit_limit') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">The maximum number of podcast
                                                    <span class="small mb-0">If user is allowed to create podcast, you can set a limit for the number of podcast can be created. To remove this limitation, leave the field blank or enter 0.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[artist_max_podcasts]" value="{{ array_get($group->permissions, 'artist_max_podcasts') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="monetization" class="tab-pane fade">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <div class="col-sm-8 col-9">
                                                    <label class=" mb-0">Allow artist to sell their's song and album</label>
                                                    <span class="small mb-0">Allow the user to create podcast show by import rss or upload episodes.</span>
                                                </div>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[monetization_sale]" value="1" @if(array_get($group->permissions, 'monetization_sale')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Artist sale cut, in percent
                                                    <span class="small mb-0">Everyone involved in making the music gets their cut.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[monetization_sale_cut]"  value="{{ array_get($group->permissions, 'monetization_sale_cut') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Minimum price of song
                                                    <span class="small mb-0">Allow to set the minimum price that artist can set when selling their song.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[monetization_song_min_price]"  value="{{ array_get($group->permissions, 'monetization_song_min_price') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Maximum price of song
                                                    <span class="small mb-0">Allow to set the maximum price that artist can set when selling their song.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[monetization_song_max_price]"  value="{{ array_get($group->permissions, 'monetization_song_max_price') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Minimum price of album
                                                    <span class="small mb-0">Allow to set the minimum price that artist can set when selling their album.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[monetization_album_min_price]"  value="{{ array_get($group->permissions, 'monetization_song_min_price') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Maximum price of album
                                                    <span class="small mb-0">Allow to set the maximum price that artist can set when selling their album.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[monetization_album_max_price]"  value="{{ array_get($group->permissions, 'monetization_song_max_price') }}">
                                                </div>
                                            </div>

                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <div class="col-sm-8 col-9">
                                                    <label class=" mb-0">Streaming Royalties</label>
                                                    <p class="small mb-0">Streaming allows artists to generate income from digital music consumption. Enable this option if you want to pay artist when their songs or podcast's episode get streamed.</p>
                                                </div>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[monetization_streaming]" value="1" @if(array_get($group->permissions, 'monetization_streaming')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Set the revenue artist will get for every streamed song and podcast's episode (in {{ config('settings.currency', 'USD') }})
                                                    <span class="small mb-0">For example Apple is paying $0.00783 per stream.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[monetization_streaming_rate]"  value="{{ array_get($group->permissions, 'monetization_streaming_rate') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Minimum withdraw for Paypal (in {{ config('settings.currency', 'USD') }})
                                                    <span class="small mb-0">Set the the minimum amount artist can withdraw from their account.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <input type="text" class="form-control" name="save_role[monetization_paypal_min_withdraw]"  value="{{ array_get($group->permissions, 'monetization_paypal_min_withdraw') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Minimum withdraw for Bank Transfer (in {{ config('settings.currency', 'USD') }})
                                                    <span class="small mb-0">Set the the minimum amount artist can withdraw from their account.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <input type="text" class="form-control" name="save_role[monetization_bank_min_withdraw]"  value="{{ array_get($group->permissions, 'monetization_bank_min_withdraw') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="blog" class="tab-pane fade">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <div class="col-sm-8 col-9">
                                                    <label class=" mb-0">Available categories</label>
                                                    <p class="small mb-0">List of categories where the users are allowed to access. They will be able to view news from these categories, as well as categories themselves.</p>
                                                </div>
                                                <div class="col-sm-4">
                                                    <select multiple="" class="form-control select2-active" name="save_role[blog_allow_view_categories][]">
                                                        <optgroup label="----- Select All -----">
                                                            {!! BackendService::categorySelection(array_get($group->permissions, 'blog_allow_view_categories')) !!}
                                                        </optgroup>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <div class="col-sm-8 col-9">
                                                    <label class=" mb-0">Categories that are prohibited for viewing</label>
                                                    <p class="small mb-0">This setting is opposite to the setting above. You can specify a list of categories which are denied to be viewed by users. They will not be allowed to browse the articles of these categories, as well as the categories themselves. Use this setting, or the setting above, depending on what is better for you: to specify a list of restricted categories or a list of permitted categories.</p>
                                                </div>
                                                <div class="col-sm-4">
                                                    <select multiple="" class="form-control select2-active" name="save_role[blog_prohibited_view_categories][]">
                                                        <optgroup label="----- Select All -----">
                                                            {!! BackendService::categorySelection(array_get($group->permissions, 'blog_prohibited_view_categories')) !!}
                                                        </optgroup>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">List of categories where users can add news
                                                    <span class="small mb-0">Choose the categories where the users of this group can add news. Only those categories will be listed in the list of categories where it is allowed to add news.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <select multiple="" class="form-control select2-active" name="save_role[blog_allow_add_categories][]">
                                                        <optgroup label="----- Select All -----">
                                                            {!! BackendService::categorySelection(array_get($group->permissions, 'blog_allow_add_categories')) !!}
                                                        </optgroup>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Publish news without verification
                                                    <span class="small mb-0">If ‘Yes’, then the user will be able to publish news in the trusted sections without verifying by the site administrator.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch">
                                                        {!! BackendService::makeCheckbox('save_role[blog_allow_public_directly]', array_get($group->permissions, 'blog_allow_public_directly')) !!}
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Trusted sections
                                                    <span class="small mb-0">If user is allowed to add news without verification, you can specify sections where user can add news wthout a moderation and sections where user can edit articles of the other users.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <select multiple="" class="form-control select2-active" name="save_role[blog_trust_categories][]">
                                                        <optgroup label="----- Select All -----">
                                                            {!! BackendService::categorySelection(array_get($group->permissions, 'blog_trust_categories')) !!}
                                                        </optgroup>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">View hidden text
                                                    <span class="small mb-0">Allow user to view a text between [hide] [/hide] tags..</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[blog_allow_hide]" value="1" @if(array_get($group->permissions, 'blog_allow_hide')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow to vote in voting published in news
                                                    <span class="small mb-0">You can allow or deny this group to participate in the voting, which are added in the news.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch">
                                                        {!! BackendService::makeCheckBox('save_role[blog_allow_vote]', array_get($group->permissions, 'blog_allow_vote')) !!}
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow to use HTML when adding news
                                                    <span class="small mb-0">This option allows using HTML when adding news to the website. Please note, if you disable this option, and WYSIWYG editor is enabled in Script Settings.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch">
                                                        {!! BackendService::makeCheckBox('save_role[blog_allow_html]', array_get($group->permissions, 'blog_allow_html')) !!}
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow to publish on the Homepage
                                                    <span class="small mb-0">You can enable or disable publication on Homepage for this group</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch">
                                                        {!! BackendService::makeCheckBox('save_role[blog_allow_public_home]', array_get($group->permissions, 'blog_allow_public_home')) !!}
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow to fix news
                                                    <span class="small mb-0">Allow users to capture news on the website.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch">
                                                        {!! BackendService::makeCheckBox('save_role[blog_allow_public_fixed]', array_get($group->permissions, 'blog_allow_public_fixed')) !!}
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow users to upload images
                                                    <span class="small mb-0">You can allow or deny users to upload images when they add a news article to the website.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch">
                                                        {!! BackendService::makeCheckBox('save_role[blog_allow_upload_images]', array_get($group->permissions, 'blog_allow_upload_images')) !!}
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Allow files uploading
                                                    <span class="small mb-0">It will be allowed to upload images and other files.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <label class="switch">
                                                        {!! BackendService::makeCheckBox('save_role[blog_allow_upload_files]', array_get($group->permissions, 'blog_allow_upload_files')) !!}
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">The maximum number of images to be uploaded for each publication
                                                    <span class="small mb-0">If user is allowed to upload images for news articles you can set a limit for the number of images to be loaded for each publication. To remove this limitation, leave the field blank or enter 0..</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[blog_num_files_allow]"  value="{{ array_get($group->permissions, 'blog_num_files_allow') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">The maximum number of files to be uploaded for each publication
                                                    <span class="small mb-0">If user is allowed to upload images for news articles you can set a limit for the number of images to be loaded for each publication. To remove this limitation, leave the field blank or enter 0.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[blog_day_edit_limit]" value="{{ array_get($group->permissions, 'blog_day_edit_limit') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">File extensions that are allowed to be uploaded
                                                    <span class="small mb-0">Enter file extensions separated by commas which are allowed for uploading. Attention! Don’t specify the image extensions. They are automatically determined by the script. If you specify the image extensions, the script will offer to domnload it, but not to show it.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[blog_upload_extensions]" value="{{ array_get($group->permissions, 'blog_upload_extensions') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Maximum file size allowable for uploading (in kilobytes)
                                                    <span class="small mb-0">Enter the maximum file size that is allowed to be uploaded. Size should be specified in kilobytes. For example, you should specify 2048 to limit the file size by 2 megabytes. If you want to remove the restriction, then enter 0 in the settings.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[blog_upload_size]" value="{{ array_get($group->permissions, 'blog_upload_size') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <div class="col-sm-8 col-9">
                                                    <label class=" mb-0">Allow downloads Attachment</label>
                                                    <p class="small mb-0">Allow the user to download the attachment files.</p>
                                                </div>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[blog_download]" value="1" @if(array_get($group->permissions, 'blog_download')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Maximum download speed (kb/s)
                                                    <span class="small mb-0">You can limit the maximum download speed. The maximum speed is indicated in kb/s. If you do not want to put restrictions on the speed of the files downloading, enter 0.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[blog_download_speed]" value="{{ array_get($group->permissions, 'blog_download_speed') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Flooding protection when you add news
                                                    <span class="small mb-0">Set the number of seconds during which the user can not re-add news. To turn off the protection and control, enter 0.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[blog_flood]" value="{{ array_get($group->permissions, 'blog_flood') }}">
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Maximum number of news per day
                                                    <span class="small mb-0">Set the number of news the user can publish per day. To disable this limit, enter 0.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[blog_news_per_day]" value="{{ array_get($group->permissions, 'blog_news_per_day') }}">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="comment" class="tab-pane fade">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow to post comments
                                                    <span class="small mb-0">Allow the user to add comments on the site.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[comment_allow]" value="1" @if(array_get($group->permissions, 'comment_allow')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Send comments for moderation
                                                    <span class="small mb-0">User’s comment will be published on the website only when it will be moderated by moderator or administrator.</span>
                                                    <span class="small text-danger">Warning! This module has been disabled in your script. For its full operation, it is necessary to include this module in the optimization section of your script.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[comment_modc]" value="1" @if(array_get($group->permissions, 'comment_modc')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <!--
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">The maximum number of characters in the signature
                                                    <p class="small mb-0">Enter the maximum number of characters allowed in a user's signature. If you want to remove the restriction on the characters number, enter 0.</p>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[comment_max_char]" value="{{ array_get($group->permissions, 'comment_max_char') }}">
                                                </div>
                                            </div>
                                            -->
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow to use clickable Links
                                                    <span class="small mb-0">You can allow or deny users to use clickable links in comments, profile or personal messages.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[comment_url]" value="1" @if(array_get($group->permissions, 'comment_url')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Edit all the comments
                                                    <span class="small mb-0">Allow the user to edit any comments on his profile page, song page and playlist page.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[comment_edit]" value="1" @if(array_get($group->permissions, 'comment_edit')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Delete profile, profile's song and profile's playlist comments
                                                    <span class="small mb-0">Allow the user to delete comments on his profile page, song page and playlist page.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[option_comment_delete]" value="1" @if(array_get($group->permissions, 'option_comment_delete')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 mb-0">Time limit for delete and edit comments
                                                    <span class="small mb-0">Set the time limit of time (in minutes) when user can edit or delete his/her comments if he/she is allowed to do so. To disable this limit, enter 0.	.</span>
                                                </label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" name="save_role[comment_day_limit_edit]" value="{{ array_get($group->permissions, 'comment_day_limit_edit') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(request()->route('id') != 6)
                        <div id="admin" class="tab-pane fade">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow access Panel
                                                    <span class="small mb-0">This option does not provide the full access to all sections. It only allows view dashboard section.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_access]" value="1" @if(array_get($group->permissions, 'admin_access')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of System Settings
                                                    <span class="small text-danger mb-0">This option provide the access to System Settings.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_settings]" value="1" @if(array_get($group->permissions, 'admin_settings')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Email Template
                                                    <span class="small text-danger mb-0">This option provide the access to email template & role.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_email]" value="1" @if(array_get($group->permissions, 'admin_email')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of SEO Meta Tags
                                                    <span class="small text-danger mb-0">This option provide the access to create, edit, delete meta tags.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_metatags]" value="1" @if(array_get($group->permissions, 'admin_metatags')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Languages
                                                    <span class="small text-danger mb-0">This option provide the access to create, edit, delete language, it also providing permission to set site's default language.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_languages]" value="1" @if(array_get($group->permissions, 'admin_languages')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Usergroup & Role
                                                    <span class="small text-danger mb-0">This option provide the access to usergroup & role.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_roles]" value="1" @if(array_get($group->permissions, 'admin_roles')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Media Manager
                                                    <span class="small text-danger mb-0">This option provide the access, create, upload, edit file in storage folder.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_media_manager]" value="1" @if(array_get($group->permissions, 'admin_media_manager')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Sitemap
                                                    <span class="small text-danger mb-0">This option provide the access to view, create sitemap.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_sitemap]" value="1" @if(array_get($group->permissions, 'admin_sitemap')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Backup
                                                    <span class="small text-danger mb-0">This option provide the access to backup section, the group can make backup, download and restore engine from the backup.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_backup]" value="1" @if(array_get($group->permissions, 'admin_backup')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of API Tester
                                                    <span class="small text-danger mb-0">This option provide the access to the API TESTER.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_api_tester]" value="1" @if(array_get($group->permissions, 'admin_api_tester')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of System logs
                                                    <span class="small text-danger mb-0">This option provide the access to view system logs & role.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_system_logs]" value="1" @if(array_get($group->permissions, 'admin_system_logs')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Data Dictionary
                                                    <span class="small text-danger mb-0">This option provide the access to data dictionary.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_dictionary]" value="1" @if(array_get($group->permissions, 'admin_dictionary')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Scheduled task
                                                    <span class="small text-danger mb-0">This option provide the access to view scheduled task.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_scheduled]" value="1" @if(array_get($group->permissions, 'admin_scheduled')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Earnings
                                                    <span class="small text-danger mb-0">This option provide the access to Earnings section, let the group view recent orders and process payment request by artist.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_earnings]" value="1" @if(array_get($group->permissions, 'admin_earnings')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Subscriptions
                                                    <span class="small text-danger mb-0">This option provide the access to Subscriptions section, let the group manage, edit, delete Subscriptions.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_subscriptions]" value="1" @if(array_get($group->permissions, 'admin_subscriptions')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow to manage promotional materials in the Administration Panel
                                                    <span class="small text-danger mb-0">This option allows users with access to Administration Panel create, edit and delete promotional materials in the Administration Panel.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_banners]" value="1" @if(array_get($group->permissions, 'admin_banners')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Categories
                                                    <span class="small mb-0">This option provide the access, create, edit, delete category.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_categories]" value="1" @if(array_get($group->permissions, 'admin_categories')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Post
                                                    <span class="small mb-0">This option provide the access, create, edit, delete post.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_posts]" value="1" @if(array_get($group->permissions, 'admin_posts')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Artist Clamming Request
                                                    <span class="small mb-0">This option allows users with access to approve or reject the Request Artist Access.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_artist_claim]" value="1" @if(array_get($group->permissions, 'admin_artist_claim')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Genres
                                                    <span class="small mb-0">This option allows users with access to create, edit and delete genre.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_genres]" value="1" @if(array_get($group->permissions, 'admin_genres')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Radio
                                                    <span class="small mb-0">This option allows users with access to create, edit and delete radio category, station.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_radio]" value="1" @if(array_get($group->permissions, 'admin_radio')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow to management of Channels
                                                    <span class="small mb-0">This option allows users with access to create, edit and delete channel.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_channels]" value="1" @if(array_get($group->permissions, 'admin_channels')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow to management of Slide show
                                                    <span class="small mb-0">This option allows users with access to create, edit and delete slide show.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_slideshow]" value="1" @if(array_get($group->permissions, 'admin_slideshow')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Playlists
                                                    <span class="small mb-0">This option allows users with access to create, edit and delete Playlists.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_playlists]" value="1" @if(array_get($group->permissions, 'admin_playlists')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Artists
                                                    <span class="small mb-0">This option allows users with access to create, edit and delete Artists.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_artists]" value="1" @if(array_get($group->permissions, 'admin_artists')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Albums
                                                    <span class="small mb-0">This option allows users with access to create, edit and delete Albums.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_albums]" value="1" @if(array_get($group->permissions, 'admin_albums')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Songs
                                                    <span class="small mb-0">This option allows users with access to create, edit and delete Songs.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_songs]" value="1" @if(array_get($group->permissions, 'admin_songs')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Songs
                                                    <span class="small mb-0">This option allows users with access to create, edit and delete Songs.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_songs]" value="1" @if(array_get($group->permissions, 'admin_songs')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow the management of Users
                                                    <span class="small mb-0">This option allows users with access to manage other users’ profiles.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_users]" value="1" @if(array_get($group->permissions, 'admin_users')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow to management of Pages
                                                    <span class="small mb-0">This option allows users with access to create, edit and delete page.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_pages]" value="1" @if(array_get($group->permissions, 'admin_pages')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3">
                                                <label class="col-sm-8 col-9 mb-0">Allow to management of Comments
                                                    <span class="small mb-0">This option allows users with access to edit and delete comment.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_comments]" value="1" @if(array_get($group->permissions, 'admin_comments')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                            <div class="form-group row border-bottom mb-0 pt-3 pb-3 bg-danger">
                                                <label class="col-sm-8 col-9 mb-0 text-white">Allow access Terminal
                                                    <span class="small mb-0">This tool should enabled for SUPER ADMIN only, it use for develop purpose, do not make change, run commands... if you have no idea what is it.</span>
                                                </label>
                                                <div class="col-sm-4 col-3">
                                                    <label class="switch"><input type="checkbox" name="save_role[admin_terminal]" value="1" @if(array_get($group->permissions, 'admin_terminal')) checked="checked" @endif><span class="slider round"></span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="mt-3 clearfix">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="reset" class="btn btn-info">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
