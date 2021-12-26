<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'teacher','namespace' => 'Teacher'],function ($router)
{
    $router->get('login', 'LoginController@showLoginForm')->name('teacher.login');
    $router->post('login', 'LoginController@login');
    $router->get('logout', 'LoginController@logout');
});

Route::group(['middleware' => 'auth.teacher', 'prefix' => 'teacher','namespace' => 'Teacher'], function ($router)
{
    $router->get('/', 'HomeController@index');
    $router->get('takeclass', 'HomeController@takeclass');
    $router->get('takeclubclass', 'HomeController@takeclubclass');
    $router->resource('lesson', 'LessonController');
    $router->post('getLessonPostPerSclass', 'HomeController@getLessonPostPerSclass');

    Route::post('uploadMDImage', 'LessonController@uploadMDImage');
    Route::get('ajaxSearchTopics', 'LessonController@ajaxSearchTopics');
    Route::get('lessonLog', 'LessonLogController@listLessonLog');
    Route::post('loadLessonLogSelection', 'LessonLogController@loadLessonLogSelection');
    Route::post('getPostDataByTermAndSclass', 'LessonLogController@getPostDataByTermAndSclass');
    Route::post('createLessonLog', 'LessonLogController@store');
    Route::post('updateLessonLog', 'LessonLogController@update');
    Route::post('loadSclassSelection', 'LessonLogController@loadSclassSelection');
    Route::post('updateRethink', 'LessonLogController@updateRethink');
    
    Route::resource('createComment', 'CommentController@store');
    Route::resource('updateComment', 'CommentController@update');
    Route::post('getCommentByPostsId', 'CommentController@getByPostsId');
    Route::post('getPost', 'HomeController@getPost');
    Route::post('getOnePostByCode', 'HomeController@getOnePostByCode');

    Route::post('updateRate', 'HomeController@updateRate');
    Route::post('updateRateByCode', 'HomeController@updateRateByCode');
    Route::post('getPostRate', 'HomeController@getPostRate');

    $router->post('getLessonLog', 'HomeController@getLessonLog');
    $router->get('reset', 'HomeController@getReset');
    $router->post('reset', 'HomeController@postReset');

    $router->get('scoreReport', 'ScoreReportController@index');
    $router->post('getScoreReport', 'ScoreReportController@report');
    $router->post('getSclassTermsList', 'ScoreReportController@getSclassTermsList');
    $router->post('email-out', 'ScoreReportController@emailOut');


    $router->get('get-lesson-list', 'LessonController@getLessonList');
    $router->post('deleteLesson', 'LessonController@deleteLesson');
    $router->post('getLesson', 'LessonController@getLesson');
    Route::post('closeLesson', 'LessonController@closeLesson');
    Route::post('openLesson', 'LessonController@openLesson');
    Route::post('chooseLesson', 'LessonController@chooseLesson');

    Route::resource('course', 'CourseController');
    Route::get('get-course-list', 'CourseController@getCourseList');
    Route::post('closeCourse', 'CourseController@closeCourse');
    Route::post('openCourse', 'CourseController@openCourse');

    Route::resource('unit', 'UnitController');
    Route::get('get-unit-list', 'UnitController@getUnitList');
    Route::post('get-unit-list-by-courses-id', 'UnitController@getUnitListByCoursesId');
    Route::post('closeUnit', 'UnitController@closeUnit');
    Route::post('openUnit', 'UnitController@openUnit');

    $router->get('sb3player', 'HomeController@sb3player');
    $router->get('imgPreview', 'HomeController@imgPreview');
    

});

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::group(['prefix' => 'student','namespace' => 'Student'],function ($router)
{
    $router->get('login', 'LoginController@showLoginForm')->name('student.login');
    $router->post('login', 'LoginController@login');
    $router->get('logout', 'LoginController@logout');
});

Route::group(['middleware' => 'auth.student', 'prefix' => 'student','namespace' => 'Student'],function ($router)
{

    $router->get('/home', 'HomeController@index');
    $router->get('/', 'HomeController@index');
    $router->get('/posts', 'PostController@index');
    $router->get('getPostsByTerm', 'PostController@getPostsByTerm');
    
    $router->post('upload', 'HomeController@upload');
    $router->post('getentry', 'HomeController@get');
    $router->post('getCommentByPostsId', 'HomeController@getCommentByPostsId');
    $router->post('getPostRate', 'HomeController@getPostRate');
    $router->post('getOnePost', 'HomeController@getOnePost');
    $router->post('getOnePostByCode', 'PostController@getOnePostByCode');
    $router->post('getMarkNumByPostsId', 'HomeController@getMarkNumByPostsId');
    $router->post('getIsMarkedByMyself', 'HomeController@getIsMarkedByMyself');
    $router->post('updateMarkState', 'HomeController@updateMarkState');
    
    $router->post('updateMarkByCode', 'PostController@updateMarkByCode');

    Route::resource('work', 'WorkController');
    $router->post('upload-cover', 'WorkController@uploadCover');
    $router->post('upload-work', 'WorkController@uploadWork');

    $router->get('/get-work-list', 'WorkController@workList');
    
    $router->get('open-classroom', 'OpenClassroomController@index');
    $router->get('open-classroom/course/{id}', 'OpenClassroomController@course');
    $router->get('open-classroom/unit/{id}', 'OpenClassroomController@unit');
    $router->get('open-classroom/lesson/{id}', 'OpenClassroomController@lesson');

    $router->get('getOneLesson', 'HomeController@getOneLesson');



    $router->get('classmate', 'ClassmateController@classmatePost');
    
    $router->post('getPostsDataByType', 'ClassmateController@getPostsDataByType');
    

    $router->get('reset', 'HomeController@getReset');
    $router->get('info', 'HomeController@getStudentInfo');
    $router->post('reset', 'HomeController@postReset');


    $router->get('sb3player', 'PostController@sb3player');
    $router->get('imgPreview', 'PostController@imgPreview');

});

Route::group(['prefix' => 'school','namespace' => 'School'],function ($router)
{
    $router->get('login', 'LoginController@showLoginForm')->name('school.login');
    $router->post('login', 'LoginController@login');
    $router->get('logout', 'LoginController@logout');
});

Route::group(['middleware' => 'auth.school:school, school/login', 'prefix' => 'school','namespace' => 'School'],function ($router)
{
    // dashboard
    $router->get('/dashboard', 'HomeController@index');
    $router->get('/', 'HomeController@index');
    $router->get('get-post-count-per-class-same-grade-data-1', 'HomeController@getPostCountPerClassWithSameGradeData1');
    $router->get('get-post-count-per-class-same-grade-data-2', 'HomeController@getPostCountPerClassWithSameGradeData2');
    $router->get('get-mark-count-per-class-same-grade-data-1', 'HomeController@getMarkCountPerClassWithSameGradeData1');
    $router->get('get-mark-count-per-class-same-grade-data-2', 'HomeController@getMarkCountPerClassWithSameGradeData2');

    // class
    $router->get('sclasses', 'SclassController@index');
    $router->post('getSclassesData', 'SclassController@getSclassesData');
    $router->post('getTermsData', 'SclassController@getTermsData');
    $router->post('createOneSclass', 'SclassController@createOneSclass');
    $router->post('createOneTerm', 'SclassController@createOneTerm');

    $router->post('getClubsData', 'SclassController@getClubsData');
    $router->post('createOneClub', 'SclassController@createOneClub');

    //student
    $router->get('students', 'StudentAccountController@index');
    $router->post('importStudents', 'StudentAccountController@importStudents')->name('school.importStudents');
    $router->post('updateStudentEmail', 'StudentAccountController@updateStudentEmail');
    $router->post('bringStudentsIntoClub', 'StudentAccountController@bringStudentsIntoClub');
    $router->post('getStudentsData', 'StudentAccountController@getStudentsData');
    $router->post('getClubStudentsData', 'StudentAccountController@getClubStudentsData');
    $router->post('resetStudentPassword', 'StudentAccountController@resetStudentPassword');
    $router->post('lockOneStudentAccount', 'StudentAccountController@lockOneStudentAccount');
    $router->post('unlockOneStudentAccount', 'StudentAccountController@unlockOneStudentAccount');
    $router->post('createOneStudent', 'StudentAccountController@createOneStudent');

    $router->post('lockOneStudentWorkComment', 'StudentAccountController@lockOneStudentWorkComment');
    $router->post('unlockOneStudentWorkComment', 'StudentAccountController@unlockOneStudentWorkComment');
    $router->post('addMaxWorkNum', 'StudentAccountController@addMaxWorkNum');
    $router->post('unbindClub', 'StudentAccountController@unbindClub');

    //group
    $router->get('groups', 'GroupController@index');
    $router->post('getGroupsInSclass', 'GroupController@getGroupsInSclass');
    $router->post('createGroupInSclass', 'GroupController@createGroupInSclass');
    $router->post('getStudentsInGroup', 'GroupController@getStudentsInGroup');
    $router->post('getStudentsInSclassButNotInGroupsBtns', 'GroupController@getStudentsInSclassButNotInGroupsBtns');
    $router->post('addOneStudentIntoGroup', 'GroupController@addOneStudentIntoGroup');
    $router->post('removeOneStudentOutGroup', 'GroupController@removeOneStudentOutGroup');
    $router->post('removeOneGroup', 'GroupController@removeOneGroup');
    

    // teacher
    $router->get('teachers', 'TeacherAccountController@index');
    $router->post('getTeachersAccountData', 'TeacherAccountController@getTeachersAccountData');
    $router->post('createOneTeacherAccount', 'TeacherAccountController@createOneTeacherAccount');
    $router->post('resetTeacherPassword', 'TeacherAccountController@resetTeacherPassword');

    // export
    $router->get('export-post', 'ExportPostController@index');
    $router->post('export-post-files', 'ExportPostController@exportPostFiles');
    $router->post('load-lesson-log-info', 'ExportPostController@loadLessonLogInfo');
    $router->post('load-post-list', 'ExportPostController@loadPostList');
    $router->get('create-zip', 'ExportPostController@exportPostFiles');
    $router->get('clear-all-zip', 'ExportPostController@clearALlZip');
    
    $router->post('loadSclassSelection', 'ExportPostController@loadSclassSelection');
    // lesson log
    $router->get('lessonLog', 'LessonLogController@index');
    $router->post('get-lesson-log-list', 'LessonLogController@getLessonLogList');
    $router->post('delLessonLog', 'LessonLogController@delLessonLog');

    // email temp close this function
    $router->get('send-mail', 'SendMailController@index');
    $router->get('get-send-mail-list', 'SendMailController@listAllMails');
    $router->post('addSendMail', 'SendMailController@addSendMail');
    $router->post('updateSendMail', 'SendMailController@updateSendMail');



    // term-end-export
    $router->get('term-end-export', 'TermEndExportController@index');
    // $router->post('export-post-files', 'TermEndExportController@exportPostFiles');
    // $router->post('load-lesson-log-info', 'TermEndExportController@loadLessonLogInfo');
    $router->post('load-term-end-post-list', 'TermEndExportController@loadTermEndPostList');
    // $router->get('create-zip', 'TermEndExportController@exportPostFiles');
    // $router->get('clear-all-zip', 'TermEndExportController@clearALlZip');

    // reset password
    $router->get('reset', 'HomeController@getReset');
    $router->post('reset', 'HomeController@postReset');

});
Route::group(['prefix' => 'district','namespace' => 'District'],function ($router)
{
    $router->get('login', 'LoginController@showLoginForm')->name('district.login');
    $router->post('login', 'LoginController@login');
    $router->get('logout', 'LoginController@logout');
});

Route::group(['middleware' => 'auth.district:district, district/login', 'prefix' => 'district','namespace' => 'District'],function ($router)
{
    $router->get('/dashboard', 'HomeController@index');
    $router->get('/', 'HomeController@index');

    $router->get('schools', 'SchoolController@index');
    $router->post('getSchoolsAccountData', 'SchoolController@getSchoolsData');
    $router->post('createOneSchool', 'SchoolController@createOneSchool');

    // $router->post('importStudents', 'HomeController@importStudents');
    $router->post('updateStudentEmail', 'HomeController@updateStudentEmail');
    $router->post('getStudentsData', 'HomeController@getStudentsData');
    $router->post('resetSchoolPassword', 'HomeController@resetSchoolPassword');
    $router->post('lockOneStudentAccount', 'HomeController@lockOneStudentAccount');
    $router->post('unlockOneStudentAccount', 'HomeController@unlockOneStudentAccount');
    $router->post('createOneStudent', 'HomeController@createOneStudent');


    $router->get('reset', 'HomeController@getReset');
    $router->post('reset', 'HomeController@postReset');


    $router->get('export-post', 'ExportPostController@index');
    $router->post('export-post-files', 'ExportPostController@exportPostFiles');
    $router->post('load-lesson-log-info', 'ExportPostController@loadLessonLogInfo');
    $router->post('load-post-list', 'ExportPostController@loadPostList');
    

    $router->get('lessonLog', 'LessonLogController@index');
    $router->post('get-lesson-log-list', 'LessonLogController@getLessonLogList');
    $router->post('delLessonLog', 'LessonLogController@delLessonLog');


    $router->get('get-post-count-per-class-same-grade-data-1', 'HomeController@getPostCountPerClassWithSameGradeData1');
    $router->get('get-post-count-per-class-same-grade-data-2', 'HomeController@getPostCountPerClassWithSameGradeData2');
    $router->get('get-mark-count-per-class-same-grade-data-1', 'HomeController@getMarkCountPerClassWithSameGradeData1');
    $router->get('get-mark-count-per-class-same-grade-data-2', 'HomeController@getMarkCountPerClassWithSameGradeData2');


    $router->get('create-zip', 'ExportPostController@exportPostFiles');
    $router->get('clear-all-zip', 'ExportPostController@clearALlZip');

    $router->get('teachers', 'TeacherAccountController@index');

    $router->get('send-mail', 'SendMailController@index');
    $router->get('get-send-mail-list', 'SendMailController@listAllMails');
    $router->post('addSendMail', 'SendMailController@addSendMail');
    $router->post('updateSendMail', 'SendMailController@updateSendMail');

});