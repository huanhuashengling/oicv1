@extends('layouts.teacher')

@section('content')

<div class="container">
  <div class="row">
    <div class="col-8">
      <h5 id="lesson-title"></h5>
      <p id="student-name"></p>
    </div>
    <div class="col-4 text-center">
      <a class="btn btn-primary" href="#"><i class="bi bi-arrow-repeat"></i> 查看内部</a>
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
              <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                <input type="radio" class="btn-check" name="rateradio" id="rateradio1" autocomplete="off">
                <label class="btn btn-outline-primary" for="rateradio1">优+</label>
                <input type="radio" class="btn-check" name="rateradio" id="rateradio2" autocomplete="off">
                <label class="btn btn-outline-primary" for="rateradio2">优</label>
                <input type="radio" class="btn-check" name="rateradio" id="rateradio3" autocomplete="off">
                <label class="btn btn-outline-primary" for="rateradio3">合格</label>
              </div>
            </div>
            <div class="col">
              <img src="/img/heart-fill-red2.svg"></i> <span id="heart-num"></span>
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
          <p class="card-text">这是我创作的第一个作品，请大家多多点赞！</p>
        </div>
      </div>
      <p>
      <div class="card">
        <div class="card-header">
          教师评语
        </div>
        <div class="card-body">
          <p class="card-text">作品完成度不错，继续加油！</p>
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
    var postUrl = "/posts/ys/" + urlParams('postCode') + ".sb3";
    // alert(postUrl);
    $(document).ready(function() {

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        type: "POST",
        url: '/teacher/getOnePostByCode',
        data: {post_code: postCode},
        success: function( data ) {
          console.log(data);
          $("#lesson-title").text("课题：" + data.title);
          $("#date-span").text(" " + data.updated_at);
          $("#student-name").text("创作者：" + data.username);
          $("#heart-num").text(data.markNum);
          
          if ("true" == data.isMarkedByMyself) {
            $("#heart-icon").addClass(" bi-heart-fill fill-red-color")
            $("#heart-icon").removeClass(" bi-heart")
          } else {
            $("#heart-icon").addClass(" bi-heart")
            $("#heart-icon").removeClass(" bi-heart-fill fill-red-color")
          }

          if (1 == data.postRate) {
            $("#rateradio1").prop('checked', true)
          } else if (2 == data.postRate) {
            $("#rateradio2").prop('checked', true)
          } else if (3 == data.postRate) {
            $("#rateradio3").prop('checked', true)
          }
        }
      });
    });

    $("[name='rateradio']").change(function(e){
      // alert(e.target.id)
      var rate = 4;
      if("rateradio1" == e.target.id) {
        rate = 1;
      } else if("rateradio2" == e.target.id) {
        rate = 2;
      } if("rateradio3" == e.target.id) {
        rate = 3;
      } 
      $.ajax({
            type: "POST",
            url: '/teacher/updateRateByCode',
            data: {post_code: postCode, rate: rate},
            success: function( returnData ) {
              console.log(returnData)
                if ("false" == returnData) {

                } else {
                  
                }
            }
        });
    })

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