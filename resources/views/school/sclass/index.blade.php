@extends('layouts.admin')

@section('content')
<div class="container">
  <div class="card col-md-12">
    <div class="card-header bg-info">学校班级列表</div>
    <div class="card-body">
      <div id="sclass-toolbar">
          <button id="add-new-sclass" class="btn btn-success">新增班级</button>
      </div>
      <table id="sclass-list" class="table table-condensed table-responsive">
          <thead>
              <tr>
                <th data-field="" checkbox="true">
                    
                </th>
                <th data-field="">
                    序号
                </th>
                <th data-field="id">
                    班级ID
                </th>
                <th data-field="enter_school_year">
                    入学年
                </th>
                <th data-field="class_num">
                    编号
                </th>
                <th data-field="class_title">
                    名称
                </th>
                <th data-field="is_graduated" data-formatter="graduatedCol">
                    毕业标志
                </th>
                <th data-field="id" data-formatter="sclassActionCol" data-events="classActionEvents">
                    操作
                </th> 
              </tr>
          </thead>
      </table>
    </div>
  </div>

<div class="card col-md-12">
  <div class="card-header bg-info">年级学期设置</div>
  <div class="card-body">
    <div id="term-toolbar">
        <button id="add-new-term" class="btn btn-success">新增学期</button>
    </div>
    <table id="term-list" class="table table-condensed table-responsive" >
        <thead>
            <tr>
              <th data-field="" checkbox="true">
                  
              </th>
              <th data-field="">
                  序号
              </th> 
              <th data-field="enter_school_year">
                  入学年
              </th>
              <th data-field="grade_key">
                  年级名称
              </th>
              <th data-field="term_segment">
                    学期名称
              </th>
              <th data-field="from_date">
                    开始时间
              </th>
              <th data-field="to_date">
                    结束时间
              </th>
              <th data-field="is_current" data-formatter="graduatedCol">
                    当前学期
              </th>
              <th data-field="id" data-formatter="sclassActionCol" data-events="classActionEvents">
                  操作
              </th> 
            </tr>
        </thead>
    </table>
  </div>
</div>

<div class="card col-md-12">
  <div class="card-header bg-info">社团列表</div>
  <div class="card-body">
    <div id="club-toolbar">
        <button id="add-new-club" class="btn btn-success">新增社团</button>
    </div>
    <table id="club-list" class="table table-condensed table-responsive">
        <thead>
            <tr>
              <th data-field="" checkbox="true">
                  
              </th>
              <th data-field="">
                  序号
              </th>
              <th data-field="id">
                  社团ID
              </th>
              <th data-field="club_title">
                  社团标题
              </th>
              <th data-field="status">
                  社团状态
              </th>
              <th data-field="term_desc">
                  所属学期
              </th>
              <th data-field="id" data-formatter="clubActionCol" data-events="clubActionEvents">
                  操作
              </th> 
            </tr>
        </thead>
    </table>
  </div>
</div>
</div>


<!-- Modal -->
<div class="modal fade" id="add-new-sclass-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">增加班级</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <input type="hidden" name="schoolsId" id="schools-id" value="{{ $schoolsId}}">
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>入学年</label>
          <input type="text" class="form-control" name="enterSchoolYear" id="enter-school-year" required="">
        </div>
        <div class="form-group">
          <label>编号</label>
          <input type="text" class="form-control" name="classNum" id="class-num" required="">
        </div>
        <div class="form-group">
          <label>名称</label>
          <input type="text" class="form-control" name="classTitle" id="class-title" required="">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-success" id="confirm-add-new-btn">增加</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="add-new-term-modal" tabindex="-1" role="dialog" aria-labelledby="termModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <input type="hidden" name="schoolsId" id="schools-id" value="{{ $schoolsId}}">
                <h4 class="modal-title" id="termModalLabel">增加学期</h4>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label>入学年</label>
                <input type="text" class="form-control" name="termEnterSchoolYear" id="term-enter-school-year" required="">
              </div>
              <div class="form-group">
                <label>年级名称</label>
                <input type="text" class="form-control" name="gradeKey" id="grade-key" required="">
              </div>
              <div class="form-group">
                <label>学期名称</label>
                <input type="text" class="form-control" name="termSegment" id="term-segment" required="">
              </div>
              <div class="form-group">
                <label>开始时间</label>
                <input type="text" class="form-control" name="startDate" id="start-date" required="">
              </div>
              <div class="form-group">
                <label>结束时间</label>
                <input type="text" class="form-control" name="endDate" id="end-date" required="">
              </div>
<!--               <div class="form-group">
                <label>当前学期</label>
                <input type="text" class="form-control" name="isCurrent" id="is-current" required="">
              </div> -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-success" id="confirm-add-new-term">增加</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="add-new-club-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">增加社团</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <input type="hidden" name="schoolsId" id="schools-id" value="{{ $schoolsId}}">
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>社团标题</label>
          <input type="text" class="form-control" name="clubTitle" id="club-title" required="">
        </div>
        <div class="form-group">
          <label>所属学期</label>
          <input type="text" class="form-control" name="termDesc" id="term-desc" required="">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-success" id="confirm-add-new-club">增加</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
    <link href="/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
    <script src="/js/fileinput.min.js"></script>
    <script src="/js/locales/zh.js"></script>
    <script src="/js/school/sclass.js?v={{rand()}}"></script>
@endsection
