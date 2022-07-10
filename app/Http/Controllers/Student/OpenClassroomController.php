<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Course;
use App\Models\Unit;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Sclass;
use App\Models\Term;
use MarkdownEditor;
use Route;

class OpenClassroomController extends Controller
{
    public function index()
    {
        $id = \Auth::guard("student")->id();

        $courses = Course::where("is_open", "=", 1)->get();
        $planetUrl = url("images/planet.png");
        return view('student/open-classroom/index', compact('courses', 'planetUrl'));
    }

    public function course(Request $request)
    {   
        $coursesId = Route::current()->parameter('id');
        $id = \Auth::guard("student")->id();
        $course = Course::find($coursesId);
        $units = Unit::where("courses_id", "=", $coursesId)
        ->where("is_open", "=", 1)->get();
        $planetUrl = url("images/planet.png");
        return view('student/open-classroom/course', compact('units', 'planetUrl', 'course'));
    }

    public function unit(Request $request)
    {
        $unitsId = Route::current()->parameter('id');

        $id = \Auth::guard("student")->id();
        $unit = Unit::find($unitsId);
        $unit->course = Course::find($unit->courses_id);
        $lessons = Lesson::where("units_id", "=", $unitsId)
        ->where("is_open", "=", 1)->get();
        $planetUrl = url("images/planet.png");
        return view('student/open-classroom/unit', compact('lessons', 'planetUrl', 'unit'));
    }

    public function lesson(Request $request)
    {
        $lessonsId = Route::current()->parameter('id');

        $id = \Auth::guard("student")->id();
        $lesson = Lesson::find($lessonsId);
        $unit = Unit::find($lesson->units_id);
        $unit->course = Course::find($unit->courses_id);
        $lesson->unit = $unit;
        $planetUrl = url("images/planet.png");
        if(strpos($lesson->help_md_doc, "10.115.3.153")) {
        $lesson->help_md_doc = str_replace("10.115.3.153", $_SERVER['HTTP_HOST'], $lesson->help_md_doc);
      }

      if("10.115.3.153" != $_SERVER['HTTP_HOST']) {
        if(strpos($lesson->help_md_doc, "10.115.3.3:8080")) {
          $lesson->help_md_doc = str_replace("10.115.3.3:8080", "kiftd.workc.cc:7002", $lesson->help_md_doc);
        }
      }
      
        $lesson->help_md_doc = MarkdownEditor::parse($lesson->help_md_doc);
        return view('student/open-classroom/lesson', compact('lesson', 'planetUrl'));
    }
}
