@extends('layouts.student')

@section('content')

@php
    $rateSvg = "star-fill-red4.svg";
    if (isset($student->rate)) {
        $rateSvg = "star-fill-red" . $student->rate . ".svg";
    }
    $markSvg = "heart-fill-red4.svg";
    if (isset($student->mark_num)) {
        if($student->mark_num < 4) {
            $markSvg = "heart-fill-red3.svg";
        } else if ($student->mark_num < 10) {
            $markSvg = "heart-fill-red2.svg";
        } else {
            $markSvg = "heart-fill-red1.svg";
        }
    }
@endphp

<div class="container">
  <div class="row">
    <div class="col-8">
      <h5 id="lesson-title"></h5>
      <p id="student-name"></p>
    </div>
    <div class="col-4 text-center">
      <!-- <a class="btn btn-primary" href="#"><i class="bi bi-arrow-repeat"></i> 查看内部</a> -->
    </div>

  </div>

  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body">
          <div id="scratch"></div>
        </div>
        <div class="card-footer">
          <div class="row">
            <div class="col">
              <img src="" id="post-rate-icon"> <span id="post-rate"></span>
            </div>
            <div class="col">
              <button class="btn" id="heart-btn" isMarkedByMyself=""><img src="" id="heart-icon"> <span id="heart-num"></span></button>
            </div>
            <div class="col">
              <i class="bi bi-cloud-upload"></i><small><span id="date-span"></span></small>
            </div>
          </div>
          
        </div>
      </div>
    </div>
    <div class="col">
      <div class="card">
        <div class="card-header">
          作品介绍
        </div>
        <div class="card-body">
          <p class="card-text"></p>
        </div>
      </div>
      <p>
      <div class="card">
        <div class="card-header">
          教师评语
        </div>
        <div class="card-body">
          <p class="card-text"></p>
        </div>
      </div>
      <p>
      <div class="card">
        <div class="card-header">
          点赞名单
        </div>
        <div class="card-body">
          <p class="card-text" id="mark_names"></p>
        </div>
      </div>
    </div>
  </div>
  
</div>
@endsection

@section('scripts')

  <script>
    var workId = urlParams('workId')
    var workUrl = urlParams('workUrl')
    var postCode = urlParams('postCode');
    var postUrl = "/posts/yuying3/" + urlParams('postCode') + ".sb3";

    $(document).ready(function() {

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        type: "POST",
        url: '/student/getOnePostByCode',
        data: {post_code: postCode},
        success: function( data ) {
          console.log(data);
          $("#lesson-title").text("课题：" + data.title);
          $("#date-span").text(" " + data.updated_at);
          $("#student-name").text("创作者：" + data.username);
          $("#heart-num").text(data.markNum);
          $("#mark_names").text(data.markNames);
          
          if ("true" == data.isMarkedByMyself) {
            $("#heart-icon").prop("src", "/img/heart-fill-red2.svg");
            $("#heart-btn").attr("isMarkedByMyself", "true");
          } else {
            $("#heart-icon").prop("src", "/img/heart-fill-red4.svg");
            $("#heart-btn").attr("isMarkedByMyself", "false");
          }
          
          if (1 == data.postRate) {
            $("#post-rate").text("优+");
            $("#post-rate-icon").prop("src", "/img/star-fill-red" + data.postRate + ".svg");
          } else if (2 == data.postRate) {
            $("#post-rate").text("优");
            $("#post-rate-icon").prop("src", "/img/star-fill-red" + data.postRate + ".svg");
          } else if (3 == data.postRate) {
            $("#post-rate").text("合格");
            $("#post-rate-icon").prop("src", "/img/star-fill-red" + data.postRate + ".svg");
          }  else {
            $("#post-rate").text("");
            $("#post-rate-icon").prop("src", "/img/star-fill-red4.svg");
          }
        }
      });
    });

    $("#heart-btn").click(function(e){
      if ("false" == $("#heart-btn").attr("isMarkedByMyself")) {
        var currentMarkNum = parseInt($("#heart-num").text());
        console.log(currentMarkNum)
        $.ajax({
            type: "POST",
            url: '/student/updateMarkByCode',
            data: {postCode: postCode, stateCode: 1},
            success: function( returnData ) {
              console.log(returnData)
                if ("false" == returnData) {

                } else {
                  $("#heart-icon").prop("src", "/img/heart-fill-red2.svg");
                  $("#heart-num").text(currentMarkNum + 1);
                  $("#heart-btn").attr("isMarkedByMyself", "true");
                }
            }
        });
      } else {
        var currentMarkNum = parseInt($("#heart-num").text());
        console.log(currentMarkNum)
        $.ajax({
            type: "POST",
            url: '/student/updateMarkByCode',
            data: {postCode: postCode, stateCode: 0},
            success: function( returnData ) {
              console.log(returnData)
                if ("false" == returnData) {

                } else {
                  $("#heart-icon").prop("src", "/img/heart-fill-red4.svg");
                  $("#heart-num").text(currentMarkNum - 1);
                  $("#heart-btn").attr("isMarkedByMyself", "false");
                }
            }
        });
      }

    });

    window.scratchConfig = {
        stageArea: {
        scale: 1,
        width: 480,
        height: 360,
        showControl: true,
        showLoading: false,
        fullscreenButton:{ //全屏按钮
          show: true,
          handleBeforeSetStageUnFull(){ //退出全屏前的操作
            return true
          },
          handleBeforeSetStageFull(){ //全屏前的操作
            return true
          }
        },
        startButton:{ //开始按钮
          show: true,
          handleBeforeStart(){ //开始前的操作
            return true
          }
        },
        stopButton:{ // 停止按钮
          show: true,
          handleBeforeStop(){ //停止前的操作
            return true
          }
        }
      },
      handleVmInitialized: (vm) => {
        window.vm = vm 
      },
      handleDefaultProjectLoaded: () => {
        if(postUrl){
          window.scratch.loadProject(postUrl, () => {
            vm.runtime.start()
          })
        }

        // if(workId){
        //   getWorkInfo(workId, function (info) {
        //     window.scratch.loadProject(info.workFileUrl, () => {
        //       vm.runtime.start()
        //     })
        //   })
        // }
      }
    }
  </script>
  <script type="text/javascript" src="/scratch3/assets_lib.min.js"></script>
  <script type="text/javascript" src="/scratch3/chunks/player.js"></script>
@endsection