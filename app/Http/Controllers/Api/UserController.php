<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\Api\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
use \Storage;

class UserController extends ApiController
{
    //返回用户列表
    public function index(){
        //3个用户为一页
        $users = Student::paginate(3);
        return UserResource::collection($users);
    }
    //返回单一用户信息
    public function show($studentId){
        // $student = \Auth::guard("student")->id();
        $student = Student::find($studentId);
        return $this->success(new UserResource($student));
    }
    //用户注册
    public function store(UserRequest $request){
        Student::create($request->all());
        return $this->setStatusCode(201)->success('用户注册成功');
    }
    //用户登录
    public function login(Request $request){
        // dd($request->username)
        $token=Auth::guard('api')->attempt(['username'=>$request->username,'password'=>$request->password]);
        if($token) {
            // $token = Auth::guard('api')->user()->toJson();
            return $this->setStatusCode(201)->success(['token' => 'bearer ' . $token]);
        }
        return $this->failed("账号或密码错误" ,400);
    }
    //用户退出
    public function logout(){
        Auth::guard('api')->logout();
        return $this->success('退出成功...');
    }
    //返回当前登录用户信息
    public function info(){
        $student = Auth::guard('api')->user();
        $lessonLog = LessonLog::select("lesson_logs.id as lesson_logs_id", "lessons_id")
              ->leftJoin('sclasses', 'lesson_logs.sclasses_id', '=', "sclasses.id")
              ->where('sclasses.id', '=', $student->sclasses_id)
              ->where("lesson_logs.status", '=', 'open')->first();

        $lesson = Lesson::where('id', '=', $lessonLog->lessons_id)->first();

        $post = Post::where('lesson_logs_id', '=', $lessonLog->lesson_logs_id)
                    ->where('students_id', '=', $student->id)
                    ->first();

        $student["lesson_logs_id"] = $lessonLog->lesson_logs_id;
        $student["lesson_title"] = $lesson->title;
        if ($post) {
            $student["post_path"] = "/posts/yuying3/" . $post->post_code . "." . $post->file_ext;
        } else {
            $student["post_path"] = "/project/blank.sb3";
        }

        return $this->success(new UserResource($student));
    }

    public function uploadPost(Request $request)
    {
        $file = $request->file('tFile');

        $cover = $request->file('tCover');

        $lessonLogsId = $request->get('lessonLogsId');

        $studentsId = Auth::guard("api")->id();

        $schoolCode =  $this->getSchoolCode();
        $oldPost = Post::where(['lesson_logs_id' => $lessonLogsId, "students_id" => $studentsId])->orderBy('id', 'desc')->first();
        $uniqid = uniqid();
        if ($file->isValid()) {
            $fileOriginalName = "";
            $fileExt = "sb3";
            $fileType = "application/x.scratch.sb3";
            $fileRealPath = $file->getRealPath();
            $filename = $uniqid . '.' . $fileExt;
            $filebool = Storage::disk($schoolCode . 'posts')->put($filename, file_get_contents($fileRealPath));
        } else {
            return $this->success("sb3文件传输错误");
        }
        
        if ($cover->isValid()) {
            $coverOriginalName = "";
            $coverExt = "png";
            $coverType = "image/png";
            $coverRealPath = $cover->getRealPath();
            $covername = $uniqid . '_c.' . $coverExt;
            $coverbool = Storage::disk($schoolCode . 'cover')->put($covername, file_get_contents($coverRealPath));
        } else {
            return $this->success("封面文件传输错误");
        }
        // return "filebool " . $filebool . " coverbool " . $coverbool;
        if ($filebool & $coverbool) {
            if($oldPost) {
                $oldFilename = $oldPost->post_code . "." . $oldPost->file_ext;
                $oldCovername = $oldPost->post_code . "_c." . $oldPost->cover_ext;
                $oldPost->export_name = $uniqid . "." . $fileExt;
                $oldPost->file_ext = $fileExt;
                $oldPost->cover_ext = $coverExt;
                $oldPost->post_code = $uniqid;
                if ($oldPost->update()) {
                    $delFileBool = Storage::disk($schoolCode . 'posts')->delete($oldFilename); 
                    $delCoverBool = Storage::disk($schoolCode . 'cover')->delete($oldCovername); 
                    if ($delFileBool & $delCoverBool) {
                        return $this->success('作品更新成功！');
                    } else {
                        return $this->success('作品更新成功，但历史文件删除错误！');
                    }
                } else {
                    return $this->success('作品更新失败！');
                }
            } else {
                $post = new Post();
                $post->students_id = Auth::guard("api")->id();
                $post->lesson_logs_id = $lessonLogsId;
                $post->export_name = $uniqid . "." . $fileExt;
                $post->file_ext = $fileExt;
                $post->cover_ext = $coverExt;
                $post->post_code = $uniqid;
                if ($post->save()) {
                    return $this->success('新增作品成功！');
                } else {
                    return $this->success('新增作品失败！');
                }
            }
        } else {
            return $this->success('作品获封面文件存储错误！');
        }
    }

    public function getSchoolCode()
    {
      $student = Student::find(Auth::guard("api")->id());

      $school = School::leftJoin('sclasses', 'sclasses.schools_id', '=', "schools.id")
              ->where('sclasses.id', '=', $student->sclasses_id)->first();
      return $school->code;
    }
}
