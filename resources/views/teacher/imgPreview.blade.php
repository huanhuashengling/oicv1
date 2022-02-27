@extends('layouts.teacher')

@section('content')
<style>
   .hideVideo {
      display: none;
   }
   .showVideo {
      display: block;
   }
</style>
<div class="container">
  <div class="row">
    <div class="col-8">
      <h5 id="lesson-title"></h5>
      <p id="student-name"></p>
    </div>

  </div>

  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-body">
          <img id="imgPreview" scr="" />
          <video id="video" controls="" class="hideVideo" preload="none">
            <source id="mp4Preview" src="" type="video/mp4"> 
          </video>
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
  </br>
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
    var postUrl = "/posts/yuying3/" + urlParams('postCode') + ".png";
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
          if ("mp4" == data.file_ext) {
            $('#video').removeClass("hideVideo");
            $('#video').addClass("showVideo");
            $("#mp4Preview").attr("src", "/posts/yuying3/" + data.export_name);
            $('#video')[0].load();
          } else {
            $('#video').addClass("hideVideo");
            $('#video').removeClass("showVideo");
            $("#imgPreview").attr("src", "/posts/yuying3/" + data.export_name)
          }
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
  </script>
@endsection