<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\LessonLog;
use App\Models\Term;
use App\Models\Sclass;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Club;
use \DB;
use \Auth;
use App\Http\Requests\LessonLogRequest;
use App\Libaries\pinyinfirstchar;

class LessonLogController extends Controller
{
    public function store(Request $request)
    {
        //TODO DO not readd the same lessonlog with the sanme teacher classe and lesson
        $teachersId = \Auth::guard("teacher")->id();
        $sclassesId = $request->get('sclassesId');
        $isClub = $request->get('isClub');
        $lessonsId = $request->session()->get('chooseLessonsId');

        if (0 == $sclassesId) {
            return redirect()->back()->withInput()->withErrors('请选择班级！');
        }
        if (!isset($lessonsId)) {
            return redirect()->back()->withInput()->withErrors('请选择课程！');
        }
        $oldlLessonLog = LessonLog::where(['teachers_id' => $teachersId, "sclasses_id" => $sclassesId, "lessons_id" => $lessonsId, "is_club" => $isClub])->first();
            


        if($oldlLessonLog) {
            $request->session()->forget('chooseLessonsId');
            $oldlLessonLog->status = 'open';
            $oldlLessonLog->update();
            if ("true" == $isClub) {
                return redirect('teacher/takeclubclass');
            } else {
                return redirect('teacher/takeclass');
            }
        }

        
        $lessonLog = new LessonLog();

        $lessonLog->teachers_id = \Auth::guard("teacher")->id();
        $lessonLog->sclasses_id = $sclassesId;
        $lessonLog->is_club = $isClub;
        $lessonLog->lessons_id = $lessonsId;
        $lessonLog->rethink = "";
        $lessonLog->status = 'open';
        // dd($lessonLog);die();
        if ($lessonLog->save()) {
            $request->session()->forget('chooseLessonsId');
            if ("true" == $isClub) {
                return redirect('teacher/takeClubClass');
            } else {
                return redirect('teacher/takeclass');
            }
        } else {
            return redirect()->back()->withInput()->withErrors('保存失败！');
        }
    }

    public function update(Request $request)
    {
        $lessonLogId = $request->get('lessonLogId');
        $action = $request->get('action');
        if ("close-lesson-log" == $action) {
            $lessonLog = LessonLog::where(['id' => $lessonLogId])->first();
            $lessonLog->status = 'close';
        }
        if ($lessonLog->update()) {
            return "true";
        } else {
            return "false";
        }
    }

    public function updateRethink(Request $request)
    {
        $lessonLogId = $request->get('lessonLogId');
        $rethink = $request->get('rethink');
        $lessonLog = LessonLog::where(['id' => $lessonLogId])->first();
        $lessonLog->rethink = $rethink;
        if ($lessonLog->update()) {
            return "true";
        } else {
            return "false";
        }
    }

    public function listLessonLog()
    {
        $teacher = Teacher::find(Auth::guard("teacher")->id());

        $clubData = Club::select("id", "club_title as class_title", "term_desc")
        ->where(["schools_id" => $teacher->schools_id])->get();
        //TODO
        // $terms = Term::where(["is_current" => 1])->orderBy("enter_school_year", "desc")->get();
        $terms = Term::orderBy("enter_school_year", "desc")->get();
        return view('teacher/lesson/lesson-log', compact('clubData', 'terms'));
    }

    public function loadSclassSelection(Request $request)
    {
        $term = Term::find($request->get('terms_id'));
        $teachers_id = \Auth::guard("teacher")->id();
        $teacher = Teacher::find($teachers_id);
        $sclasses = Sclass::where(["enter_school_year" => $term->enter_school_year, 'schools_id' => $teacher->schools_id])
        ->orderBy("enter_school_year", "desc")->get();
        return $this->buildSclassSelctionHhtml($sclasses);
    }

    public function loadLessonLogSelection(Request $request)
    {
        $matchThese = [];
        $term = Term::find($request->get('terms_id'));
            $from = date('Y-m-d', strtotime($term->from_date)); 
            $to = date('Y-m-d', strtotime($term->to_date));
        if($request->get('sclassesId')) {
            $sclass = Sclass::find($request->get('sclassesId'));
            // $matchThese = ['field' => 'value', 'another_field' => 'another_value', ...];
            $matchThese = ["lesson_logs.is_club" => "false"];

        } else {
            //TODO
            $sclass = Club::where(['status' => 'open'])->find($request->get('clubsId'));
            // $sclass = Club::find($request->get('clubsId'));
            $matchThese = ["lesson_logs.is_club" => "true"];
        }

        $lessonLogs = LessonLog::select('lesson_logs.id', 'lessons.title', 'lessons.subtitle', 'teachers.username', 'lesson_logs.updated_at', DB::raw("COUNT(`posts`.`id`) as post_num"))
            ->leftJoin('lessons', function($join){
              $join->on('lessons.id', '=', 'lesson_logs.lessons_id');
            })
            ->leftJoin('teachers', function($join){
              $join->on('teachers.id', '=', 'lesson_logs.teachers_id');
            })
            ->leftJoin('posts', function($join){
              $join->on('posts.lesson_logs_id', '=', 'lesson_logs.id');
            })
            ->groupBy('lesson_logs.id', 'lessons.title', 'lessons.subtitle', 'teachers.username', 'lesson_logs.updated_at')
            ->whereBetween('lesson_logs.created_at', array($from, $to))
            ->where($matchThese)
            ->where(['lesson_logs.sclasses_id' => $sclass->id])->get();

        return $this->buildLessonLogSelectionHtml($lessonLogs);
        // return json_encode($term);
    }

    public function getPostDataByTermAndSclass(Request $request)
    {
        $lessonLog = LessonLog::find($request->get('lessonlogsId'));

        $students = DB::table('students')->select('students.id', 'students.username', 'posts.file_ext','posts.cover_ext', 'posts.export_name', 'posts.post_code', 'post_rates.rate', 'posts.id as posts_id', DB::raw("COUNT(`marks`.`id`) as mark_num"))
        ->leftJoin('posts', 'posts.students_id', '=', 'students.id')
        ->leftJoin('post_rates', 'post_rates.posts_id', '=', 'posts.id')
        ->leftJoin('comments', 'comments.posts_id', '=', 'posts.id')
        ->leftJoin('marks', 'marks.posts_id', '=', 'posts.id')
        ->where(['posts.lesson_logs_id' => $lessonLog['id']])
        ->where('students.is_lock', "!=", "1")
        ->groupBy('students.id', 'students.username', 'posts.export_name', 'post_rates.rate','posts.cover_ext', 'posts.post_code', 'posts.id')
        ->orderBy(DB::raw('convert(students.username using gbk)'), "ASC")->get();
        
        $unPostStudentNameStr = "未交名单:";
        
        $postedStudents = [];

        if ("true" == $lessonLog->is_club) {
            $allStudentsList = Student::select("students.id", "students.username")
            ->join("club_students", "club_students.students_id", "=", "students.id")
            ->where("club_students.clubs_id", "=", $lessonLog->sclasses_id)
            ->get();
        } else {
            $allStudentsList = DB::table('students')->select('students.username')
            ->where(['students.sclasses_id' => $lessonLog['sclasses_id']])->where('students.is_lock', "!=", "1")->get();
        }

        
        foreach ($students as $key => $student) {
            array_push($postedStudents, $student->username);
        }
        $unpostCount = 0;
        foreach ($allStudentsList as $key => $studentsName) {
            if (!in_array($studentsName->username, $postedStudents)) {
                $unPostStudentNameStr .= " " . ($unpostCount + 1). ". " . $studentsName->username;
                $unpostCount++;
            }
        }
        // dd($unPostStudentName);
        $allCount = count($allStudentsList);
        // return view('teacher/takeclass', compact('students', 'lessonLog', 'py', 'allCount', 'unPostStudentName'));
        // $unpostCount = 0;
        $postedCount = $allCount-$unpostCount;
        $cardHeadStr = "(全部".$allCount.")"." "."(已交".$postedCount.")"." "."(未交".$unpostCount.")";
        
        $returnHtml = "<div class='card card-success'><div class='card-header'>".$cardHeadStr."</div><div class='card-body'><div class='row row-cols-1 row-cols-md-5 g-2'>" . $this->buildStudentPostsList($students) . " </div></div><div class='card-footer'>".$unPostStudentNameStr."</div></div>";
            
        $returnHtml .= "<input type='hidden' id='lesson-log-id' value='" . $lessonLog['id'] . "'>";
        //$returnHtml .= "<div class='card card-default'><div class='card-heading'><div class='card-title'><button class='btn btn-default' id='update-rethink'>点击记录教学反思</button></div></div><div class='card-body'><textarea class='form-control' rows='5' id='rethink' name='rethink'>" . $lessonLog['rethink'] . "</textarea></div></div>";


        return $returnHtml;
    }

    public function buildStudentPostsList($students)
    {
        $returnHtml = "";
        $schoolCode = $this->getSchoolCode();
        $py = new pinyinfirstchar();
        foreach ($students as $student) {
            if (isset($student->rate)) {
                $ratestr = $student->rate . "/";
                if ("优+" == $student->rate) {
                    $postCss = "alert-danger";
                } else if("优" == $student->rate) {
                    $postCss = "alert-success";
                } else {
                    $postCss = "alert-info";
                }
            } else {
                $ratestr = "";
                $postCss = "alert-default";
            }
            $commentStr = "";
            if (isset($student->content) && "" != $student->content) {
                $commentStr = "/评";
            }
            $marksNum = isset($student->mark_num)?$student->mark_num:"";
            if ("sb3" == $student->file_ext) {
                $returnHtml .= "<div class='col'><div class='card h-100'><a href='/teacher/sb3player?postCode=" . $student->post_code . "&schoolCode=" . $schoolCode . "' target='_blank' style='padding: 5px;'><img class='img-fluid card-img-top center-block' value='". $student->posts_id . "' src='/posts/" . $schoolCode . "/" . $student->post_code . "_c.". $student->cover_ext ."' alt=''></a><div class='card-footer'><div class='row'><div class='col'>" . $py->getFirstchar($student->username) . " " . $student->username . "</div><div class='col'><span class='text-right'> " . $ratestr . "" . $marksNum . $commentStr . "</span></div></div></div></div></div>";
            } else {
                $returnHtml .= "<div class='col'><div class='card h-100'><a href='/teacher/imgPreview?postCode=" . $student->post_code . "&schoolCode=" . $schoolCode . "' target='_blank' style='padding: 5px;'><img class='img-fluid card-img-top center-block' value='". $student->posts_id . "' src='/posts/" . $schoolCode . "/" . $student->post_code . "_c.". $student->cover_ext ."' alt=''></a><div class='card-footer'><div class='row'><div class='col'>" . $py->getFirstchar($student->username) . " " . $student->username . "</div><div class='col'><span class='text-right'> " . $ratestr . "" . $marksNum . $commentStr . "</span></div></div></div></div></div>";
            }
            
        }
        return $returnHtml;
    }

    public function buildSclassSelctionHhtml($sclasses)
    {
        $returnHtml = "<option>选择班级</option>";
        foreach ($sclasses as $key => $sclass) {
            $returnHtml .= "<option value='" . $sclass['id'] . "'>" . $sclass['class_title'] . "班</option>";
        }

        return $returnHtml;
    }

    public function buildLessonLogSelectionHtml($lessonlogs) {
        $returnHtml = "<option>选择上课记录</option>";
        foreach ($lessonlogs as $key => $lessonLog) {
            $d= date("Y-m-d", strtotime($lessonLog['updated_at']));
            // $date = new DateTime($lessonLog['updated_at'])->format("Y-m-d");
            $returnHtml .= "<option value='" . $lessonLog['id'] . "'>" . ($key+1) . ". " . $lessonLog['title'] ."(". $lessonLog['subtitle'] .")－". $lessonLog['username'] . "－".$lessonLog['post_num'] . "份－" . $d . "</option>";
        }

        return $returnHtml;
    }

    public function getSchoolCode()
    {
      $teacher = Teacher::find(Auth::guard("teacher")->id());
      $school = School::where('schools.id', '=', $teacher->schools_id)->first();
      return $school->code;
    }
}
