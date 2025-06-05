@extends('layouts.teacher')

@section('content')
<div class="container">
    <input type="hidden" id="requestUrl">
    <input type="hidden" id="selected_sclass_id">
    <div class="row g-3">
        <div class="form-group col-md-4">
            <select name="detail_report_sclasses_id" class='form-control'>
                <option>请选择班级</option>
                @foreach ($sclasses as $sclass)
                <option value="{{$sclass->id}}">{{$sclass['enter_school_year']}}级{{$sclass['class_title']}}班</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group col-md-4">
            <select name="detail_report_terms_id" class='form-control'>
                <option>请先选择班级</option>
            </select>
        </div>
    </div>
    <div id="toolbar">
    </div>
    <table id="detail-report" class="table table-condensed table-responsive">
        <thead>
            <tr>
                <th data-field="" checkbox="true">

                </th>
                <th data-field="">
                    序号
                </th>
                <th data-field="username" data-sortable="true">
                    学生姓名
                </th>
            </tr>
        </thead>
    </table>
    
</div>
@endsection

@section('scripts')
    <script src="/js/teacher/detail-report.js?v={{rand()}}"></script>

    <script src="/js/FileSaver.js"></script>
    <script src="/js/bootstrap-table-export.js"></script>
    <script src="/js/tableexport.js"></script>
@endsection