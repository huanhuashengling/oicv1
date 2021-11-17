$(document).ready(function() {
	$.ajaxSetup({
	  headers: {
	    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	  }
	});

    $('#post-list').bootstrapTable({
        method: 'get', 
        search: "true",
        url: "/student/getPostsByTerm",
        pagination:"false",
        pageList: [20], 
        pageSize:20,
        pageNumber: 1,
        toolbar:"#toolbar",
        queryParams: function(params) {
         var temp = { 
                termsId : $("#term-selection").val(),
            };
            console.log(temp.termsId);
            return temp;
        },
        // clickToSelect: true,
        columns: [
        // {  
        //             checkbox: true  
        //         },
                {  
                    title: '序号',
                    formatter: function (value, row, index) {  
                        return index+1;  
                    }  
                }],
        responseHandler: function (res) {
            console.log(res);
            return res;
        },
    });

    $("#term-selection").change(function(e){
        // refreshPostList();
    })
    // refreshPostList();
    $(document)
	   .on('click', '.panel-title', function (e) {
            if ($(this).attr("value")) {

                // $.ajax({
                //     type: "POST",
                //     url: '/student/getOnePost',
                //     data: {posts_id: postsId},
                //     success: function( data ) {
                //         // console.log(data);
                //         if ("false" == data) {

                //         } else {
                //             if ("doc" == data.filetype) {
                //                 $('#doc-preview').html(OnCreateUrl(data.storage_name));
                //             } else if ("img" == data.filetype) {
                //                 $('#classmate-post-show').attr("src", data.storage_name);
                //             }
                //             // $('#classmate-post-show').attr("src", data.storage_name);
                //             $("#classmate-post-modal-label").html(data.username+" 同学在 "+data.lessontitle+"<small>"+data.lessonsubtitle+"</small> 课上提交的作品");
                //         }
                //     }
                // });
                
                var postsId = $(this).attr("value").split(',')[0]; 
                var filePath = $(this).attr("value").split(',')[1]; 
                var filetype = $(this).attr("value").split(',')[2]; 
                var previewPath = $(this).attr("value").split(',')[3]; 

                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: '/student/getPostRate',
                    data: {posts_id : postsId},
                    success: function( data ) {
                        //console.log(data);
                        var rateStr = "暂无等第";
                        if ("false" != data) {
                           rateStr = "等第：" + data;
                        }
                        $('#rate-label-'+postsId).text(rateStr);
                    }
                });

                $.ajax({
                    type: "POST",
                    url: '/student/getCommentByPostsId',
                    data: {posts_id : postsId},
                    success: function( data ) {
                        var conmmentStr = "暂无评语";
                        if ("false" != data) {
                            conmmentStr = "老师评语：" + JSON.parse(data)['content'];
                        // console.log(JSON.parse(data));
                        }
                        $('#post-comment-'+postsId).text(conmmentStr);
                    }
                });

                $('#posts-id').val(postsId);
                if ("doc" == filetype) {
                    // console.log(OnCreateUrl(previewPath));
                    // console.log((previewPath));
                    $('#doc-preview-'+postsId).html(OnCreateUrl(previewPath));
                } else if ("img" == filetype) {
                    $('#post-show-'+postsId).attr("src", filePath);
                }
                // $('#post-show-'+postsId).attr("src", filePath);
                $('#post-download-'+postsId).attr("href", filePath);
                // $('#post-show-'+postsId).attr("href", filePath);
                //$('#myModal').modal();
            }
            
        });
        // $(".input-zh").each(function() {
        //     console.log($(this).attr("id"));
        //     var postsId = $(this).attr("id").split("-")[2];
        //     if (postsId) {
        //         $(this).fileinput({
        //             language: "zh", 
        //             // uploadUrl: "student/upload", 
        //             allowedFileExtensions: ["jpg", "png", "gif", "bmp", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "html"], 
        //             // uploadAsync: true
        //             // overwriteInitial: true,
        //             initialPreview: [
        //                 $("#posted-path-"+postsId).val(),
        //             ],
        //             initialPreviewAsData: true, // 特别重要
        //         });
        //     } else {
        //         $(this).fileinput({
        //             language: "zh", 
        //             allowedFileExtensions: ["jpg", "png", "gif", "bmp", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "html"], 
        //             initialPreviewAsData: true, // 特别重要
        //         });
        //     }
        // })
});

function refreshPostList() {
    // alert($("#term-selection").val());
    $.ajax({
        type: "POST",
        url: '/student/getPostsByTerm',
        data: {termsId : $("#term-selection").val()},
        success: function( data ) {
            $("#posts-list").html(data);
            // console.log(data);
            $(".input-zh").each(function() {
                // console.log($(this).attr("id"));
                var postsId = $(this).attr("id").split("-")[2];
                if (postsId) {
                    $(this).fileinput({
                        language: "zh", 
                        // uploadUrl: "student/upload", 
                        allowedFileExtensions: ["jpg", "png", "gif", "bmp", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "html", "sb2"], 
                        // uploadAsync: true
                        // overwriteInitial: true,
                        initialPreview: [
                            $("#posted-path-"+postsId).val(),
                        ],
                        initialPreviewAsData: true, // 特别重要
                    });
                } else {
                    $(this).fileinput({
                        language: "zh", 
                        allowedFileExtensions: ["jpg", "png", "gif", "bmp", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "html", "sb2"], 
                        initialPreviewAsData: true, // 特别重要
                    });
                }
            })
        }
    });
}

function OnCreateUrl(data)
{
    // var originalUrl = document.getElementById(OriginalUrlElementId).value;
    var originalUrl = data;

    // var generatedViewUrl = ViewUrlMask.replace(UrlPlaceholder, encodeURIComponent(originalUrl));
    var generatedEmbedCode = EmbedCodeMask.replace(UrlPlaceholder, encodeURIComponent(originalUrl));
    return generatedEmbedCode;
    // document.getElementById(GeneratedViewUrlElementId).value = generatedViewUrl;
    // document.getElementById(GeneratedEmbedCodeElementId).value = generatedEmbedCode;
}

function descCol(value, row, index) {
    var str = (25 < value.length)?(value.substring(0,25)+"..."):value;
    return [
        "<span>" + str + '</span>'
    ].join('');
}

function isOpenCol(value, row, index) {
    var str = (1 == value)?"是":"-";
    return [
        "<span>" + str + "</span>"
    ].join('');
}

function actionCol(value, row, index) {
    var lockStr = "关闭";
    var lockClass = "closeCourse";
    if (2 == row.is_open)
    {
        lockStr = "开放";
        lockClass = "openCourse";
    }
    return [
        ' <a class="btn btn-info btn-sm ' + lockClass + '">' + lockStr + '</a>',
    ].join('');
}

window.actionEvents = {
    'click .edit': function(e, value, row, index) {
        window.location.href = "/teacher/course/"+row.id+"/edit";
    },
    'click .closeCourse': function(e, value, row, index) {
        // console.log(row);
        $.ajax({
            type: "POST",
            url: '/teacher/closeCourse',
            data: {courses_id: row.id},
            success: function( data ) {
                if("true" == data) {
                    alert(row.title+" 课程已被关闭！")
                } else if ("false" == data) {
                    alert("关闭课程失败！")
                }
            }
        });
        $('#course-list').bootstrapTable("refresh");
    },
    'click .openCourse': function(e, value, row, index) {
        // console.log(row);
        $.ajax({
            type: "POST",
            url: '/teacher/openCourse',
            data: {courses_id: row.id},
            success: function( data ) {
                if("true" == data) {
                    alert(row.title+" 课程已被开放！")
                } else if ("false" == data) {
                    alert("开放课程失败！")
                }
            }
        });
        $('#course-list').bootstrapTable("refresh");
    },
    'click .unit': function(e, value, row, index) {
        // console.log(row);
        window.location.href = "/teacher/unit?cId="+row.id;
    },
    'click .del': function(e, value, row, index) {
        alert("目前不能删除！！");
        /*if(row.lesson_log_num > 0) {
            alert("上课纪录大于1，不能删除！！");
        } else {
            if (confirm("确认删除当前的课程吗？")==true) {
                $.ajax({
                    type: "POST",
                    url: '/teacher/deleteLesson',
                    data: {lessonsId: row.id},
                    success: function( data ) {
                        // console.log(data);
                        if ("true" == data) {
                            alert("课程删除成功！");
                        } else {
                            alert("课程删除失败！");
                        }
                        // $("#no-post-report").html(data);
                        $('#lesson-list').bootstrapTable("refresh");
                    }
                });
            }
        }*/
        
    },
}