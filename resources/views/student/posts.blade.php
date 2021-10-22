@extends('layouts.student')

@section('content')
<div class="container">
    <div class="row">
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>新增失败</strong> 输入不符合要求<br><br>
            {!! implode('<br>', $errors->all()) !!}
        </div>
    @endif


    
<!--     <div id="posts-list">
        

    </div> -->
    <div id="toolbar">
        <select class="form-select" id="term-selection">
        @foreach ($terms as $key => $term)
            @php
                $class = $term->is_current?"selected":"";
            @endphp
            <option value="{{$term->id}}" {{$class}}>{{$term->grade_key}}年级{{$term->term_segment}}学期</option>
        @endforeach
        </select>
    </div>
    <table id="post-list" class="table table-condensed table-responsive">
        <thead>
            <tr>
                <th data-field="" checkbox="true">

                </th>
                <th data-field="lesson_title" data-sortable="true">
                    课题
                </th>
                <th data-field="postState" data-sortable="true">
                    状态
                </th>
                <th data-field="rateScore" data-sortable="true">
                    等第分
                </th>
                <th data-field="markNum" data-sortable="true">
                    点赞数
                </th>
                <th data-field="validMarkNum" data-sortable="true">
                    有效赞
                </th>
                <th data-field="score" data-sortable="true">
                    分数
                </th>
            </tr>
        </thead>
    </table>
    
</div>


@endsection

@section('scripts')
    <link href="/css/fileinput.css" media="all" rel="stylesheet" type="text/css" />
    <script src="/js/fileinput.min.js"></script>
    <script src="/js/locales/zh.js"></script>
    <script src="/js/student/posts.js?v={{rand()}}"></script>
@endsection