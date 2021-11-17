$(document).ready(function() {
	$.ajaxSetup({
	  headers: {
	    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	  }
	});

  $("#input-zh").fileinput({
		language: "zh", 
		uploadUrl: "/student/upload", 
		showPreview: true,
		elErrorContainer: '#file-errors',
    elCaptionContainer: '#caption-info',
		allowedFileExtensions: ["jpg", "jpeg", "png", "gif", "bmp"], 
		overwriteInitial: true,
		initialPreview: [
			$("#posted-path").val(),
	    ],
	  initialPreviewShowDelete: false,
	  initialPreviewAsData: true, // 特别重要
	  uploadExtraData: function() { 
        var out = {};
        out.lesson_logs_id = $("#lesson_logs_id").val();
        return out;
    }
	});
});