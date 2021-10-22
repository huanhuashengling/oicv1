@extends('layouts.teacher')
@include('markdown::encode',['editors'=>['test-editormd']])
@section('content')
<div class="container">
    <div class="row">
        <div class="col-10 offset-md-1">
            <div class="card card-default">
                <div class="card-header"><h4>修改课时信息</h4></div>
                <div class="card-body">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>新增失败</strong> 输入不符合要求<br><br>
                            {!! implode('<br>', $errors->all()) !!}
                        </div>
                    @endif

                    <form action="{{ url('teacher/lesson/'.$lesson->id) }}" method="POST">
                        {{ method_field('PATCH') }}
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <select name="courses_id" class="form-control">
                                <option>请选择课程</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->id }}. {{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="units_id" class="form-control">
                                <option>请先选择单元</option>
                                @foreach ($units as $unit)
                                    @if($lesson->units_id == $unit->id)
                                        <option value="{{ $unit->id }}" selected="selected">{{ $unit->id }}. {{ $unit->title }}</option>
                                    @else
                                        <option value="{{ $unit->id }}">{{ $unit->id }}. {{ $unit->title }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        课时标题： <input type="text" name="title" class="form-control" required="required" value="{{ $lesson->title }}" placeholder="请输入标题">
                        <br>
                        课时副标题：<input name="subtitle" rows="10" class="form-control" required="required" placeholder="请输入副标题" value="{{ $lesson->subtitle }}"</input>
                        <br>
                        <p>编写课堂帮助文档</p>
                        <div id="test-editormd">
                            <textarea name="test-editormd" style="display:none;">{{ $lesson->help_md_doc }}</textarea>
                        </div>
                        <br>
                        <button class="btn btn-success btn-lg pull-right">保存</button>
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