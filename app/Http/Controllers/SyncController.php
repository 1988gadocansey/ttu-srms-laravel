<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models;
 
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
 use Response;
class SyncController extends Controller
{
     
    /**
     * Create a new controller instance.
     *
     
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
       
         
    }
      
    /**
     * Create a new task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function sendBulk(Request $request,SystemController $sys)
    {
                $url = "http://localhost/srms-portal/sync.php";
            
            
               
               $table = $request->input('table');
        

          
               if($table==36){
                $data = Models\CourseModel::where("UPDATED", 1)->get()->toArray();
                
                foreach ($data as $row) {
                    $course_name = $row["COURSE_NAME"];
                    $course_code = $row["COURSE_CODE"];
                    $credit = $row["COURSE_CREDIT"];
                    $level = $row["COURSE_LEVEL"];
                    $semester = $row["COURSE_SEMESTER"];
                    
                    $program = $row["PROGRAMME"];
                    $id= $row["ID"];
                     $user= $row["USER"];
                     

                    $ins = "ID='$id', COURSE_NAME='$course_name', COURSE_CODE='$course_code',  COURSE_CREDIT='$credit', COURSE_LEVEL='$level', COURSE_SEMESTER='$semester',  PROGRAMME='$program', USER='$user'";

                    $post = array('type' => 'courses', 'data' => $ins);
                    $result = $sys->sync_to_online($url, $post);
                    if ($result) {
 
                        Models\CourseModel::where("ID",$id)->update(array("UPDATED"=>0));
                    }
                
             
            }  
               }
               
            if($table==57){
                $data = Models\MountedCourseModel::where("SYNC", 1)->get()->toArray();
                
                foreach ($data as $row) {
                    $course_name = $row["COURSE"];
                    $course_code = $row["COURSE_CODE"];
                    $credit = $row["COURSE_CREDIT"];
                    $level = $row["COURSE_LEVEL"];
                    $semester = $row["COURSE_SEMESTER"];
                    
                    $program = $row["PROGRAMME"];
                    $id= $row["ID"];
                     $lecturer= $row["LECTURER"];
                     //$user= $row["USER"];
                     $year= $row["COURSE_YEAR"];
                    $type= $row["COURSE_TYPE"];
                     

                    $ins = "ID='$id',COURSE_TYPE='$type',LECTURER='$lecturer',COURSE_YEAR='$year', COURSE='$course_name', COURSE_CODE='$course_code',  COURSE_CREDIT='$credit', COURSE_LEVEL='$level', COURSE_SEMESTER='$semester',  PROGRAMME='$program',SYNC='1'";

                    $post = array('type' => 'mount', 'data' => $ins);
                    $result = $sys->sync_to_online($url, $post);
                    if ($result) {
 
                        Models\MountedCourseModel::where("ID",$id)->update(array("SYNC"=>0));
                    }
                
             
            }  
               }
           if($table==25){
                $data = Models\AcademicRecordsModel::where("updates", 1)->get()->toArray();
                
                foreach ($data as $row) {
                        $id=$row["id"];
                        $code=$row["code"];
                        $course=$row["course"];
                        $credit=$row["credits"];
                        $student=$row["student"];
                        $indexno=$row["indexno"];
                        
                        $quiz1=$row["quiz1"];
                        $quiz2=$row["quiz2"];
                        $quiz3=$row["quiz3"];
                        $midSem1=$row["midSem1"];
                        $exam=$row["exam"];
                        $total=$row["total"];
                        $grade=$row["grade"];
                        $gpoint=$row["gpoint"];
                        $year=$row["year"];
                        $sem=$row["sem"];
                        $level=$row["level"];
                        $yrgp=$row["yrgp"];
                        $groups=$row["groups"];
                        $lecturer=$row["lecturer"];
                        $resit=$row["resit"];
                        $dateRegistered=$row["dateRegistered"];
                       $createdAt=$row["createdAt"];
                       

                    $ins = " id='$id',course='$course',code='$code',student='$student', indexno='$indexno', quiz1='$quiz1',  credits='$credit', "
                            . ""
                            . "quiz2='$quiz2', quiz3='$quiz3',  midSem1='$midSem1',exam='$exam',total='$total',"
                            . ""
                            . "grade='$grade',gpoint='$gpoint',year='$year',sem='$sem',level='$level',yrgp='$yrgp',groups='$groups',lecturer='$lecturer',resit='$resit',dateRegistered='$dateRegistered',createdAt='$createdAt',updates='1'";

                  //  dd($ins);
                    $post = array('type' => 'results', 'data' => $ins);
                    $result = $sys->sync_to_online($url, $post);
                    if ($result) {
 
                        Models\AcademicRecordsModel::where("id",$id)->update(array("updates"=>0));
                    }
                
             
            }  
               }
            return response()->json(['status'=>'success','message'=>'data pushed to portal successffuly ']);
            
    }

    /**
     * Destroy the given task.
     *
     * @param  Request  $request
     * @param  Task  $task
     * @return Response
     */
    public function destroy(Request $request, Task $task)
    {
        $this->authorize('destroy', $task);

        $task->delete();

        return redirect('/tasks');
    }
}
