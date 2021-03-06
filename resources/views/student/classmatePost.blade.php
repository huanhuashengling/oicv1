@extends('layouts.student')

@section('content')

<div class="container" style="padding-left: 0px; padding-right: 0px">
    <!-- <button class="btn btn-success" id="reload-btn">点我加载最新作业</button> -->
     <div class="row" style="margin-bottom: 10px">
        <div class="col-8">
            <!-- <button class="btn btn-info col" id="current-lesson-log-btn">本节课的</button> -->
            <button class="btn btn-info col" id="my-posts-btn">我的</button>
            <button class="btn btn-info" id="same-sclass-posts-btn">同班级</button>
            <!-- <button class="btn btn-info" id="same-grade-posts-btn">同年级</button> -->
            <!-- <button class="btn btn-info" id="same-grade-posts-btn">随机</button> -->
            <!-- <button class="btn btn-info" id="all-posts-btn">全部</button> -->
        </div>
        <div class="col-2">
            <input type="text" name="" id="search-name" class="form-control col" placeholder="姓名">
        </div>
        <div class="col-2">
            <button class="btn btn-info col" id="name-search-btn">搜索</button>
        </div>
    </div>
    <div class="row row-cols-1 row-cols-md-4 g-2" id="posts-list">
    @foreach(@$posts as $key=>$post)
        <div class="col">
            <div class="card h-100">
                @if("sb3" == $post->file_ext)
                <a href="/student/sb3player?postCode={{$post->post_code}}&schoolCode={{$schoolCode}}" style="padding: 5px;"><img src="/posts/{{$schoolCode}}/{{$post->post_code}}_c.{{$post->cover_ext}}" value="{{ $post['pid'] }}" class="img-fluid" alt="Card image cap"></a>
                @else
                <a href="/student/imgPreview?postCode={{$post->post_code}}&schoolCode={{$schoolCode}}" style="padding: 5px;"><img src="/posts/{{$schoolCode}}/{{$post->post_code}}_c.{{$post->cover_ext}}" class="img-fluid" value="{{ $post['pid'] }}" alt="Card image cap"></a>
                @endif
                <div class="card-footer">
                    <div class="row" style="font-size: 0.8em;">
                        <div class="col">作品名称：{{ $post->lesson_title }}</div>
                    </div>
                    <div class="row" style="font-size: 0.7em;">
                        <div class="col">创作者：{{ $post->grade_key }}{{ $post->class_title }} {{ $post->username }}</div>
                    </div>
                </div>
                <input type="hidden" name="postInfo" value="{{ $post->studentClass }}班">
            </div>
        </div>
    @endforeach
    @if (count($posts) > 0) 
        {{ $posts->appends(request()->input())->render() }}
    @endif
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="classmate-post-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <input type="hidden" id="posts-id" value="">
    <input type="hidden" id="mark-num" value="">
    <input type="hidden" id="is-init" value="true">

  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="classmate-post-modal-label"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="height: 650px">
        <!-- <img data-magnify="gallery" data-src="big-1.jpg" src="small-1.jpg" id='classmate-post-show'> -->
        <!-- <iframe src="/scratch3/player.html" width="100%" id="sb3iFrame" height="100%"></iframe> -->
      </div>
      <div class="modal-footer">
        <div style="float: left;" id="space-link">
            <a class="btn btn-success" href="/space?sId=" target="_blank">访问他的个人空间</a>
        </div>
        <div class="switch" id="switch-box">
            <input type="checkbox" id="like-check-box" name="likeCheckBox"/>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="classmatePostModalLabel">
<input type="hidden" id="posts-id" value="">
<input type="hidden" id="mark-num" value="">
<input type="hidden" id="is-init" value="true">

  <div class="modal-dialog modal-lg" role="document" style="height: 90%">
    <div class="modal-content" style="height: 100%">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id=""></h4>
      </div>
      <div class="modal-body">
        <!-- <div id='doc-preview'></div> -->
        <!-- <img src="" id='classmate-post-show' class="img-responsive img-thumbnail center-block"> -->
        <!-- <a data-magnify="gallery" href="" id="magnify-href">
          <img src="" id='classmate-post-show' class="img-responsive img-thumbnail center-block">
        </a> -->
        <img data-magnify="gallery" data-src="big-1.jpg" src="small-1.jpg" id='classmate-post-show'>
        <!-- <div id='flashContent'> -->
            <!-- Get <a href="http://www.adobe.com/go/getflash">Adobe Flash Player</a>, otherwise this Scratch movie will not play. -->
        <!-- </div> -->
        <!-- <div id="vr-area">
            <a-scene embedded id="360-sence">
              <a-sky id="image-360"></a-sky>
            </a-scene>
        </div> -->
        <!-- <a href="" id="classmate-post-download-link">右键点击下载</a> -->
      </div>

    <div class="modal-footer">
        <div style="float: left;" id="space-link">
            <a class="btn btn-success" href="/space?sId=" target="_blank">访问他的个人空间</a>
        </div>
        <div class="switch" id="switch-box">
            <input type="checkbox" id="like-check-box" name="likeCheckBox"/>
        </div>
    </div>
  </div>
</div>
</div>

@endsection

@section('scripts')
    <!-- <script type="text/javascript" src="/scratch/swfobject.js"></script> -->
    <link href="/css/jquery.magnify.min.css" rel="stylesheet">
    <link href="/css/magnify-bezelless-theme.css" rel="stylesheet">

    <script src="/js/jquery.magnify.min.js"></script>
    <link href="/css/bootstrap-switch.css" rel="stylesheet">
    <script src="/js/bootstrap-switch.min.js"></script>
    <script src="/js/student/classmate-post.js?v={{rand()}}"></script>
    <style type="text/css">
    img
        {
            image-rendering: optimizeSpeed;
            image-rendering: -moz-crisp-edges; /* Firefox */
            image-rendering: -o-crisp-edges; /* Opera */
            image-rendering: -webkit-optimize-contrast; /* Webkit (non-standard naming) */
            image-rendering: pixelated;
            -ms-interpolation-mode: nearest-neighbor; /* IE (non-standard property) */
   </style>
@endsection