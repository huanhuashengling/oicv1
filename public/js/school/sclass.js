$(document).ready(function() {
	$.ajaxSetup({
	  headers: {
	    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	  }
	});
    $("#import-student-account").fileinput({
		showPreview: false,
		language: "zh", 
		allowedFileExtensions: ["xls", "xlsx", "csv"],
	});

    $("#update-student-email").fileinput({
        showPreview: false,
        language: "zh", 
        allowedFileExtensions: ["xls", "xlsx", "csv"],
    });

	$('#sclass-list').bootstrapTable({
        method: 'post', 
        search: "true",
        url: "/school/getSclassesData",
        pagination:"true",
        pageList: [10, 25, 50], 
        pageSize: 10,
        pageNumber: 1,
        toolbar:"#sclass-toolbar",
    	queryParams: function(params) {
    		var temp = { 
		        sclasses_id : 1
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

	$('#term-list').bootstrapTable({
        method: 'post', 
        url: "/school/getTermsData",
        pagination:"true",
        pageList: [10, 25, 50], 
        pageSize: 10,
        pageNumber: 1,
        toolbar:"#term-toolbar",
    	queryParams: function(params) {
    		var temp = { 
		        sclasses_id : 1
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


    $('#club-list').bootstrapTable({
        method: 'post', 
        search: "true",
        url: "/school/getClubsData",
        pagination:"true",
        pageList: [10, 25, 50], 
        pageSize: 10,
        pageNumber: 1,
        toolbar:"#club-toolbar",
        queryParams: function(params) {
            var temp = { 
                clubs_id : 1
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
            console.log(res);
            return res;
        },
    });

    $("#add-new-sclass").click(function(e) {
        $("#add-new-sclass-modal").modal("show");
    });

    $("#add-new-term").click(function(e) {
        $("#add-new-term-modal").modal("show");
    });

    $("#add-new-club").click(function(e) {
        $("#add-new-club-modal").modal("show");
    });

    $("#confirm-add-new-btn").click(function(e) {
        if("" == $("#enter-school-year").val() || "" == $("#class-num").val() || "" == $("#class-title").val())
        {
            alert("三项都必填！");
            return;
        }
        data = {
            'enter_school_year': $("#enter-school-year").val(),
            'class_num': $("#class-num").val(),
            'class_title': $("#class-title").val(),
            'is_graduated': "0",
            'schools_id': $("#schools-id").val(),
        }
        // console.log(data);
        $.ajax({
            type: "POST",
            url: '/school/createOneSclass',
            data: data,
            success: function( data ) {
                $("#add-new-sclass-modal").modal("hide");
                $('#sclass-list').bootstrapTable('refresh');
            }
        });
    });

    $("#confirm-add-new-term").click(function(e) {
        if("" == $("#term-enter-school-year").val() || "" == $("#grade-key").val() || "" == $("#term-segment").val())
        {
            alert("三项都必填！");
            return;
        }
        data = {
            'enter_school_year': $("#term-enter-school-year").val(),
            'grade_key': $("#grade-key").val(),
            'term_segment': $("#term-segment").val(),
            'from_date': $("#start-date").val(),
            'to_date': $("#end-date").val(),
            'is_current': "1",
            // 'schools_id': $("#schools-id").val(),
        }
        // console.log(data);
        $.ajax({
            type: "POST",
            url: '/school/createOneTerm',
            data: data,
            success: function( data ) {
                $("#add-new-term-modal").modal("hide");
                $('#term-list').bootstrapTable('refresh');
            }
        });
        // console.log($("#student-name").val());
        // console.log($("#gender").val());
        // console.log($("#add-new-btn").val());
    });

    $("#confirm-add-new-club").click(function(e) {
        if("" == $("#club-title").val() || "" == $("#term-desc").val())
        {
            alert("两项都必填！");
            return;
        }
        data = {
            'club_title': $("#club-title").val(),
            'term_desc': $("#term-desc").val(),
            'status': "open",
            'schools_id': $("#schools-id").val(),
        }
        console.log(data);
        $.ajax({
            type: "POST",
            url: '/school/createOneClub',
            data: data,
            success: function( data ) {
                $("#add-new-club-modal").modal("hide");
                $('#club-list').bootstrapTable('refresh');
            }
        });
    });
});

function graduatedCol(value, row, index) {
    return [
        '<span>'+(("0" == value)?"否":"是")+'</span>'
    ].join('');
}

function resetCol(value, row, index) {
    return [
        '<a class="btn btn-info btn-sm reset" data-unique-id="', row.users_id, '">重置</a>'
    ].join('');
}

function sclassActionCol(value, row, index) {
    return [
        ' <a class="btn btn-danger btn-sm edit">编辑</a> ',
    ].join('');
}

function clubActionCol(value, row, index) {
    return [
        ' <a class="btn btn-danger btn-sm edit">关闭</a> ',
        ' <a class="btn btn-danger btn-sm edit">编辑</a> ',
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

window.classActionEvents = {
    'click .edit': function(e, value, row, index) {
        console.log("click edit students id "+row.studentsId);
    },
}
