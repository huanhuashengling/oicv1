<?php

namespace App\Http\Controllers\School;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Imports\StudentsImport;
use App\Models\Teacher;
use App\Models\Sclass;
use App\Models\WorkComment;
use App\Models\Lesson;
use App\Models\Post;
use App\Models\Mark;
use App\Models\Term;
use App\Models\Club;
use App\Models\ClubStudent;
use Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use \Auth;

class StudentAccountController extends Controller
{
    public function index()
    {
        $schoolsId = \Auth::guard("school")->id();
        $sclassesData = Sclass::select('sclasses.class_title', 'sclasses.enter_school_year', 'sclasses.id', DB::raw('count(students.id) as count'))
                              ->leftJoin('students', function($join) {
                                  $join->on('students.sclasses_id', '=', 'sclasses.id');
                              })
                              ->leftJoin('schools', function($join) {
                                  $join->on('schools.id', '=', 'sclasses.schools_id');
                              })
                              ->where('sclasses.is_graduated', '=', 0)
                              ->where('schools.id', '=', $schoolsId)
                              ->groupBy('sclasses.class_title', 'sclasses.enter_school_year', 'sclasses.id')
                              ->orderBy('sclasses.id')
                              ->get();

        $clubs = Club::where('schools_id', '=', $schoolsId)->get();
        foreach ($clubs as $key => $club) {
            $clubStudent = ClubStudent::where(["clubs_id" => $club->id])->count();
            $club->club_student_num = $clubStudent;
        }

        foreach ($sclassesData as $key => $item) {
            $sclassesData[$key]["title"] = $item['enter_school_year'] . "级" . $item["class_title"] . "班";
        }
        // dd($clubs);

        return view('school/student-account/index', compact('sclassesData', "clubs"));
    }

    public function importStudents(Request $request)
    {
        if(null !== $request->file('student')){
            // $path = $request->file('student');
            // dd($path);
            Excel::import(new StudentsImport, $request->file('student'));
        }
    }

    public function updateStudentEmail(Request $request)
    {
        if($request->hasFile('xls')){
            $path = $request->file('xls')->getRealPath();
            $data = Excel::import($path, function($reader) {})->get();
            if(!empty($data) && $data->count()){

                foreach ($data->toArray() as $value) {
                    // dd($value);

                    if(!empty($value)){
                        $this->updateOneStudentEmail($value);
                        // die();
                    }
                }
            }
        }
    }

    public function getStudentsData(Request $request) {
        $sclass = Sclass::find($request->get('sclasses_id'));
        if (isset($sclass)) {
            $students = Student::select('students.id as studentsId', 'students.*', 'sclasses.*')
            ->leftJoin('sclasses', function($join){
              $join->on('sclasses.id', '=', 'students.sclasses_id');
            })
            ->where(['sclasses_id' => $sclass->id])->get();
            return json_encode($students);
        } else {
            return "false";
        }
    }

    public function getClubStudentsData(Request $request) {
        $club = Club::find($request->get('clubs_id'));

        if (isset($club)) {
        // dd($club);

            $clubStudents = ClubStudent::where(["clubs_id" => $club->id])->get();
            $studentData = array();
            foreach ($clubStudents as $key => $clubStudent) {
                $student = Student::find($clubStudent->students_id);
                $sclass = Sclass::find($student->sclasses_id);
                $student->studentsId = $student->id;
                $student->clubsId = $club->id;
                $student->class_title = $sclass->class_title;
                $student->enter_school_year = $sclass->enter_school_year;
                array_push($studentData, $student);
            }
            return json_encode($studentData);
        } else {
            return "false";
        }
    }

    public function unbindClub(Request $request) {
        $studentsId = $request->get('users_id');
        $clubsId = $request->get('clubs_id');
        $clubStudent = ClubStudent::where(["clubs_id" => $clubsId])
                        ->where(["students_id" => $studentsId])
                        ->first();

        if (isset($clubStudent)) {
            $clubStudent->delete();
            // ClubStudent::delete($clubStudent->id);
            return "true";
        } else {
            return "false";
        }
    }

    public function resetStudentPassword(Request $request) {
        $student = Student::find($request->get('users_id'));
        if ($student) {
            $student->password = bcrypt("123456");
            $student->save();
            return "true";
        } else {
            return "false";
        }
    }

    public function lockOneStudentAccount(Request $request) {
        $student = Student::find($request->get('users_id'));
        if ($student) {
            $student->is_lock = 1;
            $student->save();
            return "true";
        } else {
            return "false";
        }
    }

    public function unlockOneStudentAccount(Request $request) {
        $student = Student::find($request->get('users_id'));
        if ($student) {
            $student->is_lock = 0;
            $student->save();
            return "true";
        } else {
            return "false";
        }
    }

    public function lockOneStudentWorkComment(Request $request) {
        $workComment = WorkComment::where("guest_students_id", "=", $request->get('users_id'))->delete();

        $student = Student::find($request->get('users_id'));
        if ($student) {
            $student->work_comment_enable = 2;
            $student->save();
            return "true";
        } else {
            return "false";
        }
    }

    public function unlockOneStudentWorkComment(Request $request) {
        $student = Student::find($request->get('users_id'));
        if ($student) {
            $student->work_comment_enable = 1;
            $student->save();
            return "true";
        } else {
            return "false";
        }
    }

    public function addMaxWorkNum(Request $request) {
        $student = Student::find($request->get('users_id'));
        if ($student) {
            $student->work_max_num = $student->work_max_num + 1;
            $student->save();
            return "true";
        } else {
            return "false";
        }
    }

    public function createOneStudent(Request $request)
    {
        $data = [];
        $data["username"] = $request->get('username');
        $data["gender"] = $request->get('gender');
        $data["password"] = $request->get('password');
        $data["groups_id"] = $request->get('groups_id');
        $data["order_in_group"] = $request->get('order_in_group');
        $data["sclasses_id"] = $request->get('sclasses_id');
        return $this->createStudentAccount($data);
    }

    public function createStudentAccount($data) {
        try {
            $student = Student::create([
                'username' => $data['username'],
                'email' => "",
                'password' => bcrypt($data['password']),
                'gender' => $data['gender'],
                'level' => 0,
                'score' => 0,
                'work_max_num' => 1,
                'work_comment_enable' => 1,
                'groups_id' => $data['groups_id'],
                'order_in_group' => $data['order_in_group'],
                'sclasses_id' => $data['sclasses_id'],
                'is_lock' => 0,
                'remember_token' => Str::random(10),
            ]);
        } catch (Exception $e) {
            throw new Exception("Error Processing Request", 1);
        }
    }

    public function updateOneStudentEmail($data) {
        try {
            $student = Student::find($data["id"]);
            $student->email = $data["email"];
            $student->save();
        } catch (Exception $e) {
            throw new Exception("Error Processing Request", 1);
        }
    }

    public function bringStudentsIntoClub(Request $request) {
        $club_id = $request->get("club_id");
        $student_id_str = $request->get("student_id_list");
        // echo($club_id);
        // dd($student_id_str);
        if ("" == $student_id_str) {
            return;
        }
        $studentIdList = explode(",", $student_id_str);
        foreach ($studentIdList as $key => $studentId) {
            echo($club_id);
        echo($studentId);
            // try {
                $clubStudent = ClubStudent::where(['students_id' => $studentId])
                ->where(['clubs_id' => $club_id])
                ->first();
                if(isset($clubStudent)) {
                    return;
                }
                // dd($clubStudent);

                $clubStudent = ClubStudent::create([
                    'students_id' => $studentId,
                    'clubs_id' => $club_id,
                    'status' => "open",
                ]);
            // } catch (Exception $e) {
            //     throw new Exception("Error Processing Request", 1);
            // }
        }
    }
}
