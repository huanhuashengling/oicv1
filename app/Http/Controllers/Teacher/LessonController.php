<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\LessonLog;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\Unit;
use \Auth;
use \DB;
use MarkdownEditor;
use Redirect;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $uId = $request->get('uId');
        // $uId = Route::current()->getParameter('id');
        $unit = Unit::find($uId);
        $unit->course = Course::find($unit->courses_id);
        // dd($unit);
        return view('teacher/lesson/index', compact("uId", "unit"));
    }

    public function create(Request $request)
    {
        $coursesId = $request->get("cId");
        $unitsId = $request->get("uId");
        $courses = Course::get();
        $units = Unit::get();
        return view('teacher/lesson/create', compact("coursesId", "unitsId", "courses", "units"));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|unique:lessons|max:255',
            'subtitle' => 'required',
            'courses_id' => 'required',
            'units_id' => 'required',
            'allowPostFileTypes' => 'required',
        ]);

        $lesson = new Lesson;
        $lesson->units_id = $request->get('units_id');
        $lesson->courses_id = $request->get('courses_id');
        $lesson->title = $request->get('title');
        $lesson->subtitle = $request->get('subtitle');
        $lesson->allow_post_file_types = $request->get('allowPostFileTypes');
        $lesson->help_md_doc = $request->get('test-editormd');
        $lesson->teachers_id = Auth::guard('teacher')->id();;

        if ($lesson->save()) {
            return redirect('teacher/lesson?uId=' . $request->get('units_id'));
        } else {
            return redirect()->back()->withInput()->withErrors('新建失败！');
        }
    }

    public function edit(Request $request, $id)
    {
        $lesson = Lesson::find($id);
        $coursesId = $request->get("cId");
        $unitsId = $request->get("uId");
        $courses = Course::get();
        $units = Unit::get();
        // var_dump($courses);
        // var_dump($units);
        // dd($lesson);
        return view('teacher/lesson/edit', compact("lesson", "courses", "units", "coursesId", "unitsId"));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|unique:lessons,title,'.$id.'|max:255',
            'subtitle' => 'required',
            'courses_id' => 'required',
            'units_id' => 'required',
            'allow_post_file_types' => 'required',
        ]);
// echo MarkdownEditor::parse($request->get('test-editormd'));
        // dd($request->get('test-editormd'));
        $lesson = Lesson::find($id);
        $lesson->units_id = $request->get('units_id');
        $lesson->courses_id = $request->get('courses_id');
        $lesson->title = $request->get('title');
        $lesson->allow_post_file_types = $request->get('allow_post_file_types');
        $lesson->subtitle = $request->get('subtitle');
        // $lesson->help_md_doc = $request->get('test-editormd');
        $lesson->help_md_doc = $request->get('test-editormd');

        if ($lesson->save()) {
            return redirect('teacher/lesson?uId=' . $request->get('units_id'));
        } else {
            return redirect()->back()->withInput()->withErrors('修改失败！');
        }
    }

    public function destroy($id)
    {
        Lesson::find($id)->delete();
        return redirect()->back()->withInput()->withErrors('删除成功！');
    }

    public function deleteLesson(Request $request)
    {
        if (Lesson::find($request->get('lessonsId'))->delete()) {
            return "true";
        } else {
            return "false";
        }
    }

    public function uploadMDImage()
    {
        // $data = MarkdownEditor::uploadImgFile('uploads/md');

        // return json_encode($data);
    }

    public function ajaxSearchTopics()
    {
        $lessons = Lesson::all();
        $helpMDDoc = [];
        foreach ($lessons as $key => $lesson) {
            $helpMDDoc[$lesson->subtitle] = $lesson->help_md_doc;
        }
        return $helpMDDoc;
    }

    public function getLesson(Request $request)
    {
        $lessonsId = $request->get('lessonsId');
        $lesson = Lesson::find($lessonsId);
        if(strpos($lesson->help_md_doc, "10.115.3.153")) {
          $lesson->help_md_doc = str_replace("10.115.3.153", $_SERVER['HTTP_HOST'], $lesson->help_md_doc);
        }

        if("10.115.3.153" != $_SERVER['HTTP_HOST']) {
            if(strpos($lesson->help_md_doc, "10.115.3.3:8080")) {
                $lesson->help_md_doc = str_replace("10.115.3.3:8080", "kiftd.workc.cc:7002", $lesson->help_md_doc);
            }
        }
      
        $lesson->help_md_doc = MarkdownEditor::parse($lesson->help_md_doc);
        return $lesson;
    }

    public function getLessonList(Request $request)
    {
        $unitsId = $request->get('unitsId');

        $lessons = Lesson::select('lessons.id', 'lessons.title', 'lessons.is_open', 'lessons.subtitle', 'lessons.updated_at', 'teachers.username', 'units.title as unit_title', 'courses.title as course_title', DB::raw("COUNT(`lesson_logs`.`id`) as lesson_log_num"))
            ->join("teachers", "teachers.id", "=", "lessons.teachers_id")
            ->leftJoin("lesson_logs", "lesson_logs.lessons_id", "=", "lessons.id")
            ->join("units", "units.id", "=", "lessons.units_id")
            ->join("courses", "courses.id", "=", "units.courses_id")
            ->groupBy('lessons.id', 'lessons.title', 'lessons.subtitle', 'lessons.updated_at', 'teachers.username', 'unit_title', 'course_title')
            ->where("lessons.units_id", "=", $unitsId)
            ->orderBy('lessons.updated_at', 'DESC')
            ->get();
        return $lessons;
    }

    public function openLesson(Request $request)
    {
        $lesson = Lesson::find($request->get('lessons_id'));
        $lesson->is_open = 1;

        if ($lesson->save()) {
            return "true";
        } else {
            return "false";
        }
    }

    public function chooseLesson(Request $request)
    {
        $userId = auth()->guard('teacher')->id();
        $lessonsId = $request->get('lessons_id');
        $lessonLog = LessonLog::where("lessons_id", "=", $lessonsId)
                     ->where("teachers_id", "=", $userId)
                     ->where("status", "=", "open")->first();
        if(isset($lessonLog)) {
            return "inclass";
        }
        $request->session()->put('chooseLessonsId', $lessonsId);
        // return $request->session()->get('chooseLessonsId');
        // redirect('/teacher/');
        return "true";
        // redirect()->action('Teacher\HomeController@index');
        // Redirect::to('/teacher');
    }

    public function closeLesson(Request $request)
    {
        $lesson = Lesson::find($request->get('lessons_id'));
        $lesson->is_open = 2;

        if ($lesson->save()) {
            return "true";
        } else {
            return "false";
        }
    }
}
