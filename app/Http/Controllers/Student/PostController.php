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
use \Auth;

use MarkdownEditor;
use \DB;

class PostController extends Controller
{
    public function index()
    {
        $imgTypes = ['jpg', 'jpeg', 'bmp', 'gif', 'png'];
        $docTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

        $id = \Auth::guard("student")->id();
        $student = Student::find($id);
        $sclass = Sclass::find($student->sclasses_id);
        $terms = Term::where(['enter_school_year' => $sclass->enter_school_year])->get();
        return view('student/posts', compact('terms'));
    }

    public function getPostsByTerm(Request $request) {
        $termsId = $request->get('termsId');
        // dd($termsId);
        $term = Term::find($termsId);
        $middir = "/posts/" . $this->getSchoolCode() . "/";
        $imgTypes = ['jpg', 'jpeg', 'bmp', 'gif', 'png'];

        $id = \Auth::guard("student")->id();
        $student = Student::find($id);
        $sclass = Sclass::find($student->sclasses_id);

        $from = date('Y-m-d', strtotime($term->from_date)); 
        $to = date('Y-m-d', strtotime($term->to_date));
        $lessonLogs = LessonLog::where(['sclasses_id' => $student['sclasses_id']])
        ->whereBetween('lesson_logs.created_at', array($from, $to))->get();

        $allMarkNum = 0;
        $allEffectMarkNum = 0;
        $allYouNum = 0;
        $allYouJiaNum = 0;
        $postData = [];
        foreach ($lessonLogs as $key => $lessonLog) {
            $lesson = Lesson::where(['id' => $lessonLog['lessons_id']])->first();
            $lesson->help_md_doc = MarkdownEditor::parse($lesson->help_md_doc);
            $post = Post::where(['lesson_logs_id' => $lessonLog['id'], "students_id" => $id])->orderBy('id', 'desc')->first();
            // $post->storage_name = env('APP_URL').  $middir .$post->storage_name;
            $postState = "未交";
            $rateScore = 0;
            $hasComment = "false";
            $markNum = 0;
            $validMarkNum = 0;
            $markNames = [];

            if (isset($post)) {

                $postState = "未评";

                $post->export_name = env('APP_URL'). $middir .$post->export_name;

                if (in_array($post->file_ext, $imgTypes)) {
                    $post["filetype"] = "img";
                    $post["previewPath"] = getThumbnail($post['storage_name'], 800, 600, $this->getSchoolCode(), 'fit', $post['file_ext']);
                }
                //  elseif (in_array($post->file_ext, $docTypes)) {
                //     $post["filetype"] = "doc";
                //     $post["previewPath"] = $post->storage_name;
                // }


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
                $comment = Comment::where(['posts_id' => $post['id']])->first();
                // $hasComment = isset($comment)?"true":"false";
                // if (isset($comment)) {
                //     $hasComment = "true";
                //    ++;
                // }
                $markNames = Mark::select('students.username')
                ->leftJoin("students", 'students.id', '=', 'marks.students_id')
                ->where(['posts_id' => $post['id'], 'state_code' => 1])->get();
                $markNum = count($markNames);
                $validMarkNum = ($markNum>4)?4:$markNum;
                $allMarkNum += $markNum;
                $allEffectMarkNum += ($markNum>4)?4:$markNum;
                // dd($marks);
            }

            $postData[] = ["lesson_title" => $lesson->title, 
                            'post' => $post, 
                            'postState' => $postState, 
                            'rateScore' => $rateScore,
                            'score' => $rateScore + $validMarkNum/2,
                            'lessonLog' => $lessonLog, 
                            'hasComment' => $hasComment, 
                            'validMarkNum' => $validMarkNum,
                            'markNum' => $markNum, 
                            'markNames' => $markNames];
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
        // return $this->buildPostListHtml($postData, $allMarkNum, $allEffectMarkNum, $allYouNum, $allYouJiaNum, $allScore, $levelStr);
    }






    public function buildPostListHtml($postData, $allMarkNum, $allEffectMarkNum, $allYouNum, $allYouJiaNum, $allScore, $levelStr) {
        $resultHtml = "<div class='alert alert-info col-md-10 col-md-offset-1'><div>当前成绩：(有效赞" . $allEffectMarkNum ." * 0.5) (" . $allYouJiaNum . "个优+ * 9)(". $allYouNum . "个优 * 8) = " . $allScore . "分 （当前等第：" . $levelStr . "）</div><div>总赞数(共" . $allMarkNum . "个赞)，每份作业4次有效，以每个0.5分计入期末成绩，共2分</div><div>颜色注释：白色为未提交，黄色为未看或未达到作业要求，绿色为优等，红色为优+等第</div></div>";

        $resultHtml .= "<div class='accordion' id='accordionExample'>";
        foreach ($postData as $key => $item) {
            $orderNum = $key + 1;
            $hasComment = "";
            
            $hasPostCss = "default";
            $hasPostStr = "(未交)";
            $rateStr = "";
            $markStr = "";
            if (isset($item['post'])) {
                
                $markStr = $item['markNum']."个赞";
                $hasPostStr = $item['rate'];
                if ("优" == $item['rate']) {
                    $hasPostCss = "success";
                } elseif ("优+" == $item['rate']) {
                    $hasPostCss = "danger";
                } else {
                    $hasPostCss = "warning";
                }
            }

            // if ("true" == $item['hasComment']) {
            //     $hasComment = "有评语";
            //     $hasPostCss = "danger";
            // }
            // if ("优" != $item['rate']) {
                $resultHtml .= "<input type='hidden' name='' id='posted-path-" . $item['post']['id'] . "' value='" . $item['post']['storage_name'] . "' />";
            // }
// <div class='accordion' id='accordionExample'>
//   <div class='accordion-item'><h2 class='accordion-header' id='headingOne'>
//       <button class='accordion-button' type='button' data-bs-toggle='collapse' data-bs-target='#collapseOne' aria-expanded='true' aria-controls='collapseOne'>
//         Accordion Item #1
//       </button>
//     </h2>
//     <div id='collapseOne' class='accordion-collapse collapse show' aria-labelledby='headingOne' data-bs-parent='#accordionExample'>
//       <div class='accordion-body'>
//         <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
//       </div>
//     </div>
//   </div>
            $resultHtml .= "<div class='accordion-item'><div class='accordion-header' id='headingOne" . $orderNum. "'><button class='accordion-button' type='button' value='" . $item['post']['id'] . "," . $item['post']['storage_name'] . "," . $item['post']['filetype'] . "," . $item['post']['previewPath'] . "' data-bs-toggle='collapse' data-bs-target='#collapseOne" . $orderNum . "' aria-controls='collapseOne".$orderNum."'>第" . $orderNum . "节： " . $item['lesson']['title'] . " <small>" . $item['lesson']['subtitle'] ." </small>  <label class='text-right'>" ." ". $hasPostStr ." " . $markStr . "</label></button></div><div id='collapseOne" . $orderNum . "' class='accordion-collapse collapse show' aria-labelledby='headingOne" . $orderNum . "' data-bs-parent='#accordionExample'><div class='accordion-body'><div id='post-body-" . $item['post']['id'] . "'></div>";
            

                if (isset($item['post'])) {
                    if ('待完' != $item['rate']) {
                        $resultHtml .= "<div id='doc-preview-" .$item['post']['id'] . "'></div>";
                        $resultHtml .= "<img src='' id='post-show-" . $item['post']['id'] . "' class='img-responsive'>";
                        
                        $resultHtml .= "<a href='' id='post-download-" . $item['post']['id'] . "'>右键点击下载</a><p></p>";
                        $resultHtml .= "<div class='form-group'><label id='rate-label-" . $item['post']['id'] . "'></label></div>";
                        $resultHtml .= "<div class='form-group'><label id='post-comment-" . $item['post']['id'] . "' value=''></label></div>";
                        $resultHtml .= "<div class='form-group'>" . $item['markNum'] . "个人为你点赞：";
                            foreach ($item['markNames'] as $key => $name) {
                                $resultHtml .= $name->username . ", ";
                            }
                        $resultHtml .= "</div>";
                    } else {
                        $resultHtml .= "<a href='' id='post-download-" . $item['post']['id'] . "'>右键点击下载</a><p></p>";
                        $resultHtml .= $item['lesson']['help_md_doc'];
                        $resultHtml .= "<form action='/student/upload' method='POST' accept-charset='UTF-8' enctype='multipart/form-data'>" . csrf_field();
                        $resultHtml .= "<input type='hidden' name='lesson_logs_id' value='" . $item['lessonLog']['id'] . "'>";
                        $resultHtml .= "<input type='file' name='source' id='input-zh-".$item["post"]["id"]."' class='file input-zh'>";
                            
                        $resultHtml .= "</form>";
                    }
                } else {
                    $resultHtml .= $item['lesson']['help_md_doc'];
                    $resultHtml .= "<form action='/student/upload' method='POST' accept-charset='UTF-8' enctype='multipart/form-data'>" . csrf_field();
                    $resultHtml .= "<input type='hidden' name='lesson_logs_id' value='" . $item['lessonLog']['id'] . "'>";
                    $resultHtml .= "<input type='file' name='source' id='input-zh-".$item["post"]["id"]."' class='file input-zh'>";
                        
                    $resultHtml .= "</form>";
                }

            $resultHtml .= "</div></div></div>";     
        }
        $resultHtml .= "</div></div>";
        return $resultHtml;
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
