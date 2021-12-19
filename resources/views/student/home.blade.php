@extends('layouts.student')

@section('content')
<style type="text/css">
  /*.markdown-body.editormd-preview-container p img {
    width:900px;
  }*/
</style>
<div class="container">
      <div class="card bg-info">
        <div class="card-body">点击下拉框，可切换课，进行作业补交 </div>
      </div>

  @if (0 < count($groupStudentsName))
    <div class="alert alert-success">
      <div>你所在的 <strong>{{$groupName}}</strong> 还有
      <?php foreach ($groupStudentsName as $key => $name): ?>
        {{$name}},
      <?php endforeach ?>
      请互相交流合作，互相帮助，完成课堂任务！</div>
    </div>
  @endif
    <select class="form-select mb-3" id="lesson-select">
      @foreach ($allLessonData as $lessonData)
        <option {{$lessonData->selected}} value={{$lessonData->lessons_id}} lessonlogsid={{$lessonData->lesson_logs_id}}>{{$lessonData->curr_str}}第{{$lessonData->order}}课：{{$lessonData->title}}({{$lessonData->subtitle}}) --------{{$lessonData->finished_status}}</option>
      @endforeach
    </select>

      <div class="card">
          <div class="card-body">
            <div class="markdown-body editormd-preview-container" previewcontainer="true" style="padding: 20px;" id="md-content">
            </div>
          </div>
      </div>
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
      <div class="card" style="margin-top: 20px">
        <div class="card-header bg-primary text-white text-center">作业提交区</div>
        <div class="card-body">
              <div id="sb3-area" class="d-none">
                <a id="sb3editor-atag" class="btn btn-primary" href="" target="_blank">打开在线编辑窗口</a>
              </div>
              <div id="img-area" class="d-none">
                <div id="file-errors"></div>
                <div id="caption-info"></div>
                <form role="form" method='POST' files=true>
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="file" class="form-control" name="source" id="input-zh">
                </form>
              </div>
        </div> 
      </div>
    </div>
</div>
@endsection

@section('scripts')
    <link href="/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
    <script src="/js/fileinput.min.js"></script>
    <script src="/js/locales/zh.js"></script>
    <script src="/js/student/student-upload.js?v={{rand()}}"></script>
@endsection