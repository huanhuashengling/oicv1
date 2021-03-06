$(document).ready(function() {
	$.ajaxSetup({
	  headers: {
	    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	  }
	});

	$("#term-selection").change(function(){
		if (0 == $("#term-selection").val()) {
    		$("#lesson-log-selection").html("<option>请选择学期</option>");
    		return;
    	}
	});

	$("#classes-selection").change(function(){
		if (0 == $("#term-selection").val()) {
    		$("#lesson-log-selection").html("<option>请选择学期</option>");
    		return;
    	}
		if (0 == $("#classes-selection").val()) {
    		$("#lesson-log-selection").html("<option>请选择班号</option>");
    		return;
    	}
		// alert($("#term-selection").val());
		$.ajax({
            type: "POST",
            url: '/term-check-load-lesson-log',
            data: {terms_id: $("#term-selection").val(), class_num: $("#classes-selection").val()},
            success: function( data ) {
            	// console.log(data);
            	$("#lesson-log-selection").html(data);
            }
        });
	});

	$("#lesson-log-selection").change(function(){
		// alert($("#term-selection").val());
		$.ajax({
            type: "POST",
            url: '/term-check-get-post-data/',
            data: {lessonlogsId: $("#lesson-log-selection").val()},
            success: function( data ) {
            	// console.log(data);
            	$("#posts_area").html(data);
            }
        });
	});

    $(document)
		.on('click', '.post-btn', function(e) {
	        e.preventDefault();
	        $('#post-comment').val("");
	        // var postsId = (e.target.value).split(',')[0]; 
	        var postsId = $(this).attr("value"); 
	        $('#post-show').attr("src", "");
	        $.ajax({
	            type: "POST",
	            url: '/term-check-get-post-rate/',
	            data: {posts_id: postsId},
	            success: function( data ) {
	                if ("false" == data) {
                        $("#rate").text("等第：无")
	                } else {
                        $("#rate").text("等第:" + data)
	                }
	            }
	        });

	        $.ajax({
	            type: "POST",
	            url: '/term-check-get-post/',
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
                    }
                    // $('#doc-preview').attr("src", "http://lessons_id/op/embed.aspx?src=" + data);

                }
	            }
	        });

	        $.ajax({
	            type: "POST",
	            url: '/term-check-get-comment/',
	            data: {posts_id: postsId},
	            success: function( data ) {
	                if ("false" == data) {
                        $('#post-comment').text("教师评语：无");
	                } else {
	                    var comment = JSON.parse(data);
	                    $('#post-comment').text("教师评语：" + comment['content']);
	                }
	            }
	        });

	        $('#posts-id').val(postsId);
	        // $('#post-show').attr("src", filePath);
	        $('#myModal').modal();
	    })
});