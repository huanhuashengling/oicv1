$(document).ready(function() {
	$.ajaxSetup({
	  headers: {
	    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	  }
	});

  getOneLesson();

  $("#lesson-select").change(function(){
    getOneLesson();
  });
});

function getOneLesson(lessonsId) {
  var lessonLogsId = $('option:selected', $("#lesson-select")).attr("lessonlogsid");
  $.ajax({
        type: "GET",
        url: '/student/getOneLesson',
        data: {lessons_id: $("#lesson-select").val(), lesson_logs_id:lessonLogsId},
        success: function( data ) {
            // console.log(data);
            var exportName = data.preview_path;
            var allowFileExt = "";
            if ("sb3" == data.allow_post_file_types) {
              $("#sb3-area").removeClass("d-none");
              $("#img-area").addClass("d-none");
              $("#sb3editor-atag").attr("href", "/scratch3/index.html?code="+data.JWTToken+"&llid="+lessonLogsId);
            } else {
              if ("mp4" == data.allow_post_file_types) {
                allowFileExt = ["mp4"];
              } else {
                allowFileExt = ["jpg", "jpeg", "png", "gif", "bmp"];
              }
              $("#img-area").removeClass("d-none");
              $("#sb3-area").addClass("d-none");
              $("#input-zh").fileinput('destroy');
              $("#input-zh").fileinput({
                  language: "zh", 
                  uploadUrl: "/student/upload", 
                  showPreview: true,
                  elErrorContainer: '#file-errors',
                  elCaptionContainer: '#caption-info',
                  allowedFileExtensions: allowFileExt, 
                  maxFileSize: 3000,
                  overwriteInitial: true,
                  initialPreview: exportName,                  
                  initialPreviewShowDelete: false,
                  initialPreviewAsData: true, // 特别重要
                  uploadExtraData: {lesson_logs_id:lessonLogsId},
                });
            }
            $('#md-content').html(data.help_md_doc);
        }
    });
}