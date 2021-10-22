
@extends('layouts.teacher')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>添加课时</h4></div>
                <div class="panel-body">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>添加失败</strong> 输入不符合要求<br><br>
                            {!! implode('<br>', $errors->all()) !!}
                        </div>
                    @endif

                    <form action="{{ url('teacher/lesson') }}" method="POST">
                        {!! csrf_field() !!}
                        <div class="form-group">
                             <select name="courses_id" class="form-control">
                                <option>请选择课程</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $loop->index + 1 }}">{{ $loop->index + 1 }}. {{ $course }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="units_id" id="units-id">
                                <option>请先选择课程，再来选择单元</option>
                            </select>
                        </div>
                        <input type="text" name="title" class="form-control" required="required" placeholder="请输入课时标题">
                        <br>
                        <input type="text" name="subtitle" class="form-control" required="required" placeholder="请输入课时副标题" />
                        <br>
                        <p>编写课堂帮助文档</p>
                        <!-- <div class="editor"> 
                            <textarea class='form-control' id='myEditor'> content </textarea>
                        </div> -->
                        <div id="test-editormd">
                            <textarea name="test-editormd" style="display:none;"></textarea>
                        </div>
                        <br>
                        <button class="btn btn-success btn-lg pull-right">添加</button> 
                         <a class="btn btn-info btn-lg pull-right" href="javascript:window.history.back()">返回</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@include('markdown::encode',['editors'=>['test-editormd']])
@section('scripts')
    <script src="/js/teacher/lesson-edit.js?v={{rand()}}"></script>
@endsection