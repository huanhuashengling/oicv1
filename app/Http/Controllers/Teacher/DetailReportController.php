<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Sclass;
use App\Models\Club;
use App\Models\LessonLog;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Post;
use App\Models\PostRate;
use App\Models\Comment;
use App\Models\Group;
use App\Models\Mark;
use App\Models\Term;
use App\Models\SendMailList;

use App\Mail\PostReport;
use Carbon\Carbon;
use \DB;

class DetailReportController extends Controller
{
    
    
    public function index() 
    {
        $userId = auth()->guard('teacher')->id();
        $teacher = Teacher::find($userId);
        $sclasses = Sclass::where(["is_graduated" => 0, "schools_id" => $teacher->schools_id])->get();
        $clubs = Club::where(["status" => "open", "schools_id" => $teacher->schools_id])->get();
        return view('teacher/detailReport/index', compact('sclasses', 'clubs'));
    }
    public function getSclassTermsList(Request $request) {
        $sclassesId = $request->get('sclassesId');
        $sclass = Sclass::find($sclassesId);
        $terms = Term::where("terms.enter_school_year", '=', $sclass->enter_school_year)->get();
        return $this->buildTermsSelectionHtml($terms);
    }
    public function buildTermsSelectionHtml($terms) {
        $resultHtml = "";
        foreach ($terms as $key => $term) {
            $class = $term->is_current?"selected":"";
            $hint = $term->is_current?"（本学期）":"";
            $resultHtml .= "<option value='" . $term->id . "' " . $class . ">" . $term->grade_key . "年" . $term->term_segment . "期".$hint."</option>";
        }
        return $resultHtml;
    }

    public function report(Request $request) {
        $sclassesId = $request->get('sclassesId');
        $termsId = $request->get('termsId');
        // dd($sclassesId."-".$termsId);
        $term = Term::find($termsId);
        $from = date('Y-m-d', strtotime($term->from_date)); 
        $to = date('Y-m-d', strtotime($term->to_date));
        $lessonLogCount = LessonLog::where("lesson_logs.sclasses_id", '=', $sclassesId)->whereBetween('lesson_logs.created_at', array($from, $to))->count();
        $students = Student::where("sclasses_id", "=", $sclassesId)->where("students.is_lock", "!=", "1")->get();
        // dd($students);
        // order username postednum unpostnum rate1num rate2num rate3num rate4num commentnum marknum scorecount
        $dataset = [];
        foreach ($students as $key => $student) {
            $tData = [];
            $tData['users_id'] = $student->id;
            $tData['username'] = $student->username;
            
            $tData['lessonLogs'] = LessonLog::select([
                                    'lessons.title AS lesson_title',
                                    DB::raw("CASE 
                                        WHEN posts.id IS NULL THEN '未交' 
                                        WHEN post_rates.rate IS NULL THEN '不合格' 
                                        WHEN post_rates.rate = 1 THEN '优+' 
                                        WHEN post_rates.rate = 2 THEN '优' 
                                        ELSE 3 
                                    END AS status"),
                                    'lesson_logs.created_at AS lesson_time',
                                    'lesson_logs.created_at AS lesson_time',
                                    // 新增字段：检查是否在近24小时内有更新
                                    DB::raw("CASE 
                                        WHEN EXISTS (
                                            SELECT 1 FROM posts 
                                            WHERE posts.lesson_logs_id = lesson_logs.id 
                                            AND posts.students_id = {$student->id} 
                                            AND posts.updated_at >= DATE_SUB(NOW(), INTERVAL 124 HOUR)
                                        ) THEN 1 
                                        ELSE 0 
                                    END AS status_new")
                                ])
                                ->leftJoin('lessons', 'lesson_logs.lessons_id', '=', 'lessons.id')
                                ->leftJoin('sclasses', 'lesson_logs.sclasses_id', '=', 'sclasses.id') // 课程日志班级与班级表关联
                                ->leftJoin('posts', function ($join) use ($student) {
                                    $join->on('lesson_logs.id', '=', 'posts.lesson_logs_id')
                                         ->where('posts.students_id', $student->id); // 指定学生 ID
                                })
                                ->leftJoin('post_rates', 'posts.id', '=', 'post_rates.posts_id')
                                ->where('lesson_logs.sclasses_id', $student->sclasses_id) // 直接使用 $student->sclasses_id 关联班级
                                ->whereBetween('lesson_logs.created_at', [$from, $to])
                                ->orderBy('lesson_logs.created_at', 'ASC')
                                ->get();
            $dataset[] = $tData;
        }
        return $dataset;
    }

}
