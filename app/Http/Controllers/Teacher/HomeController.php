<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Redirect;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Sclass;
use App\Models\School;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\Unit;
use App\Models\Post;
use App\Models\PostRate;
use App\Models\Comment;
use App\Models\LessonLog;
use App\Models\Mark;
use App\Models\Term;
use App\Models\Club;
use App\Models\ClubStudent;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use \Auth;
use \DB;
use \Storage;
use App\Libaries\pinyinfirstchar;


class HomeController extends Controller
{
    public function index(Request $request)
    {
        // dd(auth()->guard('teacher')->user());

        $userId = auth()->guard('teacher')->id();
        $teacher = Teacher::find($userId);
        // dd($userId);
        $lessonLog = LessonLog::where(['teachers_id' => $userId, 'status' => 'open'])->first();

        if ($lessonLog) {
            if ("true" == $lessonLog->is_club) {
                return redirect('teacher/takeclubclass');
            } else {
                //If the teacher has one lesson log, only need redirect to route takeclass and load view
                return redirect('teacher/takeclass');
            }
        }

        $chooseLessonsId = $request->session()->get('chooseLessonsId');

        $sclasses = Sclass::where(["is_graduated" => 0, "schools_id" => $teacher->schools_id])->orderBy("enter_school_year", "DESC")->get();
        $classData = [];
        foreach ($sclasses as $key => $sclass) {
            $term = Term::where(['enter_school_year' => $sclass['enter_school_year'], 'is_current' => 1])->first();
            $classData[$sclass['id']] = $term['grade_key'] . $sclass['class_title'] . "班";
        }

        $clubs = Club::where(["status" => "open", "schools_id" => $teacher->schools_id])->get();
        $clubData = [];
        foreach ($clubs as $key => $club) {
            $term = Term::where(['enter_school_year' => $sclass['enter_school_year'], 'is_current' => 1])->first();
            $clubData[$club['id']] = $club['club_title'];
        }
        $chooseLessonDesc = "";
        if (isset($chooseLessonsId)) {
            $lesson = Lesson::find($chooseLessonsId);
            $unit = Unit::find($lesson->units_id);
            $course = Course::find($unit->courses_id);
            $chooseLessonDesc = "您已选择: " . $course->title . " > " . $unit->title . " > " . $lesson->title;
        }
        // $courses = Course::orderBy("courses.created_at", "DESC")->get();
        // $coursesData = [];
        // array_push($coursesData, "请选择课程");
        // $order = 1;
        // foreach ($courses as $key => $course) {
        //     $coursesData[$course['id']] = $order . ". " . $course['title'];
        //     $order++;
        // }

        return view('teacher/home', compact('classData', 'clubData', 'chooseLessonDesc', 'chooseLessonsId'));
    }

    public function takeClass(Request $request)
    {
        
        $schoolCode = $this->getSchoolCode();
        // dd($schoolCode);
        $userId = auth()->guard('teacher')->id();
        $lessonLog = LessonLog::select('lesson_logs.id', 'lesson_logs.rethink', 'lesson_logs.sclasses_id', 'lessons.title', 'lessons.subtitle', 'sclasses.enter_school_year', 'sclasses.class_title', 'terms.grade_key', 'terms.term_segment')
        ->join("lessons", 'lessons.id', '=', 'lesson_logs.lessons_id')
        ->join("sclasses", 'sclasses.id', '=', 'lesson_logs.sclasses_id')
        ->join("terms", 'terms.enter_school_year', '=', 'sclasses.enter_school_year')
        ->where(['lesson_logs.teachers_id' => $userId, 'lesson_logs.status' => 'open', 'terms.is_current' => 1])->first();
        // dd($lessonLog);die();
        
        if ("group" == $request->get("order")) {
            $students = DB::table('students')->select('students.id', 'students.username', 'posts.cover_ext', 'posts.file_ext', 'posts.export_name', 'posts.post_code', 'comments.content', 'post_rates.rate', 'groups.order_num', 'posts.id as posts_id', DB::raw("COUNT(`marks`.`id`) as mark_num"))
            ->leftJoin('posts', 'posts.students_id', '=', 'students.id')
            ->leftJoin('post_rates', 'post_rates.posts_id', '=', 'posts.id')
            ->leftJoin('groups', 'groups.id', '=', 'students.groups_id')
            ->leftJoin('comments', 'comments.posts_id', '=', 'posts.id')
            ->leftJoin('marks', 'marks.posts_id', '=', 'posts.id')
            ->where(["students.sclasses_id" => $lessonLog['sclasses_id'], 'posts.lesson_logs_id' => $lessonLog['id']])
            ->where('students.is_lock', "!=", "1")
            ->groupBy('students.id', 'students.username', 'posts.post_code', 'comments.content', 'post_rates.rate', 'posts.id')
            ->orderBy('groups.order_num', "ASC")->get();
            
        } else {
            $students = DB::table('students')->select('students.id', 'students.username', 'posts.cover_ext', 'posts.file_ext', 'posts.export_name', 'posts.post_code', 'comments.content', 'post_rates.rate', 'posts.id as posts_id', DB::raw("COUNT(`marks`.`id`) as mark_num"))
            ->leftJoin('posts', 'posts.students_id', '=', 'students.id')
            ->leftJoin('post_rates', 'post_rates.posts_id', '=', 'posts.id')
            ->leftJoin('comments', 'comments.posts_id', '=', 'posts.id')
            ->leftJoin('marks', 'marks.posts_id', '=', 'posts.id')
            ->where(["students.sclasses_id" => $lessonLog['sclasses_id'], 'posts.lesson_logs_id' => $lessonLog['id']])
            ->where('students.is_lock', "!=", "1")
            ->groupBy('students.id', 'students.username', 'posts.post_code', 'comments.content', 'post_rates.rate', 'posts.id')
            ->orderBy(DB::raw('convert(students.username using gbk)'), "ASC")->get();
        }

        
        // dd($lessonLog);
        $unPostStudentName = [];
        
        $postedStudents = [];
        $allStudentsList = DB::table('students')->select('students.username')
        ->where(['students.sclasses_id' => $lessonLog['sclasses_id']])->where('students.is_lock', "!=", "1")->get();
        foreach ($students as $key => $student) {
            array_push($postedStudents, $student->username);
        }
        foreach ($allStudentsList as $key => $studentsName) {
            if (!in_array($studentsName->username, $postedStudents)) {
                array_push($unPostStudentName, $studentsName->username);
            }
        }
        $redirectUri = "takeclass";

        // dd($unPostStudentName);
        $allCount = count($allStudentsList);
        $py = new pinyinfirstchar();
        return view('teacher/takeclass', compact('students', 'lessonLog', 'py', 'allCount', 'unPostStudentName', 'schoolCode', 'redirectUri'));
    }

    public function takeClubClass(Request $request)
    {
        $schoolCode = $this->getSchoolCode();
        // dd($schoolCode);
        $userId = auth()->guard('teacher')->id();
        $lessonLog = LessonLog::select('lesson_logs.id', 'lesson_logs.rethink', 'lesson_logs.sclasses_id', 'clubs.club_title as class_title', 'lessons.title', 'lessons.subtitle')
        ->join("clubs", 'clubs.id', '=', 'lesson_logs.sclasses_id')
        ->join("lessons", 'lessons.id', '=', 'lesson_logs.lessons_id')
        ->where(['lesson_logs.teachers_id' => $userId, 'lesson_logs.status' => 'open'])->first();
        // dd($lessonLog);die();
        if ("group" == $request->get("order")) {
            $students = DB::table('students')->select('students.id', 'students.username', 'posts.cover_ext', 'posts.file_ext', 'posts.export_name', 'posts.post_code', 'comments.content', 'post_rates.rate', 'groups.order_num', 'posts.id as posts_id', DB::raw("COUNT(`marks`.`id`) as mark_num"))
            ->leftJoin('posts', 'posts.students_id', '=', 'students.id')
            ->leftJoin('post_rates', 'post_rates.posts_id', '=', 'posts.id')
            ->leftJoin('groups', 'groups.id', '=', 'students.groups_id')
            ->leftJoin('comments', 'comments.posts_id', '=', 'posts.id')
            ->leftJoin('marks', 'marks.posts_id', '=', 'posts.id')
            ->where(["students.sclasses_id" => $lessonLog['sclasses_id'], 'posts.lesson_logs_id' => $lessonLog['id']])
            ->where('students.is_lock', "!=", "1")
            ->groupBy('students.id', 'students.username', 'posts.post_code', 'comments.content', 'post_rates.rate', 'posts.id')
            ->orderBy('groups.order_num', "ASC")->get();
            
        } else {
            $students = DB::table('students')->select('students.id', 'students.username', 'posts.cover_ext', 'posts.file_ext', 'posts.export_name', 'posts.post_code', 'post_rates.rate', 'posts.id as posts_id', DB::raw("COUNT(`marks`.`id`) as mark_num"))
            ->leftJoin('posts', 'posts.students_id', '=', 'students.id')
            ->leftJoin('post_rates', 'post_rates.posts_id', '=', 'posts.id')
            ->leftJoin('marks', 'marks.posts_id', '=', 'posts.id')
            ->leftJoin('club_students', 'club_students.students_id', '=', 'students.id')
            ->where(["club_students.clubs_id" => $lessonLog['sclasses_id'], 'posts.lesson_logs_id' => $lessonLog['id']])
            ->where('club_students.status', "=", "open")
            ->groupBy('students.id', 'students.username', 'posts.post_code', 'post_rates.rate', 'posts.id')
            ->orderBy(DB::raw('convert(students.username using gbk)'), "ASC")->get();
        }

        
        // dd($lessonLog);
        $unPostStudentName = [];
        
        $postedStudents = [];
        $allStudentsList = Student::select('students.username', "club_students.*")
        ->join("club_students", 'club_students.students_id', '=', 'students.id')
        ->where('clubs_id', '=', $lessonLog['sclasses_id'])
        ->where('students.is_lock', "!=", "1")
        ->where('status', "=", "open")->get();

        foreach ($students as $key => $student) {
            array_push($postedStudents, $student->username);
        }
        foreach ($allStudentsList as $key => $studentsName) {
            if (!in_array($studentsName->username, $postedStudents)) {
                array_push($unPostStudentName, $studentsName->username);
            }
        }
        $redirectUri = "takeclubclass";
        // dd($unPostStudentName);
        $allCount = count($allStudentsList);
        $py = new pinyinfirstchar();
        return view('teacher/takeclass', compact('students', 'lessonLog', 'py', 'allCount', 'unPostStudentName', 'schoolCode', "redirectUri"));
    }

    public function imgPreview(Request $request) {
        $postCode = $request->get("postCode");
        return view('teacher/imgPreview', compact("postCode"));
    }

    public function updateRate(Request $request)
    {
        $this->validate($request, [
            'posts_id' => 'required',
            'rate' => 'required',
        ]);

        $id = Auth::guard('teacher')->id();
        $posts_id = $request->get('posts_id');
        $rate = $request->get('rate');
        $postRate = PostRate::where(['teachers_id' => $id, "posts_id" => $posts_id])->first();
        if (isset($postRate)) {
            $postRate->rate = $rate;
            if ($postRate->update()) {
                return "true";
            } else {
                return "false";
            }
        } else {
            $postRate = new PostRate();
            $postRate->teachers_id = $id;
            $postRate->posts_id = $posts_id;
            $postRate->rate = $rate;
            if ($postRate->save()) {
                return "true";
            } else {
                return "false";
            }
        }
    }

    public function updateRateByCode(Request $request)
    {
        $this->validate($request, [
            'post_code' => 'required',
            'rate' => 'required',
        ]);

        $id = Auth::guard('teacher')->id();
        $post_code = $request->get('post_code');
        $post = Post::where(['post_code' => $post_code])->first();
        $rate = $request->get('rate');
        $postRate = PostRate::where(['teachers_id' => $id, "posts_id" => $post->id])->first();
        if (isset($postRate)) {
            $postRate->rate = $rate;
            if ($postRate->update()) {
                return "true";
            } else {
                return "false";
            }
        } else {
            $postRate = new PostRate();
            $postRate->teachers_id = $id;
            $postRate->posts_id = $post->id;
            $postRate->rate = $rate;
            if ($postRate->save()) {
                return "true";
            } else {
                return "false";
            }
        }
    }

    public function getLessonLog(Request $request)
    {   
        $teachersId = Auth::guard('teacher')->id();
        $lessonLog = LessonLog::where(['lessons_id' => $request->input('lessonsId'), 
                                    'sclasses_id' => $request->input('sclassesId'), 
                                    'is_club' => $request->input('isClub'), 
                                    'teachers_id' => $teachersId])->first();

        if (isset($lessonLog)) {
            $postNum = Post::where(['lesson_logs_id' => $lessonLog->id])->count();

            return $postNum;
        } else {
            return "false";
        }
    }

    public function getPostRate(Request $request)
    {
        $postRate = PostRate::where(['posts_id' => $request->input('posts_id')])->first();

        if (isset($postRate)) {
            return $postRate['rate'];
        } else {
            return "false";
        }
    }

    public function getOnePostByCode(Request $request)
    {
        $post = Post::select("posts.*", "lessons.title", "lessons.subtitle", "students.username", "students.id as students_id")
                ->where("posts.post_code", "=", $request->input('post_code'))
                ->join('students', 'students.id', '=', "posts.students_id")
                ->join('lesson_logs', 'lesson_logs.id', '=', "posts.lesson_logs_id")
                ->join('lessons', 'lessons.id', '=', "lesson_logs.lessons_id")->first();

        $marks = Mark::where(["posts_id" => $post->id, "state_code" => 1])->get();
        $markNum = isset($marks)?count($marks):0;

        $post["markNum"] = $markNum;

        $studentsId = Auth::guard("student")->id();
        $mark = Mark::where(["posts_id" => $post->id, "students_id" => $studentsId, "state_code" => 1])->first();

        $post["isMarkedByMyself"] = isset($mark)?"true":"false";

        $postRate = PostRate::where(['posts_id' => $post->id])->first();
        $post["postRate"] = isset($postRate)?$postRate['rate']:"";

        return $post;  
    }

    public function getPost(Request $request)
    {
        $middir = "/posts/" . $this->getSchoolCode() . "/";
        $post = Post::where(['id' => $request->input('posts_id')])->orderBy('id', 'desc')->first();
        $imgTypes = ['jpg', 'jpeg', 'bmp', 'gif', 'png'];
        $docTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        if (isset($post)) {
            if (in_array($post->cover_ext, $imgTypes)) {
                return ["filetype"=>"img", "url" => getThumbnail($post['storage_name'], 801, 601, $this->getSchoolCode(), 'background', $post['cover_ext'])];
            } elseif (in_array($post->cover_ext, $docTypes)) {
                return ["filetype"=>"doc", "url" => env('APP_URL'). $middir .$post->storage_name];
            } elseif ("sb2" == $post->cover_ext) {
                return ["filetype"=>"sb2", "url" => env('APP_URL'). $middir .$post->storage_name];
            }
            // $file = Storage::disk('uploads')->get($post->storage_name)->getPath();
                // $post->storage_name = env('APP_URL')."/posts/".$post->storage_name;
            // return getThumbnail($post['storage_name'], 800, 600, 'fit');
        
        } else {
            return "false";
        }
    }

    public function getLessonPostPerSclass(Request $request)
    {
        $id = \Auth::guard("teacher")->id();
        $lessons_id = $request->get('lessons_id');
        $lessonLogs = LessonLog::leftJoin('sclasses', function($join) {
            $join->on('sclasses.id', '=', 'lesson_logs.sclasses_id');
        })->where(['lessons_id' => $lessons_id, 'teachers_id' => $id])->orderBy('sclasses.id', 'asc')->selectRaw("lesson_logs.id as lesson_logs_id, sclasses.class_title, sclasses.enter_school_year")->get();
        $newLessonLogs = [];
        $students = [];
        foreach ($lessonLogs as $key => $lessonLog) {
            $students = Student::leftJoin('lesson_logs', function($join) {
                $join->on('students.sclasses_id', '=', 'lesson_logs.sclasses_id');
            })->where(["lesson_logs.id" => $lessonLog['lesson_logs_id']])->selectRaw("*, students.id as students_id")->get();

            $postData = [];
            foreach ($students as $key => $student) {
                $post = Post::where(['students_id' => $student->students_id, 'lesson_logs_id' => $lessonLog['lesson_logs_id']])->first();
                $postRate = PostRate::where(['posts_id' => $post['id']])->first();
                $rate = isset($postRate)?$postRate['rate']:"";

                $comment = Comment::where(['posts_id' => $post['id']])->first();
                $hasComment = isset($comment)?"true":"false";
                $marksNum = Mark::where(['posts_id' => $post['id']])->count();

                $postData[$student->students_id] = ['post' => $post, 'rate' => $rate, 'hasComment' => $hasComment, 'marksNum' => $marksNum];
            }
            $newLessonLogs[] = ['students' => $students, 'postData' => $postData, 'class_title' => $lessonLog['class_title'], 'enter_school_year' => $lessonLog['enter_school_year'], 'lesson_logs_id' => $lessonLog['lesson_logs_id']];
        }
// dd($newLessonLogs);die();
        return $this->lessonHistoryHtmlCreate($newLessonLogs);
        // $post = Post::where(['students_id' => $student['id'], 'lesson_logs_id' => $lessonLog['id']])->first();
        //有哪些班，第一个班的详细数据
    }

    public function lessonHistoryHtmlCreate($lessonLogs)
    {
        $py = new pinyinfirstchar();
        $returnHtml = "<ul class='nav nav-tabs'>";
            foreach ($lessonLogs as $key => $lessonLog) {
// dd($lessonLog);
                $returnHtml .= "<li><a href='#show-class" . $lessonLog["lesson_logs_id"] . "' data-toggle='tab'>" . $lessonLog["enter_school_year"]."级".$lessonLog["class_title"] . "班</a></li>";
            }
        $returnHtml .= "</ul>";
        $returnHtml .= "<div class='tab-content'>";
            foreach ($lessonLogs as $key => $lessonLog) {
                $students = $lessonLog['students'];
                $active = (0 == $key)?"in active":"";
                $html = View::make('teacher.partials.studentlist', compact('students', 'py'))->render();
                $returnHtml .= "<div class='tab-pane fade " . $active . "' id='show-class" . $lessonLog["lesson_logs_id"] . "'>" . $html . "</div>";
            }
        $returnHtml .= "</div>";
        // dd($returnHtml);
        return $returnHtml;
    }

    public function getReset()
    {
        return view('teacher.login.reset');
    }

    public function postReset(Request $request)
    {
        $oldpassword = $request->input('oldpassword');
        $password = $request->input('password');
        $data = $request->all();
        $rules = [
            'oldpassword'=>'required|between:6,20',
            'password'=>'required|between:6,20|confirmed',
        ];
        $messages = [
            'required' => '密码不能为空',
            'between' => '密码必须是6~20位之间',
            'confirmed' => '新密码和确认密码不匹配'
        ];
        $validator = Validator::make($data, $rules, $messages);
        $user = Auth::guard('teacher')->user();
        $validator->after(function($validator) use ($oldpassword, $user) {
            if (!\Hash::check($oldpassword, $user->password)) {
                $validator->errors()->add('oldpassword', '原密码错误');
            }
        });
        if ($validator->fails()) {
            return back()->withErrors($validator);  //返回一次性错误
        }
        $user->password = bcrypt($password);
        $user->save();
        Auth::guard('teacher')->logout();
        return redirect('/teacher/login');
    }

    public function getSchoolCode()
    {
      $teacher = Teacher::find(Auth::guard("teacher")->id());
      $school = School::where('schools.id', '=', $teacher->schools_id)->first();
      return $school->code;
    }

    public function sb3player(Request $request)
    {
        $postCode = $request->get("postCode");
        return view('teacher/sb3player', compact("postCode"));
    }
}
