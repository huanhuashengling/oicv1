<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Student;
use App\Models\Post;
use App\Models\Mark;
use App\Models\Comment;
use App\Models\PostRate;
use App\Models\Lesson;
use App\Models\LessonLog;
use App\Models\Term;
use App\Models\Sclass;
use App\Models\School;
use App\Models\ClubStudent;
use App\Models\Club;
use \Auth;

use MarkdownEditor;
use \DB;

class PostController extends Controller
{
    public function index()
    {
        $id = \Auth::guard("student")->id();
        $student = Student::find($id);
        $sclass = Sclass::find($student->sclasses_id);
        $terms = Term::where(['enter_school_year' => $sclass->enter_school_year])->get();
        return view('student/posts', compact('terms'));
    }

    public function getPostsByTerm(Request $request) {
        $termsId = $request->get('termsId');

        $term = Term::find($termsId);
        $middir = "/posts/" . $this->getSchoolCode() . "/";
        $imgTypes = ['jpg', 'jpeg', 'bmp', 'gif', 'png'];

        $id = \Auth::guard("student")->id();
        $student = Student::find($id);
        $sclass = Sclass::find($student->sclasses_id);

        $from = date('Y-m-d', strtotime($term->from_date)); 
        $to = date('Y-m-d', strtotime($term->to_date));
        $tLessonLogs = LessonLog::select('lesson_logs.id', 'lesson_logs.is_club', 'lessons.title', 'lessons.subtitle', 'lessons.id as lessons_id')
        ->join('lessons', 'lessons.id', '=', "lesson_logs.lessons_id")
        ->where(['sclasses_id' => $student['sclasses_id']])
        ->whereBetween('lesson_logs.created_at', array($from, $to))->get();


        $clubStudent = ClubStudent::where("students_id", "=", $id)
        ->where("status", "=", "open")->first();
        $tClubLessonLogs = [];
        if ($clubStudent) {
          $sclassesId = $clubStudent->clubs_id;
          $tClubLessonLogs = LessonLog::select('lesson_logs.id', 'lesson_logs.is_club', 'lessons.title', 'lessons.subtitle', 'lessons.id as lessons_id')
          ->join('lessons', 'lessons.id', '=', "lesson_logs.lessons_id")
          ->where('lesson_logs.sclasses_id', "=", $clubStudent->clubs_id)
          ->where('lesson_logs.is_club', "=", "true")
          ->get();
        }

        $allLessonLogs = $tLessonLogs->concat($tClubLessonLogs);

        $allMarkNum = 0;
        $allEffectMarkNum = 0;
        $allYouNum = 0;
        $allYouJiaNum = 0;
        $postData = [];
        foreach ($allLessonLogs as $key => $lessonLog) {
            $post = Post::where(['lesson_logs_id' => $lessonLog->id, "students_id" => $id])->orderBy('id', 'desc')->first();
            $postState = "未交";
            $rateScore = 0;
            $markNum = 0;
            $validMarkNum = 0;

            if (isset($post)) {

                $postState = "已交";
                $postRate = PostRate::where(['posts_id' => $post['id']])->first();
                // $rate = isset($postRate)?$postRate['rate']:"";
                if (isset($postRate)) {
                    if (2 == $postRate['rate']) {
                        $postState = "优";
                        $rateScore = 8;
                        $allYouNum ++;
                    } else if(1 == $postRate['rate']) {
                        $postState = "优+";
                        $allYouJiaNum ++;
                        $rateScore = 9;
                    }
                }
                $markNum = Mark::select('students.username')
                ->leftJoin("students", 'students.id', '=', 'marks.students_id')
                ->where(['posts_id' => $post['id'], 'state_code' => 1])->count();
                $validMarkNum = ($markNum>4)?4:$markNum;
                $allMarkNum += $markNum;
                $allEffectMarkNum += ($markNum>4)?4:$markNum;
                // dd($marks);
            }

            $postData[] = ["lesson_title" => $lessonLog->title, 
                            'postType' => ("true" == $lessonLog->is_club)?"社团":"课堂", 
                            'postState' => $postState, 
                            'rateScore' => $rateScore,
                            'score' => $rateScore + $validMarkNum/2,
                            'lessonLog' => $lessonLog, 
                            'validMarkNum' => $validMarkNum,
                            'markNum' => $markNum];
        }
        $allScore = $allEffectMarkNum * 0.5 + $allYouNum * 8 + $allYouJiaNum * 9;
        if ($allScore < 60) {
            $levelStr = "不合格";
        } else if ($allScore < 80) {
            $levelStr = "合格";
        } else if ($allScore < 95) {
            $levelStr = "优秀";
        } else {
            $levelStr = "非常优秀";
        }
        return $postData;
    }

    public function getSchoolCode()
    {
      $student = Student::find(Auth::guard("student")->id());

      $school = School::leftJoin('sclasses', 'sclasses.schools_id', '=', "schools.id")
              ->where('sclasses.id', '=', $student->sclasses_id)->first();
      return $school->code;
    }

    public function sb3player(Request $request)
    {
        $postCode = $request->get("postCode");
        return view('student/sb3player', compact("postCode"));
    }

    public function imgPreview(Request $request)
    {
        $postCode = $request->get("postCode");
        return view('student/imgPreview', compact("postCode"));
    }

    public function getOnePostByCode(Request $request)
    {
        $post = Post::select("posts.*", "lessons.title", "lessons.subtitle", "students.username", "students.id as students_id")
                ->where("posts.post_code", "=", $request->input('post_code'))
                ->join('students', 'students.id', '=', "posts.students_id")
                ->join('lesson_logs', 'lesson_logs.id', '=', "posts.lesson_logs_id")
                ->join('lessons', 'lessons.id', '=', "lesson_logs.lessons_id")->first();

        $marks = Mark::where(["posts_id" => $post->id, "state_code" => 1])->get();
        $markNum = 0;
        $post["markNames"] = "";
        if (isset($marks)) {
            $markNum = count($marks);
            foreach ($marks as $key => $mark) {
                $student = Student::find($mark->students_id);
                $post["markNames"] .= $student->username . ", ";
            }
        }

        $post["markNum"] = $markNum;

        $studentsId = Auth::guard("student")->id();
        $mark = Mark::where(["posts_id" => $post->id, "students_id" => $studentsId, "state_code" => 1])->first();

        $post["isMarkedByMyself"] = isset($mark)?"true":"false";

        $postRate = PostRate::where(['posts_id' => $post->id])->first();
        $post["postRate"] = isset($postRate)?$postRate['rate']:"";

        return $post;
    }

    public function updateMarkByCode(Request $request)
    {
        $studentsId = Auth::guard("student")->id();
        $postsCode =  $request->input('postCode');
        $stateCode =  $request->input('stateCode');
        $post = Post::where("post_code", $postsCode)->first();
        
        $mark = Mark::where(["posts_id" => $post->id, "students_id" => $studentsId])->first();
        if (isset($mark)) {
            $mark->state_code = $stateCode;
            if ($mark->save()) {
                return "true";
            }
        } else {
          $mark = new Mark();
          $mark->posts_id = $post->id;
          $mark->students_id = $studentsId;
          $mark->state_code = 1;
          if ($mark->save()) {
            return "true";
          }
        }
    }

}
