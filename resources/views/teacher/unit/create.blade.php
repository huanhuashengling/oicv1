
@extends('layouts.teacher')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-6 offset-md-3">
            <div class="card card-default">
                <div class="card-header">添加单元</div>
                <div class="card-body">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>添加失败</strong> 输入不符合要求<br><br>
                            {!! implode('<br>', $errors->all()) !!}
                        </div>
                    @endif
                    
                    <form action="{{ url('teacher/unit') }}" method="POST">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <select name="courses_id" class="form-control">
                                <option>请选择课程</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $loop->index + 1 }}">{{ $loop->index + 1 }}. {{ $course }}</option>
                                @endforeach
                            </select>
                        </div>
                        <br>
                        <input type="text" name="title" class="form-control" required="required" placeholder="请输入单元标题">
                        <br>
                        <input type="text" name="description" class="form-control" required="required" placeholder="请输入单元描述" />
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