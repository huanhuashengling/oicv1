@extends('layouts.teacher')

@section('content')
<div class="container">
    <div class="row g-3">
        <div class="form-group col-md-4">
            <select name="score_report_sclasses_id" class='form-control'>
                <option>请选择班级</option>
                @foreach ($sclasses as $sclass)
                <option value="{{$sclass->id}}">{{$sclass['enter_school_year']}}级{{$sclass['class_title']}}班</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-4">
            <select name="score_report_terms_id" class='form-control'>
                <option>请先选择班级</option>
            </select>
        </div>
    </div>
    <div id="toolbar">
        <button class="btn btn-success" id="export-score-report-btn">导出成绩</button>
        <!-- <button class="btn btn-success" id="email-all-out-btn">发送所有邮件</button> -->
    </div>
    <table id="score-report" class="table table-condensed table-responsive">
        <thead>
            <tr>
                <th data-field="" checkbox="true">

                </th>
                <th data-field="">
                    序号
                </th>
                <th data-field="users_id" data-sortable="true">
                    ID
                </th>
                <th data-field="username" data-sortable="true">
                    学生姓名
                </th>
                <th data-field="order_num" data-sortable="true">
                    组号
                </th>
                <th data-field="postedNum" data-sortable="true">
                    已交
                </th>
                <th data-field="rateYouJiaNum" data-sortable="true">
                    优+
                </th>
                <th data-field="rateYouNum" data-sortable="true">
                    优
                </th>
                <th data-field="rateDaiWanNum" data-sortable="true">
                    待完善
                </th>
                <th data-field="unPostedNum" data-sortable="true">
                    未交
                </th>
                <th data-field="markNum" data-sortable="true">
                    点赞
                </th>
                <th data-field="effectMarkNum" data-sortable="true">
                    有效赞
                </th>
                <th data-field="commentNum" data-sortable="true">
                    评论
                </th>
                <th data-field="scoreCount" data-sortable="true">
                    分数合计
                </th>
                <th data-field="reportText" data-visible="false">
                  学期分数报告
              </th>
              <th data-field="users_id" data-formatter="emailCol" data-events="emailActionEvents" data-visible="false">
                  发送
              </th>
            </tr>
        </thead>
    </table>
    
</div>
@endsection

@section('scripts')
    <script src="/js/teacher/score-report.js?v={{rand()}}"></script>

    <script src="/js/FileSaver.js"></script>
    <script src="/js/bootstrap-table-export.js"></script>
    <script src="/js/tableexport.js"></script>
@endsection