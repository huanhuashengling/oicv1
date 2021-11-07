$(document).ready(function() {
	$.ajaxSetup({
	  headers: {
	    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	  }
	});

  $("#input-zh").fileinput({
		language: "zh", 
		uploadUrl: "/student/upload", 
		showPreview: false,
		elErrorContainer: '#file-errors',
		allowedFileExtensions: ["jpg", "png", "gif", "bmp"], 
	});
});