@extends('layouts.teacher')

@section('content')
<div class="container">
    {!! Breadcrumbs::render('tunit', $unit) !!}
    <div id="toolbar">
        <button class="btn btn-success" id="add-lesson-btn">新增课</button>
        <input type="hidden" name="" id="units-id" value="{{$uId}}">
    </div>
    <table id="lesson-list" class="table table-condensed table-responsive">
        <thead>
            <tr>
                <th data-field="" checkbox="true">

                </th>
                <th data-field="">
                    序号
                </th>
                <!-- <th data-field="course_title" data-sortable="true">
                    所属课程
                </th>
                <th data-field="unit_title" data-sortable="true">
                    所属单元
                </th> -->
                <th data-field="title" data-sortable="true">
                    标题
                </th>
                <th data-field="subtitle" data-sortable="true" data-formatter="subtitleCol">
                    副标题
                </th>
                <th data-field="is_open" data-sortable="true" data-formatter="isOpenCol">
                    开放
                </th>
                <th data-field="username" data-sortable="true">
                    创建者
                </th>
                <th data-field="lesson_log_num" data-sortable="true">
                    上课次数
                </th>
                <th data-field="updated_at" data-sortable="true">
                    创建时间
                </th>
                <th data-field="lessonsId" data-formatter="actionCol" data-events="actionEvents">
                  操作
                </th>
            </tr>
        </thead>
    </table>


    <!-- Modal -->
    <div class="modal fade" id="lesson-detail-modal" tabindex="-1" aria-labelledby="lesson-detail-modal" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="lesson-detail-title">查看课程内容</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="lesson-detail-help-md-doc">
            <div class="markdown-body editormd-preview-container" previewcontainer="true" style="padding: 20px;">
            <div id="doc-content">
                <textarea style="display:none;">
                </textarea>
            </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
          </div>
        </div>
      </div>
    </div>

</div>
@endsection

@section('scripts')
    <script src="/js/teacher/lesson.js?v={{rand()}}"></script>
@endsection