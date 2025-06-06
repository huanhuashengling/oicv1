<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ trans("layouts.title") }}</title>
    <link rel="icon" href="/img/oic.ico" type="image/x-icon" />
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/bootstrap-icons.css">

    <link href="/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
    <link href="/css/bootstrap-table.min.css" media="all" rel="stylesheet" type="text/css" />
    <link href="/css/bootstrap-table-fixed-columns.min.css" media="all" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="/css/editormd.preview.min.css" />
    <link href="/css/jquery-ui.css" rel="stylesheet">

    <script src="/js/jquery.min.js"></script>
    <script src="/js/popper.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/respond.min.js"></script>
    <script src="/js/plugins/canvas-to-blob.min.js" type="text/javascript"></script>
    <script src="/js/plugins/sortable.min.js" type="text/javascript"></script>
    <script src="/js/plugins/purify.min.js" type="text/javascript"></script>
    <script src="/js/fileinput.min.js"></script>
    <script src="/js/bootstrap-table.min.js"></script>
    <script src="/js/bootstrap-table-fixed-columns.min.js"></script>

    <script src="/js/locales/zh.js"></script>
    
        <!--for scratch3-->
    <link href="/css/scratch.css" rel="stylesheet">
    <!-- <link href="/scratch3/css/player.css" rel="stylesheet"> -->

    <script src="/js/common.js"></script>
    
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" rel="stylesheet" /> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js"></script> -->

        <style type="text/css">

      .fill-red-color {
        color: #ff0000;
      }

      .fill-mid-red-color {
        color: #ff6781;
      }
    </style>
</head>
<body id="app-layout">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" aria-label="Eighth navbar example" style="margin-bottom: 20px;">
    <div class="container">
      <a class="navbar-brand" href="/">{{ trans("layouts.project_name") }}</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample07" aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExample07">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link  {{ request()->segment(2) === '' ? 'active' : null }}" aria-current="page" href="/teacher">选课上课</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ (request()->segment(2) === 'course' || request()->segment(2) === 'unit' || request()->segment(2) === 'lesson') ? 'active' : null }}" href="/teacher/course">课程管理</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->segment(2) === 'lessonLog' ? 'active' : null }}" href="/teacher/lessonLog" tabindex="-1">上课记录</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->segment(2) === 'scoreReport' ? 'active' : null }}" href="/teacher/scoreReport" tabindex="-1">成绩报告</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->segment(2) === 'detailReport' ? 'active' : null }}" href="/teacher/detailReport" tabindex="-1">详细报告</a>
          </li>
        </ul>
      
          <ul class="nav navbar-nav navbar-right">
          @if (Auth::guard("teacher")->guest())
            <li><a href="{{ url('/login') }}">{{ trans("layouts.login") }}</a></li>
            <!-- <li><a href="{{ url('/register') }}">{{ trans("layouts.register") }}</a></li> -->
            @else
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="dropdown07" data-bs-toggle="dropdown" aria-expanded="false">你好，{{ Auth::guard("teacher")->user()->username }} <span class="caret"></span></a>
                <ul class="dropdown-menu" aria-labelledby="dropdown07">
                  <li><a class="dropdown-item" href="{{ url('/teacher/reset') }}"><i class="bi bi-info-circle"></i> 个人信息</a></li>
                  <li><a class="dropdown-item" href="{{ url('/teacher/reset') }}"><i class="bi bi-gear"></i> 修改密码</a></li>
                  <li><a class="dropdown-item" href="{{ url('/teacher/logout') }}"><i class="bi bi-arrow-left-circle"></i> {{ trans("layouts.logout") }}</a></li>
                </ul>
              </li>

              @endif
          </ul>

      </div>
    </div>
  </nav>

    @yield('content')
    @yield('scripts')
</body>
</html>