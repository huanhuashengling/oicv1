@extends('layouts.admin')

@section('content')
<div class="container">

<div class="card col-md-6">
  <div class="card-header bg-info">导入学生账户</div>
  <div class="card-body">

    <form method="POST" enctype="multipart/form-data">
      <div class="file-loading">
        <input type="file" class="form-control" name="student" id="import-student-account">
      </div>
    </form>
     <!-- Form::open(array('url'=>'school/importStudents','method'=>'POST','files'=>'true')) 
     Form::file('xls', ['id' => 'import-student-account', 'type'=>"file", 'class'=>"file-loading"]) 
     Form::close()  -->
  </div>
</div>
<!-- url school/updateStudentEmail   id update-student-email -->
<div class="card col-md-6">
  <div class="card-body">
    <img src="/img/oicstudentimport.png" width="400px">
  </div>
</div>
<div class="card col-md-12">
  <div class="card-header bg-info">管理学生账户</div>
  <div class="card-body">

  @foreach ($sclassesData as $sclass)
      <button class="btn btn-info sclass-btn" value="{{ $sclass['id'] }}">{{ $sclass['title'] }} <span class="badge">{{ $sclass['count'] }}</span></button>
  @endforeach

@foreach ($clubs as $club)
      <button class="btn btn-info club-btn" value="{{ $club->id }}">{{ $club->club_title }} <span class="badge">{{ $club->club_student_num }}</span></button>
  @endforeach

  </div>
</div>

<div class="card col-md-12">
  <div class="card-header bg-info">班级学生账户列表</div>
  <div class="card-body">
    <div id="toolbar">
        <button id="lock-btn" class="btn btn-danger">锁定</button>
        <button id="active-btn" class="btn btn-danger">激活</button>
        <button id="reset-pass-btn" class="btn btn-success">重置密码</button>
        <button id="add-new-btn" class="btn btn-success d-none">新增学生</button>
        <button id="bring-to-club" class="btn btn-warning d-none">加入社团</button>
    </div>
    <table id="student-list" class="table table-condensed table-responsive">
        <thead>
            <tr>
              <th data-field="" checkbox="true">
                  
              </th>
              <th data-field="">
                  序号
              </th> 
              <th data-field="class_title" data-formatter="classTitleCol">
                  班级
              </th>
              <th data-field="username">
                  姓名
              </th>
              <th data-field="gender" data-formatter="genderCol">
                  性别
              </th>
              <th data-field="work_max_num">
                  作品数上限
              </th>
              <th data-field="work_comment_enable" data-formatter="workCommentEnableCol">
                  发言状态
              </th>
              <!-- <th data-field="email">
                  邮箱
              </th> -->
              <th data-field="users_id" data-formatter="resetCol" data-events="resetActionEvents">
                  重置密码
              </th>
              <th data-field="studentsId" data-formatter="studentAccountActionCol" data-events="studentAccountActionEvents">
                  操作
              </th> 
            </tr>
        </thead>
    </table>
  </div>
</div>
</div>

<!-- Modal -->
<div class="modal fade" id="add-new-student-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">增加学生</h4>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label>学生姓名</label>
                <input type="text" class="form-control" name="studentName" id="student-name" required="">
              </div>
              <div class="form-group">
                <label>学生性别（1为男生／0为女生）</label>
                <input type="text" class="form-control" name="gender" id="gender" required="">
              </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-success" id="confirm-add-new-btn">增加</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="bring-into-club-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">加入社团</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>已选择学生名单：</label>
          <input type="hidden" name="studentIdList" id="student-id-list" val="">
          <p id="student-name-list"></p>
        </div>
        <div class="form-group">
          <label>选择社团</label>
          <select class="form-control" id="club-select">
            @foreach($clubs as $club)
              <option value="{{$club->id}}">{{$club->club_title}}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">关闭</button>
        <button type="button" class="btn btn-success" id="confirm-bring-into-club">点击加入</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
    <link href="/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
    <script src="/js/fileinput.min.js"></script>
    <script src="/js/locales/zh.js"></script>
    <script src="/js/school/student-account.js?v={{rand()}}"></script>
@endsection
