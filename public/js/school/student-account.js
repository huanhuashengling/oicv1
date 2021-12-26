$(document).ready(function() {
	$.ajaxSetup({
	  headers: {
	    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	  }
	});
    $("#import-student-account").fileinput({
        uploadUrl:'/school/importStudents',
		showPreview: false,
		language: "zh", 
		allowedFileExtensions: ["xls", "xlsx", "csv"],
	});

    $("#update-student-email").fileinput({
        showPreview: false,
        language: "zh", 
        allowedFileExtensions: ["xls", "xlsx", "csv"],
    });

	$(".sclass-btn").click(function(e) {
		$('#student-list').bootstrapTable('destroy');
        $("#bring-to-club").removeClass("d-none");
		// console.log($(this).val());
		var sclassesId = $(this).val();
        $("#add-new-btn").removeClass("hidden");
        $("#add-new-btn").val(sclassesId);

		$('#student-list').bootstrapTable({
	        method: 'post', 
	        search: "true",
	        url: "/school/getStudentsData",
	        pagination:"true",
	        pageList: [10, 25, 50], 
	        pageSize: 10,
	        pageNumber: 1,
	        toolbar:"#toolbar",
        	queryParams: function(params) {
        		var temp = { 
			        sclasses_id : sclassesId
			    };
			    return temp;
        	},
        	clickToSelect: true,
        	columns: [{  
                        checkbox: true  
                    },{  
                        title: '序号',
                        formatter: function (value, row, index) {  
                            return index+1;  
                        }  
                    }],
	        responseHandler: function (res) {
	        	// console.log(res);
	            return res;
	        },
	    });
	});

    $(".club-btn").click(function(e) {
        $('#student-list').bootstrapTable('destroy');
        $("#bring-to-club").addClass("d-none");
        // console.log($(this).val());
        var clubsId = $(this).val();

        $('#student-list').bootstrapTable({
            method: 'post', 
            search: "true",
            url: "/school/getClubStudentsData",
            pagination:"true",
            pageList: [10, 25, 50], 
            pageSize: 10,
            pageNumber: 1,
            toolbar:"#toolbar",
            queryParams: function(params) {
                var temp = { 
                    clubs_id : clubsId
                };
                return temp;
            },
            clickToSelect: true,
            columns: [{  
                        checkbox: true  
                    },{  
                        title: '序号',
                        formatter: function (value, row, index) {  
                            return index+1;  
                        }  
                    }],
            responseHandler: function (res) {
                // console.log(res);
                return res;
            },
        });
    });

    $("#add-new-btn").click(function(e) {
        // alert($(this).val());
        $("#add-new-student-modal").modal("show");
    });

    $("#bring-to-club").click(function(e) {
        var rows = $("#student-list").bootstrapTable('getSelections');
        var nameList = "";
        var idList = "";
        for (var i = rows.length - 1; i >= 0; i--) {
            // rows[i].studentsId
            nameList = ("" == nameList)?rows[i].username:(nameList +","+ rows[i].username)
            idList = ("" == idList)?rows[i].studentsId:(idList +","+ rows[i].studentsId)
        }
        $("#student-name-list").html(nameList);
        $("#student-id-list").val(idList);
        // console.log(rows);
        // console.log(idList);
        $("#bring-into-club-modal").modal("show");
    });

    $("#confirm-bring-into-club").click(function(e) {
        if("" == $("#student-id-list").val())
        {
            alert("请先选择学生！");
            return;
        }
        data = {
            'club_id' : $("#club-select").val(),
            'student_id_list' : $("#student-id-list").val(),
        }
        $.ajax({
            type: "POST",
            url: '/school/bringStudentsIntoClub',
            data: data,
            success: function( data ) {
                $("#bring-into-club-modal").modal("hide");
                $('#student-list').bootstrapTable('refresh');
            }
        });
        // console.log($("#student-name").val());
        // console.log($("#gender").val());
        // console.log($("#add-new-btn").val());
    });

    $("#confirm-add-new-btn").click(function(e) {
        if("" == $("#student-name").val() || "" == $("#gender").val())
        {
            alert("姓名和性别不能为空！");
            return;
        }
        data = {
            'username' : $("#student-name").val(),
            'gender' : $("#gender").val(),
            'password' : "123456",
            'groups_id' : '1',
            'sclasses_id' : $("#add-new-btn").val(),
        }
        $.ajax({
            type: "POST",
            url: '/school/createOneStudent',
            data: data,
            success: function( data ) {
                $("#add-new-student-modal").modal("hide");
                $('#student-list').bootstrapTable('refresh');
            }
        });
        // console.log($("#student-name").val());
        // console.log($("#gender").val());
        // console.log($("#add-new-btn").val());
    });
});

function genderCol(value, row, index) {
    return [
        '<span>'+(("0" == value)?"女":"男")+'</span>'
    ].join('');
}

function workCommentEnableCol(value, row, index) {
    return [
        '<span>'+(("2" == value)?"禁止":"开放")+'</span>'
    ].join('');
}

function classTitleCol(value, row, index) {
    return [
        "<span>" + row["enter_school_year"] + "级" + row["class_title"] + '班</span>'
    ].join('');
}

function resetCol(value, row, index) {
    return [
        '<a class="btn btn-info btn-sm reset" data-unique-id="', row.users_id, '">重置</a>'
    ].join('');
}

function studentAccountActionCol(value, row, index) {
    var lockStr = "锁定";
    var lockClass = "lock";
    if (1 == row.is_lock)
    {
        lockStr = "解锁";
        lockClass = "unlock";
    }
    var clubStr = "";
    var lockWorkCommentStr = "禁言";
    var lockWorkCommentClass = "lockWorkComment";
    if (2 == row.work_comment_enable)
    {
        lockWorkCommentStr = "解言";
        lockWorkCommentClass = "unlockWorkComment";
    }
    if ($("#bring-to-club").hasClass("d-none")) {
        clubStr = ' <a class="btn btn-warning btn-sm unbind-club">解除社团</a>'
    }
    return [
        '<a class="btn btn-warning btn-sm '+ lockClass+'">'+lockStr+'</a> ',
        '<a class="btn btn-warning btn-sm '+ lockWorkCommentClass+'"">'+lockWorkCommentStr+'</a> ',
        ' <a class="btn btn-danger btn-sm edit">编辑</a> ',
        ' <a class="btn btn-success btn-sm work-num-add">作品数加1</a>',clubStr
    ].join('');
}

window.resetActionEvents = {
	'click .reset': function(e, value, row, index) {
     	$.ajax({
            type: "POST",
            url: '/school/resetStudentPassword',
            data: {users_id: row.studentsId},
            success: function( data ) {
            	if("true" == data) {
            		alert("重置密码成功，已修改为默认密码123456！")
            	} else if ("false" == data) {
            		alert("重置密码失败，没有找到该用户！")
            	}
            }
        });
    },
}

window.studentAccountActionEvents = {
    'click .lock': function(e, value, row, index) {
        // console.log(row);
        $.ajax({
            type: "POST",
            url: '/school/lockOneStudentAccount',
            data: {users_id: row.studentsId},
            success: function( data ) {
                if("true" == data) {
                    alert(row.username+"已被锁定！")
                } else if ("false" == data) {
                    alert("锁定失败！")
                }
            }
        });
    },
    'click .unlock': function(e, value, row, index) {
        // console.log(row);
        $.ajax({
            type: "POST",
            url: '/school/unlockOneStudentAccount',
            data: {users_id: row.studentsId},
            success: function( data ) {
                if("true" == data) {
                    alert(row.username+"解锁成功！")
                } else if ("false" == data) {
                    alert("解锁失败！")
                }
            }
        });
    },
    'click .lockWorkComment': function(e, value, row, index) {
        // console.log(row);
        $.ajax({
            type: "POST",
            url: '/school/lockOneStudentWorkComment',
            data: {users_id: row.studentsId},
            success: function( data ) {
                if("true" == data) {
                    alert(row.username+"已被禁言！")
                } else if ("false" == data) {
                    alert("禁言失败！")
                }
            }
        });
    },
    'click .unlockWorkComment': function(e, value, row, index) {
        // console.log(row);
        $.ajax({
            type: "POST",
            url: '/school/unlockOneStudentWorkComment',
            data: {users_id: row.studentsId},
            success: function( data ) {
                if("true" == data) {
                    alert(row.username+"解言成功！")
                } else if ("false" == data) {
                    alert("解言失败！")
                }
            }
        });
    },
    'click .edit': function(e, value, row, index) {
        console.log("click edit students id "+row.studentsId);
    },
    'click .unbind-club': function(e, value, row, index) {
        $.ajax({
            type: "POST",
            url: '/school/unbindClub',
            data: {users_id: row.studentsId, clubs_id:row.clubsId},
            success: function( data ) {
                if("true" == data) {
                    alert(row.username+"解除社团绑定")
                } else if ("false" == data) {
                    alert("解除社团绑定失败！")
                }
            }
        });
    },
    'click .work-num-add': function(e, value, row, index) {
        $.ajax({
            type: "POST",
            url: '/school/addMaxWorkNum',
            data: {users_id: row.studentsId},
            success: function( data ) {
                if("true" == data) {
                    alert(row.username+"作品数上限增加成功！")
                } else if ("false" == data) {
                    alert("作品数上限增加失败！")
                }
            }
        });
    },
}
