$(document).ready(function() {
	$.ajaxSetup({
	  headers: {
	    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	  }
	});

	$("[name='sclasses_id']").on("change", function(e) {
		checkSelection();
	});

	// $("[name='lessons_id']").on("change", function(e) {
	// 	checkSelection();
	// });

	$(".class-btn").on("click", function(e) {
		e.preventDefault();
		if (!$("#lessons-id").val()) {
			alert("请先选择课程");
			return;
		}
		$(".class-btn").removeClass("btn-primary");
		$(this).addClass("btn-primary");
    $("#sclasses-id").val($(this).val());
		// $("#is-club").val("false");
		checkSelection();
	});

  $(".club-btn").on("click", function(e) {
    e.preventDefault();
    if (!$("#lessons-id").val()) {
      alert("请先选择课程");
      return;
    }
    $(".club-btn").removeClass("btn-primary");
    $(this).addClass("btn-primary");
    $("#sclasses-id").val($(this).val());
    $("#is-club").val("true");
    checkSelection();
  });

	$("#submit-btn").on("click", function(e) {
		e.preventDefault();
		var sclassesId = $("#sclasses-id").val();
		var lessonsId = $("#lessons-id").val();
    var isClub = $("#is-club").val();

		// alert(sclassesId + "  "  + lessonsId);
		$.ajax({
            type: "POST",
            url: '/teacher/createLessonLog',
            data: {sclassesId: sclassesId, lessonsId: lessonsId, isClub: isClub},
            success: function( data ) {
                if ("false" != data) {
                  if ("true" == isClub) {
                    window.location.href = "/teacher/takeclubclass";
                  } else {
                    window.location.href = "/teacher/takeclass";
                  }
                }
            }
        });
	});
});

function checkSelection() {
  var isClub = $("#is-club").val();
	var sclassesId = $("#sclasses-id").val();
	var lessonsId = $("#lessons-id").val();
	if(0 !=  sclassesId && 0 != lessonsId) {
		// alert(sclassesId + " --- " +lessonsId);
		$.ajax({
            type: "POST",
            url: '/teacher/getLessonLog',
            data: {sclassesId: sclassesId, lessonsId: lessonsId, isClub: isClub},
            success: function( data ) {
                console.log(data);
                if ("false" != data) {
                	alert("请注意，这节课你已经上过一次，已有"+data+"份作业，点击按钮课程将重新打开");
                }
            }
        });
	}
}