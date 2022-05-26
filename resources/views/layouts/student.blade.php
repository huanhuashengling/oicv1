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
<link rel="stylesheet" href="/css/editormd.preview.min.css" />
    <link href="/css/jquery-ui.css" rel="stylesheet">
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/respond.min.js"></script>

    <script src="/js/plugins/canvas-to-blob.min.js" type="text/javascript"></script>
    <script src="/js/plugins/sortable.min.js" type="text/javascript"></script>
    <script src="/js/plugins/purify.min.js" type="text/javascript"></script>
    <script src="/js/fileinput.min.js"></script>

    <script src="/js/bootstrap-table.min.js"></script>

    <script src="/js/locales/zh.js"></script>
    
    <!--for scratch3-->
    <link href="/css/scratch.css" rel="stylesheet">
    <!-- <link href="/scratch3/css/player.css" rel="stylesheet"> -->

    <script src="/js/common.js"></script>
    
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" rel="stylesheet" /> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js"></script> -->
    <style type="text/css">
      .container {
        width: 1000px;
      }

      .fill-red-color {
        color: #ff0000;
      }

      .fill-mid-red-color {
        color: #ff6781;
      }
    </style>
    
</head>
<body id="app-layout" style="height: 100%;">
  <nav class="navbar navbar-expand-lg navbar-light" aria-label="Eighth navbar example" style="margin-bottom: 20px; background-color: #e3f2fd;">
    <div class="container">
      <a class="navbar-brand" href="/">{{ trans("layouts.project_name") }}</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample07" aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExample07">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link  {{ request()->segment(2) === '' ? 'active' : null }}" aria-current="page" href="{{ url('/student/home') }}">信息课</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->segment(2) === 'posts' ? 'active' : null }}" href="{{ url('/student/posts') }}">作业记录</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->segment(2) === 'classmate' ? 'active' : null }}" href="{{ url('/student/classmate?type=same-sclass') }}" tabindex="-1">作业墙</a>
          </li>
          <!-- <li class="nav-item">
            <a class="nav-link {{ request()->segment(2) === 'work' ? 'active' : null }}" href="{{ url('/student/work') }}" tabindex="-1">个人主页</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->segment(2) === 'open-classroom' ? 'active' : null }}" href="{{ url('/student/open-classroom') }}" tabindex="-1">开放课堂</a>
          </li> -->
        </ul>
          <ul class="nav navbar-nav navbar-right">
          @if (Auth::guard("student")->guest())
            <li><a href="{{ url('/login') }}">{{ trans("layouts.login") }}</a></li>
            <!-- <li><a href="{{ url('/register') }}">{{ trans("layouts.register") }}</a></li> -->
            @else
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="dropdown07" data-bs-toggle="dropdown" aria-expanded="false">你好，{{ Auth::guard("student")->user()->username }} <span class="caret"></span></a>
                <ul class="dropdown-menu" aria-labelledby="dropdown07">
                  <li><a class="dropdown-item" href="{{ url('/student/info') }}"><i class="bi bi-info-circle"></i> 个人信息</a></li>
                  <li><a class="dropdown-item" href="{{ url('/student/reset') }}"><i class="bi bi-gear"></i> 修改密码</a></li>
                  <li><a class="dropdown-item" href="{{ url('/student/logout') }}"><i class="bi bi-arrow-left-circle"></i> {{ trans("layouts.logout") }}</a></li>
                </ul>
              </li>
              @endif
          </ul>

      </div>
    </div>
  </nav>

    @yield('content')
    @yield('scripts')
    <br>
    <br>
    <br>

</body>
</html>