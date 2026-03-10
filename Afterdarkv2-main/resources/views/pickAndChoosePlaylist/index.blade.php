@extends('index')
@section('content')
    <div class="pick-and-choose-playlist">
        <div class="pick-and-choose-playlist__title">
            <h1>Pick and choose playlist</h1>
        </div>
        <div class="pick-and-choose-playlist__desc">
            Pick and choose playlist desc
        </div>
        <div class="row" style="margin-top: 30px;">
            <div class="col-lg-4 col-12">
                <div class="img">
                    <img id="page-cover-art"
                         src="https://erocast.s3.us-east-2.wasabisys.com/1238753/conversions/1702361249-lg.jpg"
                         alt="Best of Today">
                    <div class="inner mobile">
                        <h1 title="Best of Today">Best of Today </h1>
                        <div class="byline">
                            <span class="byline">by <a href="https://musicengine.top/artist/237/bbc-radio-4"
                                                       class="artist-link artist"
                                                       title="BBC Radio 4">BBC Radio 4</a></span>
                        </div>
                        <div class="actions-primary">
                            <a class="btn btn-favorite favorite " data-type="podcast" data-id="73330187"
                               data-title="Best of Today"
                               data-url="https://musicengine.top/podcast/73330187/best-of-today"
                               data-text-on="Unsubscribe" data-text-off="Subscribe">
                                <svg class="off" height="26" viewBox="0 0 24 24" width="18"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path>
                                    <path d="M0 0h24v24H0z" fill="none"></path>
                                </svg>
                                <svg class="on" height="26" viewBox="0 0 24 24" width="18"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path>
                                </svg>
                                <span class="label" data-translate-text="PLAYLIST_SUBSCRIBE">Subscribe</span>
                            </a>
                            <a class="btn share desktop" data-type="podcast" data-id="73330187">
                                <svg height="26" viewBox="0 0 24 24" width="14" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0h24v24H0z" fill="none"></path>
                                    <path
                                            d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z">
                                    </path>
                                </svg>
                                <span data-translate-text="SHARE">Share</span>
                            </a>
                        </div>
                    </div>
                    <div class="podcast-artwork-caption text-secondary desktop">
                        <div class="podcast-total-episodes">
                            895 Episodes
                        </div>
                        <a class="podcast-report d-flex align-items-center text-secondary" data-action="report"
                           data-type="podcast" data-id="73330187">
                            <span data-translate-text="REPORT">Report</span>
                        </a>
                    </div>
                </div>
                <div class="podcast-description">
                    Insight, analysis and expert debate as key policy makers are challenged on the latest news
                    stories.
                    From BBC Radio 4's Today programme
                </div>
            </div>
            <div id="audio-tree" class="pick-and-choose-playlist__song_section col-lg-8 col-12">
            </div>
        </div>
    </div>
@endsection