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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
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
        width: 850px;
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
            <a class="nav-link {{ request()->segment(2) === 'classmate' ? 'active' : null }}" href="{{ url('/student/classmate') }}" tabindex="-1">作业墙</a>
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
    <footer class="footer mt-auto py-3 bg-light">
      <div class="container">
          <div class="flex justify-center mt-4 sm:items-center sm:justify-between">
              <div class="text-center text-sm text-gray-500 sm:text-left">
                  <div class="flex">
                      <svg fill="none" width="16" height="16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor" class="-mt-px w-5 h-5 text-gray-400">
                          <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                      </svg>

                      <a href="mailto:shengling_2005@163.com" class="ml-1 underline">
                          技术支持
                      </a>

                      <svg fill="none" width="16" height="16" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="ml-4 -mt-px w-5 h-5 text-gray-400">
                          <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                      </svg>

                      <a href="" class="ml-1 underline">
                          幻化生灵 All Rights Reserved
                      </a>
                  </div>
              </div>
          </div>
      </div>
    </footer>
</body>
</html>