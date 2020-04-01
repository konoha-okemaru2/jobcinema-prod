@extends('layouts.employer_mypage_master')

@section('title', 'サブ動画２の登録 | JOB CiNEMA')
@section('description', '釧路の職場を上映する求人サイト')

@section('header')
  @component('components.employer.mypage_header')
  @endcomponent
@endsection

@section('contents')
<div class="main-wrap">
<section class="main-section job-create-section">
<div class="inner">
<div class="pad">

    <div class="col-md-10 mr-auto ml-auto">
        @if(count($errors) > 0)
            <div class="alert alert-danger">
            <strong><i class="fas fa-exclamation-circle"></i>エラー</strong><br>
                <ul class="list-unstyled">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(Session::has('message_success'))
            <div class="alert alert-success">
                {{ Session::get('message_success') }}
            </div>
        @endif
        @if(Session::has('message_danger'))
            <div class="alert alert-danger">
                {{ Session::get('message_danger') }}
            </div>
        @endif
        <form class="file-apload-form" action="@if($job){{route('sub.movie2.post', [$job->id])}}@else{{route('sub.movie2.post')}}@endif" method="POST" enctype="multipart/form-data">@csrf
            <div class="card">
                <div class="card-header">サブ動画の登録</div>
                <div class="card-body">
                        <p>ファイルを選択から登録したい動画を選んでください</p>
                        <div class="my-5">
                            <input name="job_sub_mov2" type="file" id="job_sub_mov2">
                        </div>
                        @if($job == '' && Session::has('movie_path_list.sub2'))
                        <p>現在登録されている動画</p>
                        <p class="pre-main-movie">
                        <p  oncontextmenu="return false;" class="pre-main-movie">
                            <video controls controlsList="nodownload" preload="none" playsinline>
                                <source src="{{Session::get('movie_path_list.sub2')}}"/></iframe>
                            </video>
                        </p>
                        @elseif($job != '' && Session::has('movie_path_list.sub2') && Session::get('movie_path_list.sub2') != '')
                        <p>現在登録されている動画</p>
                        <p  oncontextmenu="return false;" class="pre-main-movie">
                            <video controls controlsList="nodownload" preload="none" playsinline>
                                <source src="{{Session::get('edit_movie_path_list.sub2')}}"/></iframe>
                            </video>
                        </p>
                        @elseif($job != '' && Session::has('edit_movie_path_list.sub2') == false && $job->job_mov3 != null)
                        <p>現在登録されている動画</p>
                        <p  oncontextmenu="return false;" class="pre-main-movie">
                            <video controls controlsList="nodownload" preload="none" playsinline>
                                <source src="{{$job->job_mov3}}"/></iframe>
                            </video>
                        </p>
                        @endif
                        <ul class="list-unstyled">
                            <li>動画はMP4/MOV/WMV/MPEG/MPG/AVI形式に対応しています。</li>
                            <li>動画のサイズは480×310ピクセルです。
サイズの違う動画は自動的にサイズ調整されます。</li>
                            <li>登録できるファイルサイズは80MBまでです。</li>
                        </ul>
                </div>
            </div> <!-- card --> 
            <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary">登録する</button>
                    <a href="@if($job){{route('sub.movie2.delete', [$job->id])}}@else{{route('sub.movie2.delete')}}@endif" class="btn btn-secondary">登録された動画を削除</a>
                    <a class="create-image-back-btn" href="javascript:void(0);"　class="btn btn-outline-secondary"  onClick="window.opener.location.reload(),window.close()">戻る</a>
            </div>
        </form>
    </div>
            
</div>  <!-- pad -->
</div>  <!-- inner --> 
</section>
</div> <!-- main-wrap -->
@endsection

@section('footer')
  @component('components.employer.mypage_footer')
  @endcomponent
@endsection




