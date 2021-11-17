@extends('layouts.student')

@section('content')

<div class="container">
  @if (0 < $unPostedLessonLogsNum)
      <div class="alert alert-danger">
        <div><small>你之前还有</small><strong>{{$unPostedLessonLogsNum}}</strong><small>节课没有提交作业，请记得</small><a href="/student/posts">点击这里</a><small>补交作业！</small></div>
      </div>
  @endif

  @if (0 < count($groupStudentsName))
    <div class="alert alert-success">
      <div>你所在的 <strong>{{$groupName}}</strong> 还有
      <?php foreach ($groupStudentsName as $key => $name): ?>
        {{$name}},
      <?php endforeach ?>
      请互相交流合作，互相帮助，完成课堂任务！</div>
    </div>
  @endif

  @if (is_null($lessonLog))
    <div class="jumbotron">
      <h1>别着急，还未开始上课!</h1>
      <p>你可以耐心等待或者尝试<a href="/student">刷新</a>一下页面，你也回顾<a href="/student/posts">以前交的作业</a>或者<a href="/student/classmate">其他同学的作业</a>。</p>
    </div>
  @else
    @if ($post)
      <input id="posted-path" value="/posts/yuying3/{{ $post->export_name }}" hidden />
    @endif
    <input type="hidden" id="lesson_logs_id" value="{{$lessonLog['id']}}">
    <div class="accordion" id="accordionExample">
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            课题：{{ $lesson['title'] }}<small>({{ $lesson['subtitle'] }})</small>
          </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
          <div class="accordion-body">
            <div class="markdown-body editormd-preview-container" previewcontainer="true" style="padding: 20px;">
              {!! $lesson['help_md_doc'] !!}
            </div>
            
          </div>
        </div>
      </div>
    </div>
    <div>
      @if(Session::has('success'))
        <div class="alert alert-success">
          <p>{!! Session::get('success') !!}</p>
        </div>
      @endif

      @if(Session::has('danger'))
        <div class="alert alert-danger">
          <p>{!! Session::get('danger') !!}</p>
        </div>
      @endif

      @if('sb3' == $lesson['allow_post_file_types'])
        <a class="btn btn-primary" href="/scratch3/index.html?code={{$JWTToken}}" target="_blank">打开在线编辑窗口</a>
      @else
        <div id="file-errors"></div>
        <div id="caption-info"></div>
        <form role="form" method='POST' files=true>
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="file" class="form-control" name="source" id="input-zh">
        </form>
      @endif
    </div>

  @endif
</div>
@endsection

@section('scripts')
    <link href="/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
    <script src="/js/fileinput.min.js"></script>
    <script src="/js/locales/zh.js"></script>
    <script src="/js/student/student-upload.js?v={{rand()}}"></script>
@endsection