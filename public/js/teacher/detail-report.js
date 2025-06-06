$(document).ready(function() {
	$.ajaxSetup({
	  headers: {
	    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	  }
	});


	$("[name='detail_report_sclasses_id']").on("change", function(e) {
		var sclassesId = $("[name='detail_report_sclasses_id']").val();
		if(0 !=  sclassesId) {
			// alert(sclassesId + " --- " +lessonsId);
			// $('#detail-report').bootstrapTable("refresh");
            $("#requestUrl").val("/teacher/getScoreReport");
            $("#selected_sclass_id").val(sclassesId);

            $.ajax({
            type: "POST",
            url: '/teacher/getSclassTermsList',
            data: {sclassesId: sclassesId},
            success: function( data ) {
                // console.log(data);
                $("[name='detail_report_terms_id']").html(data);
                // console.log($("[name='detail_report_terms_id']"));
                $('#detail-report').bootstrapTable("refresh");
            }
        });
		}
	});

    $("[name='detail_report_terms_id']").on("change", function(e) {
        var termsId = $("[name='detail_report_terms_id']").val();
        if(0 !=  termsId) {
            // alert(sclassesId + " --- " +lessonsId);
            $('#detail-report').bootstrapTable("refresh");
        }
    });

// 标志变量，用于判断是否已经初始化列配置
    let columnsInitialized = false;

	$('#detail-report').bootstrapTable({
        method: 'post', 
        // search: "true",
        url: "/teacher/getDetailReport",
        pagination:"true",
        pageList: [50, 30], 
        pageSize: 50,
        pageNumber: 1,
        toolbar:"#toolbar",
        showExport: true,          //是否显示导出
        showColumns: true, 
        exportDataType: "basic",              //basic', 'all', 'selected'.
    	queryParams: function(params) {
    		var temp = {
                sclassesId : $("#selected_sclass_id").val(),
		        termsId : $("[name='detail_report_terms_id']").val(),
		    };
		    return temp;
    	},
    	// clickToSelect: true,
    	columns: [{  
                    checkbox: true  
                },{  
                    title: '序号',
                    formatter: function (value, row, index) {  
                        return index+1;
                    },
                    sortable: true
                },{
                    field: 'username',
                    title: '学生姓名',
                    sortable: true
                },],
		responseHandler: function (res) {
            // 存储所有作业标题，避免重复添加列
            const lessonTitles = new Set();
            // 先获取现有的列配置
            let columns = $('#detail-report').bootstrapTable('getOptions').columns[0];

            // 遍历所有行数据
            res.forEach(row => {
                if (row.lessonLogs) {
                    row.lessonLogs.forEach((lesson, index) => {
                        if (!lessonTitles.has(lesson.lesson_title)) {
                            lessonTitles.add(lesson.lesson_title);
                            columns.push({
                                field: `lesson_${index}`,
                                title: lesson.lesson_title,
                                sortable: true,
                                formatter: function (value, currentRow, currentIndex) {
                                    // 获取当前行对应作业的状态
                                    let currentLesson = currentRow.lessonLogs && currentRow.lessonLogs[index];
                                    let status = currentLesson ? currentLesson.status : null;
                                    let color = '';
                                    switch (status) {
                                        case "优+":
                                            color = 'green'; 
                                            break;
                                        case "优":
                                            color = 'green'; 
                                            break;
                                        case "不合格":
                                            color = 'red'; 
                                            break;
                                        default:
                                            color = 'black';
                                    }
                                    return `<span style="color: ${color}">${status ? status : '未交'}</span>`;
                                }
                            });
                        }
                    });
                }
            });

            // 更新表格的列配置
            if (!columnsInitialized) {
                $('#detail-report').bootstrapTable('refreshOptions', { columns: [columns] });
                columnsInitialized = true;
            }

            return res;
        },
        // 启用 fixedColumns 插件
        fixedColumns: true,
        // 指定要冻结的列数（不包括 checkbox 列）
        fixedNumber: 3
        
    });
	
});
