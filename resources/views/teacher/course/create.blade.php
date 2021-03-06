
@extends('layouts.teacher')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="card card-default">
                <div class="card-header"><h4>添加课程</h4></div>
                <div class="card-body">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>添加失败</strong> 输入不符合要求<br><br>
                            {!! implode('<br>', $errors->all()) !!}
                        </div>
                    @endif

                    <form action="{{ url('teacher/course') }}" method="POST">
                        {!! csrf_field() !!}
                        <input type="text" name="title" class="form-control" required="required" placeholder="请输入标题">
                        <br>
                        <input type="text" name="description" class="form-control" required="required" placeholder="请输入课程描述" />
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