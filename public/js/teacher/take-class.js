$(document).ready(function() {
	$.ajaxSetup({
	  headers: {
	    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	  }
	});

    

    $('#close-lesson-log').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '/teacher/updateLessonLog',
            data: {lessonLogId: $("#lesson-log-id").val(), action: "close-lesson-log"},
            success: function( data ) {
            	// alert(data);
                if ("true" == data) {
                	window.location.href = "/teacher";
                } else {
                	alert('关闭失败!');
                }
            }
        });
    });

    $('.post-btn').on('click', function (e) {
        e.preventDefault();
        $("div[name='level-btn-group'] label").each(function(){
            $(this).removeClass("active");
        });
        // if ($(".rate-btn").hasClass('active')) {
        //   setTimeout(function() {
        //     $(".rate-btn").removeClass('active').find('input').prop('checked', false);
        //   }.bind(this), 10);
        // }
        $('#post-comment').val("");
        // console.log($(this).attr("value"));
        // var postsId = (e.target.value).split(',')[0]; 
        var postsId = $(this).attr("value");
        $('#post-show').attr("src", "");
        $.ajax({
            type: "POST",
            url: '/teacher/getPostRate',
            data: {posts_id: postsId},
            success: function( data ) {
                if ("false" == data) {

                } else {
                    $("div[name='level-btn-group'] label").each(function(){
    // console.log($(this).children().attr("value"));
                    if (data == $(this).children().attr("value")) {
                        $(this).addClass("active");
                        }
                    });
                }
            }
        });

        $.ajax({
            type: "POST",
            url: '/teacher/getPost',
            data: {posts_id: postsId},
            success: function( data ) {
                // console.log(data);
                if ("false" == data) {

                } else {
                    if ("doc" == data.filetype) {
                    } else if ("img" == data.filetype) {
                        $('#post-show').removeClass("hidden");
                        $('#doc-preview').addClass("hidden");
                        $('#flashContent').addClass("hidden");
                        $('#post-show').attr("src", data.url);
                    } else if ("sb2" == data.filetype) {
                        // alert("sb2");
                        $('#post-show').addClass("hidden");
                        $('#doc-preview').addClass("hidden");
                        $('#flashContent').removeClass("hidden");
                        var tHtml = "<object type='application/x-shockwave-flash' data='/scratch/Scratch.swf' width='850px' height='850px'>\n"+
                                        "<param name='movie' value='/scratch/Scratch.swf'/>\n"+
                                        "<param name='bgcolor' value='#FFFFFF'/>\n"+
                                        "<param name='FlashVars' value='project=" + data.url + "&autostart=false' />\n"+
                                        "<param name='allowscriptaccess' value='always'/>\n"+
                                        "<param name='allowFullScreen' value='true'/>\n"+
                                        "<param name='wmode' value='direct'/>\n"+
                                        "<param name='menu' value='false'/>\n"+
                                    "</object>";
                        $('#flashContent').html(tHtml);
                        // showScratch(data.url);
                        // showScratch(data.url);
                    }
                    // $('#doc-preview').attr("src", "http://lessons_id/op/embed.aspx?src=" + data);
                    $('#post-download-link').attr("href", data["url"]);

                }
            }
        });

        $.ajax({
            type: "POST",
            url: '/teacher/getCommentByPostsId',
            data: {posts_id: postsId},
            success: function( data ) {
                if ("false" == data) {
                    $("#edit-post-comment-btn").addClass("hidden");
                    $("#add-post-comment-btn").removeClass("hidden");
                } else {
                    var comment = JSON.parse(data);
                    $("#edit-post-comment-btn").val(comment['id']);
                    $('#post-comment').val(comment['content']);
                    $("#edit-post-comment-btn").removeClass("hidden");
                    $("#add-post-comment-btn").addClass("hidden");
                }
            }
        });

        $('#posts-id').val(postsId);
        // $('#post-show').attr("src", filePath);
        $('#myModal').modal();
    });

    $("div[name='level-btn-group'] label").on('click', function (e) {
        var data = {posts_id: $('#posts-id').val(), rate: $(this).children().attr("value")};
        // console.log($(this).children().attr("value"));
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '/teacher/updateRate',
            data: data,
            success: function( data ) {
                // alert(data);
                if ("true" == data) {
                    // window.location.href = "/teacher/takeclass";
                    $('#myModal').modal("hide");
                } else {
                    alert('评价失败!');
                }
            }
        });
    });

    $("#add-post-comment-btn").on('click', function (e) {
        var data = {posts_id: $('#posts-id').val(), content: $('#post-comment').val()};
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '/teacher/createComment',
            data: data,
            success: function( data ) {
                if ("true" == data) {
                    // window.location.href = "/teacher/takeclass";
                    $('#myModal').modal("hide");

                    // alert('添加评论成功!');
                } else {
                    alert('添加评论失败!');
                }
            }
        });
    });

    $("#update-rethink").on('click', function (e) {
        // console.log($("#rethink").val());
        if ("" == $("#rethink").val())
        {
            alert("反思没有内容！！");
            return;
        }
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '/teacher/updateRethink',
            data: {lessonLogId: $("#lesson-log-id").val(), rethink:$("#rethink").val()},
            success: function( data ) {
                if ("true" == data) {
                    alert('反思更新成功!');
                } else {
                    alert('反思更新失败!');
                }
            }
        });
    });

    $("#edit-post-comment-btn").on('click', function (e) {
        var data = {posts_id: $('#posts-id').val(), 
                    content: $('#post-comment').val(), 
                    comments_id: $("#edit-post-comment-btn").val()};
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '/teacher/updateComment',
            data: data,
            success: function( data ) {
                if ("true" == data) {
                    // window.location.href = "/teacher/takeclass";
                    $('#myModal').modal("hide");

                    // alert('编辑评论成功!');
                } else {
                    alert('编辑评论失败!');
                }
            }
        });
    });
});