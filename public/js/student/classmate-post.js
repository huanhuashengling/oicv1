$(document).ready(function() {
	$.ajaxSetup({
	  headers: {
	    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	  }
	});

    $('[data-magnify=gallery]').magnify({"modalWidth": 800, "ratioThreshold":0.25, "minRatio": 0.5, "maxRatio": 32, "modalHeight": 600, "title": false, "footToolbar": ['zoomIn', 
'zoomOut', 
'fullscreen',
'actualSize',
'rotateRight']});

    $("#reload-btn").on("click", function(e) {
        top.location = "/student/classmate";
    });

    $("#vr-btn").on("click", function (e) {
        $('#classmate-post-show').addClass("hidden");
        // $("#vr-area").removeClass("hidden");
        $('#image-360').attr("src", $('#image-360-src').val());
        var scene = document.querySelector('a-scene');
        scene.enterVR();
    });

    $("#2d-btn").on("click", function (e) {
        $('#classmate-post-show').removeClass("hidden");
        // $("#vr-area").addClass("hidden");
        var scene = document.querySelector('a-scene');
        scene.exitVR();
    });

    $("[name='likeCheckBox']").bootstrapSwitch({
        onText: '点赞',
        offText: '点赞',
        onColor: 'danger',
        offColor: 'default',
    });
    // $("#is-init").val("true");
    $("[name='likeCheckBox']").on('switchChange.bootstrapSwitch', function (e, data) {
        // console.log("change state event init value "+$("#is-init").val() +" data "+data);
        // var $el = $(data.el)
        // , value = data.value;
        // console.log("init" + $("#is-init").val());
        if ("true" == $("#is-init").val()) {
            var currentMarkNum = parseInt($("#mark-num").val());
            // console.log(e, data, currentMarkNum);

            var markNum = 0;
            $.ajax({
                type: "POST",
                url: '/student/updateMarkState',
                data: {postsId: $("#posts-id").val(), stateCode:((true == data)?1:0)},
                success: function( returnData ) {
                    // console.log(data);
                    if ("false" == returnData) {

                    } else {
                        if (true == data) {
                            markNum = currentMarkNum+1;
                        } else {
                            markNum = (0 == currentMarkNum)?0:(currentMarkNum-1);
                        }
                        $("#mark-num").val(markNum);
                        $("[name='likeCheckBox']").siblings(".bootstrap-switch-label").text(markNum);
                    }
                }
            });
        }
        $("#is-init").val("true");
    });

    $('#my-posts-btn').on('click', function (e) {
        top.location='/student/classmate?type=my'; 
    });

    $('#all-posts-btn').on('click', function (e) {
        top.location='/student/classmate?type=all'; 
    });

    $('#current-lesson-log-btn').on('click', function (e) {
        top.location='/student/classmate?type=current-lesson-log'; 
    });

    $('#same-sclass-posts-btn').on('click', function (e) {
        top.location='/student/classmate?type=same-sclass'; 
    });

    $('#same-grade-posts-btn').on('click', function (e) {
        top.location='/student/classmate?type=same-grade'; 
    });

    $('#my-marked-posts-btn').on('click', function (e) {
        top.location='/student/classmate?type=my-marked'; 
    });

    $('#most-marked-posts-btn').on('click', function (e) {
        top.location='/student/classmate?type=most-marked'; 
    });

    // $('#has-comment-posts-btn').on('click', function (e) {
    //     top.location='/student/classmate?type=has-comment'; 
    // });

    $('#name-search-btn').on('click', function (e) {
        if("" == $("#search-name").val()) {
            alert("姓名不能为空！");
        } else {
            top.location='/student/classmate?type=search-name=' + $("#search-name").val(); 
        }
    });

    $('.card-img-top').on('click', function (e) {
        e.preventDefault();
        // $("[name='likeCheckBox']").bootstrapSwitch('state', "false");
        if ($(".rate-btn").hasClass('active')) {
          setTimeout(function() {
            $(".rate-btn").removeClass('active').find('input').prop('checked', false);
          }.bind(this), 10);
        }
        // $("#vr-area").addClass("hidden");
        // $('#post-comment').val("");
        // $("#switch-box").addClass('hidden'); 
        // console.log($(this).attr("value"));
        // var postsId = (e.target.value).split(',')[0]; 
        var postsId = $(this).attr("value");
        $('#classmate-post-show').attr("src", "");

        $.ajax({
            type: "POST",
            url: '/student/getPostRate',
            data: {posts_id: postsId},
            success: function( data ) {
                if ("false" != data && "待完" != data) {
                    // $("#switch-box").removeClass('hidden'); 
                }
            }
        });

        $.ajax({
            type: "POST",
            url: '/student/getMarkNumByPostsId',
            data: {postsId: postsId},
            success: function( data ) {
                $("[name='likeCheckBox']").siblings(".bootstrap-switch-label").text(data);
                $("#mark-num").val(data);
            }
        });

        $.ajax({
            type: "POST",
            url: '/student/getIsMarkedByMyself',
            data: {postsId: postsId},
            success: function( data ) {
                // console.log("getIsMarkedByMyself " + data);

                if ("true" == data) {
                    $("#is-init").val("false");
                    // $("#is-marked-by-myself").val(data);
                    $("[name='likeCheckBox']").bootstrapSwitch('state', true);
                    // $("[name='likeCheckBox']").prop('checked', true);
                } else {
                    $("[name='likeCheckBox']").bootstrapSwitch('state', false);
                }
                
            }
        });

        $.ajax({
            type: "POST",
            url: '/student/getOnePost',
            data: {posts_id: postsId},
            success: function( data ) {
                // console.log(data);
                if ("false" == data) {

                } else {
                    if ("doc" == data.filetype) {

                    } else if ("img" == data.filetype) {
                        // $('#doc-preview').addClass("hidden");
                        $('#classmate-post-show').removeClass("hidden");
                        // $('#flashContent').addClass("hidden");
                        $('#classmate-post-show').attr("src", data.storage_name);
                        $('#classmate-post-show').attr("data-src", data.file_path);
                        // $("#magnify-href").href(data.storage_name);
                        // $('#image-360-src').val(data.file_path);
                    } else if ("sb3" == data.filetype) {
                        top.location='/student/sb3player?postCode=' + data.post_code; 
                        // $('#doc-preview').addClass("hidden");
                        // $('#classmate-post-show').addClass("hidden");
                        
                        // $('#sb3iFrame').attr("src", "/scratch3/player.html?postUrl=" + data.file_path);
                    }
                    $('#space-link').html("<a class='btn btn-success' href='/space?sId=" + data.students_id + "' target='_blank'>访问" + data.username + "同学的空间</a>");
                    $("#classmate-post-modal-label").html(data.username+" 同学在 "+data.lessontitle+"<small>"+data.lessonsubtitle+"</small> 课上提交的作品");
                }
            }
        });

        // $.ajax({
        //     type: "POST",
        //     url: '/student/getCommentByPostsId',
        //     data: {posts_id: postsId},
        //     success: function( data ) {
        //         // console.log(data);
        //         if ("false" == data) {
        //             $("#edit-post-comment-btn").addClass("hidden");
        //             $("#add-post-comment-btn").removeClass("hidden");
        //         } else {
        //             var comment = JSON.parse(data);
        //             $("#edit-post-comment-btn").val(comment['id']);
        //             $('#post-comment').text("老师评语：" + comment['content']);
        //             $("#edit-post-comment-btn").removeClass("hidden");
        //             $("#add-post-comment-btn").addClass("hidden");
        //         }
        //     }
        // });

        $('#posts-id').val(postsId);
        // $('#post-show').attr("src", filePath);
        // var myModal = new bootstrap.Modal(document.getElementById('classmate-post-modal'), {
        //       keyboard: false
        //     })
        // myModal.show();
        // console.log(window.vm);
        // rescale();
    });
});


function rescale(){
    var size = {width: $(window).width() , height: $(window).height() }
    /*CALCULATE SIZE*/
    var offset = 20;
    var offsetBody = 150;
    $('#classmate-post-modal').css('height', size.height - offset );
    $('.modal-body').css('height', size.height - (offset + offsetBody));
    $('#classmate-post-modal').css('top', 0);
}
$(window).bind("resize", rescale);