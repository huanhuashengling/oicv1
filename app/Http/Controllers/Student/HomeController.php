<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Sclass;
use App\Models\School;
use App\Models\LessonLog;
use App\Models\Post;
use App\Models\Lesson;
use App\Models\PostRate;
use App\Models\Group;
use App\Models\Comment;
use App\Models\Mark;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\File;
use \Auth;
use \Storage;
use MarkdownEditor;
use JWTAuth;

class HomeController extends Controller
{
    public function index()
    {   
        $id = auth()->guard("student")->id();
        $student = Student::find($id);
        // $JWTToken = $student->getJWTIdentifier();
        // $JWTToken = Auth::guard('api')->fromUser($student);
        $lessonLog = LessonLog::where(['sclasses_id' => $student['sclasses_id'], 'status' => 'open'])->first();

        $allLessonLogs = LessonLog::select('lesson_logs.id as lesson_logs_id', 'lessons.title', 'lessons.subtitle', 'lesson_logs.updated_at', 'lessons.id as lessons_id')
        ->join('lessons', 'lessons.id', '=', "lesson_logs.lessons_id")
        ->where(['lesson_logs.sclasses_id' => $student['sclasses_id']])->get();
        $allLessonData = array();
        // dd($allLessonLogs);
        foreach ($allLessonLogs as $key => $lessonLogData) {
          $tLesson = (object) array("order"=> $key + 1, "lesson_logs_id" =>$lessonLogData->lesson_logs_id, "lessons_id" => $lessonLogData->lessons_id, "title" => $lessonLogData->title, 'subtitle' => $lessonLogData->subtitle, 'finished_status' => "已提交", 'selected' => "", 'curr_str' => "[历史] ");

          $post = Post::where(['students_id' => $id, 'lesson_logs_id' => $lessonLogData['lesson_logs_id']])->first();
          if (!isset($post)) {
            $tLesson->finished_status = "未提交";
          }
          if ($lessonLog) {
            if ($lessonLog->id == $lessonLogData->lesson_logs_id) {
              $tLesson->selected = "selected";
              $tLesson->curr_str = "";
            }
          }
          
          array_push($allLessonData, $tLesson);
        }
        $groupStudentsName = [];
        $groupName = "";
        if ($student->groups_id) {
          $tGroup = Group::find($student->groups_id);
          $groupName = $tGroup->name;

          $tStudents = Student::where("groups_id", "=", $student->groups_id)->orderBy("order_in_group", "ASC")->get();
            
            foreach ($tStudents as $key => $tStudent) {
              if ($tStudent->username != $student->username) {
                $groupStudentsName[] = $tStudent->username;
              }
            }
        }
        return view('student/home', compact('groupStudentsName', 'groupName', 'allLessonData'));
    }

    public function getOneLesson(Request $request){
      $id = auth()->guard("student")->id();
      $student = Student::find($id);
        // $JWTToken = $student->getJWTIdentifier();
      $JWTToken = "";

      $lessonsId = $request->get('lessons_id');
      $lessonLogsId = $request->get('lesson_logs_id');

      $lesson = Lesson::where(['id' => $lessonsId])->first();

      if ("sb3" == $lesson->allow_post_file_types) {
        $JWTToken = JWTAuth::fromUser($student);
      }

      if(strpos($lesson->help_md_doc, "10.115.3.153")) {
        $lesson->help_md_doc = str_replace("10.115.3.153", $_SERVER['HTTP_HOST'], $lesson->help_md_doc);
      }
      $lesson->help_md_doc = MarkdownEditor::parse($lesson->help_md_doc);
      $lesson->JWTToken = $JWTToken;
      $lesson->export_name = "";
      // $lesson->export_name = "/posts/yuying3/619579c62d64f.png";
      
      $post = Post::where(['students_id' => $id, 'lesson_logs_id' => $lessonLogsId])->first();
      if ($post) {
        $lesson->export_name = "/posts/yuying3/" . $post->export_name;
      }
      return $lesson;
    }

    public function upload(Request $request)
    {
      $file = $request->file('source');
      if(!$file) {
        return json_encode("{'请重新选择作业提交！'}");
        // return Redirect::to('student')->with('danger', '请重新选择作业提交！');
      }

      $studentsId = Auth::guard("student")->id();
      
      // return $this->getSchoolCode();
      // dd($request->get('lesson_logs_id'));
      $lessonLogsId = $request->get('lesson_logs_id');
      $oldPost = Post::where(['lesson_logs_id' => $lessonLogsId, "students_id" => $studentsId])->orderBy('id', 'desc')->first();

      if ($file->isValid()) {
        // 原文件名
        $originalName = $file->getClientOriginalName();
        // $bytes = File::size($filename);
        // 扩展名
        $ext = $file->getClientOriginalExtension();
        // $originalName = str_replace($originalName, ".".$ext);
        // MimeType
        $type = $file->getClientMimeType();
        // dd($originalName);
        // 临时绝对路径
        $realPath = $file->getRealPath();

        $uniqid = uniqid();
        // $filename = $originalName . '-' . $uniqid . '.' . $ext;
        $filename = $uniqid . '.' . $ext;

        $bool = Storage::disk($this->getSchoolCode() . 'posts')->put($filename, file_get_contents($realPath)); 

        Image::configure(array('driver' => 'imagick')); 
        Image::make(file_get_contents($realPath))
              ->resize(240, 180)->save(public_path('posts/yuying3/') . $uniqid . '_c.png');

        //TDDO update these new or update code
        if($oldPost) {
          $oldCoverFilename = $oldPost->post_code . "_c.png";
          $oldFilename = $oldPost->export_name;
          $oldPost->export_name = $filename;
          $oldPost->cover_ext = "png";
          $oldPost->file_ext = $ext;
          $oldPost->post_code = $uniqid;
          if ($oldPost->update()) {
            $bool = Storage::disk($this->getSchoolCode() . 'posts')->delete($oldFilename); 
            $bool = Storage::disk($this->getSchoolCode() . 'posts')->delete($oldCoverFilename); 
            return json_encode("{'作业提交成功！'}");
          } else {
            return json_encode("{'作业提交失败，请重新操作！'}");
          }
        } else {
          $post = new Post();
          $post->students_id = Auth::guard("student")->id();
          $post->lesson_logs_id = $request->get('lesson_logs_id');
          $post->export_name = $filename;
          $post->file_ext = $ext;
          $post->cover_ext = "png";
          $post->post_code = $uniqid;
          if ($post->save()) {
            return json_encode("{'作业提交成功！''}");
          } else {
            return json_encode("{'作业提交失败，请重新操作！'}");
          }
        }
      } else {
            return json_encode("{'文件上传失败，请确认是否文件过大？'}");
      }
    }

    public function getReset()
    {
        return view('student.login.reset');
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
        $user = Auth::guard("student")->user();
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
        Auth::guard("student")->logout();  //更改完这次密码后，退出这个用户
        return redirect('/student/login');
    }

    public function getCommentByPostsId(Request $request)
    {
        $comment = Comment::where(['posts_id' => $request->get('posts_id')])->first();

        if (isset($comment)) {
            return json_encode($comment);
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

    public function getStudentInfo()
    {
        $userId = auth()->guard('student')->id();
        $student = Student::select('students.*', 'terms.grade_key', 'sclasses.class_title', 'schools.title', 'districts.title as district_title')
                ->join('sclasses', 'sclasses.id', '=', "students.sclasses_id")
                ->join('schools', 'schools.id', '=', "sclasses.schools_id")
                ->join('districts', 'districts.id', '=', "schools.districts_id")
                ->join('terms', 'terms.enter_school_year', '=', "sclasses.enter_school_year")
                ->where(['students.id' => $userId, 'terms.is_current' => 1])
                ->first();
        $posts = Post::where(['posts.students_id' => $userId])->get();
        $postNum = count($posts);
        $rateYouNum = 0;
        $rateYouJiaNum = 0;
        $rateWeipingNum = 0;
        $rateDaiWanNum = 0;
        $commentNum = 0;
        $markNum = 0;
        
        $allLessonLogNum = LessonLog::where(['lesson_logs.sclasses_id' => $student->sclasses_id])->count();
        $unPostNum = $allLessonLogNum - $postNum;
        foreach ($posts as $key => $post) {
          $post_rate = PostRate::where(['post_rates.posts_id' => $post->id])->first();
          if (!$post_rate) {
            $rateWeipingNum++;
          } else if ("优+" == $post_rate->rate) {
            $rateYouJiaNum++;
          } else if ("优" == $post_rate->rate) {
            $rateYouNum++;
          }else if ("待完" == $post_rate->rate) {
            $rateDaiWanNum++;
          }
          $comment = Comment::where(['comments.posts_id' => $post->id])->first();
          if ($comment) {
            $commentNum++;
          }
          $mark = Mark::where(['marks.posts_id' => $post->id, 'marks.state_code' => 1])->count();
          $markNum += $mark;
        }
        $markOthersNum = Mark::where(['marks.students_id' => $userId])->count();
        return view('student/login/info', compact('student', 'postNum', 'rateYouJiaNum', 'rateYouNum', 'rateDaiWanNum', 'commentNum', 'markNum', 'markOthersNum', 'rateWeipingNum', 'unPostNum', 'allLessonLogNum'));
    }

    public function getOnePost(Request $request)
    {
        $middir = "/posts/" . $this->getSchoolCode() . "/";
        $imgTypes = ['jpg', 'jpeg', 'bmp', 'gif', 'png'];
        $docTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        $post = Post::select("posts.*", "lessons.title", "lessons.subtitle", "students.username", "students.id as students_id")
                ->where("posts.id", "=", $request->input('posts_id'))
                ->join('students', 'students.id', '=', "posts.students_id")
                ->join('lesson_logs', 'lesson_logs.id', '=', "posts.lesson_logs_id")
                ->join('lessons', 'lessons.id', '=', "lesson_logs.lessons_id")->first();
                // return var_dump($post);
        if (isset($post)) {
          if (in_array($post->file_ext, $imgTypes)) {
                return ["filetype"=>"img", 
                    "storage_name" => getThumbnail($post['storage_name'], 801, 601, $this->getSchoolCode(), 'background', $post['file_ext']), 
                    'username' => $post["username"], 
                    'students_id' => $post["students_id"], 
                    'lessontitle' => $post["title"], 
                    'lessonsubtitle' => $post["subtitle"],
                    'file_path' => env('APP_URL'). $middir .$post->storage_name];
            } elseif (in_array($post->file_ext, $docTypes)) {
              return ["filetype"=>"doc", 
                    "storage_name" => env('APP_URL'). $middir .$post->storage_name, 
                    'username' => $post["username"], 
                    'students_id' => $post["students_id"], 
                    'lessontitle' => $post["title"], 
                    'lessonsubtitle' => $post["subtitle"]];
            } elseif ("sb3" == $post->file_ext) {
              return ["filetype"=>"sb3", 
                    "file_path" => $middir .$post->post_code . ".sb3", 
                    "post_code" => $post->post_code, 
                    'username' => $post["username"], 
                    'students_id' => $post["students_id"], 
                    'lessontitle' => $post["title"], 
                    'lessonsubtitle' => $post["subtitle"]];
            }
          // $post['storage_name'] = env('APP_URL'). $middir .$post['storage_name'];
            // return env('APP_URL'). $middir .$post['storage_name'];
            // {{ getThumbnail($post->storage_name, 140, 100, 'fit') }}
            // return ["storage_name" => env('APP_URL'). $middir .$post['storage_name'], 
            return ["storage_name" => getThumbnail($post['post_code'], 800, 600, $this->getSchoolCode(), 'fit', $post['file_ext']), 
                    'username' => $post["username"], 
                    'lessontitle' => $post["title"], 
                    'lessonsubtitle' => $post["subtitle"]];
        } else {
            return "false";
        }
    }

    public function getMarkNumByPostsId(Request $request)
    {
        $marks = Mark::where(["posts_id" => $request->input('postsId'), "state_code" => 1])->get();

        if (isset($marks)) {
            return count($marks);
        } else {
            return 0;
        }
    }

    public function getIsMarkedByMyself(Request $request)
    {
      $studentsId = Auth::guard("student")->id();
      $postsId =  $request->input('postsId');
      $mark = Mark::where(["posts_id" => $postsId, "students_id" => $studentsId, "state_code" => 1])->first();

        if (isset($mark)) {
            return "true";
        } else {
            return "false";
        }
    }

    public function updateMarkState(Request $request)
    {
        $studentsId = Auth::guard("student")->id();
        $postsId =  $request->input('postsId');
        $stateCode =  $request->input('stateCode');
        
        $mark = Mark::where(["posts_id" => $postsId, "students_id" => $studentsId])->first();
        // dd($mark);
        if (isset($mark)) {
          $mark->state_code = $stateCode;
          if ($mark->save()) {
            return "true";
          }
        } else {
          $mark = new Mark();
          $mark->posts_id = $postsId;
          $mark->students_id = $studentsId;
          $mark->state_code = $stateCode;
          if ($mark->save()) {
            return "true";
          }
        }
    }

    public function getSchoolCode()
    {
      $student = Student::find(Auth::guard("student")->id());

      $school = School::leftJoin('sclasses', 'sclasses.schools_id', '=', "schools.id")
              ->where('sclasses.id', '=', $student->sclasses_id)->first();
      return $school->code;
    }
}
