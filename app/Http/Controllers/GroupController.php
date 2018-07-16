<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use App\Models;
use Validator;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
 
class GroupController extends Controller
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
     * Display a list of all of the user's task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request, SystemController $sys)
    {
        $array=$sys->getSemYear();
              
              $year=$array[0]->YEAR;
         if ( @\Auth::user()->department == 'top') {
        
        $query=  Models\GroupModel::where("year",$year)->paginate(100);
        
         }else{
             $query=  Models\GroupModel::where("createdBy",@\Auth::user()->fund)->where("year",$year)->paginate(100);
       
         }
        return view("groups.index")->With("data",$query);
        
    }
   

     public function createGroup(Request $request, SystemController $sys)
    { 
          if (@\Auth::user()->role == 'HOD' || @\Auth::user()->role == 'Registrar' || @\Auth::user()->department == 'top') {
               $array=$sys->getSemYear();
              
              $year=$array[0]->YEAR;
           if ($request->isMethod("get")) {
               
                $user_department= @\Auth::user()->department;
              $data= \DB::table('tpoly_programme')->where('DEPTCODE',$user_department)->orderby("PROGRAMME")
                 ->paginate(100);
              
               $programs=$sys->getProgramList();
               $lecturers=$sys->getLectureList_All();
               $query=  Models\GroupModel::where("createdBy",@\Auth::user()->fund)
                ->where("year",$year)->where("run",0)->get();
        
        
            return view('groups.create')->with("level",$sys->getLevelList())->with("program",$programs)->with("data",$data)->with('group', $query)
                    ->with("lecturers",$lecturers);
                             
            } 
            else{
                
                
                  $this->validate($request, [
            'level' => 'required',
            'name'=>'required',
            'total'=>'required',
            'program'=>'required',
             
        ]);
  
       
         $total=$request->input('total');
         $level=$request->input('level');
         $name=$request->input('name');
         $program=$request->input('program');
         //dd($program);
       // first store the groups
      for($i=0;$i<count($total);$i++){
         $groupModel= new Models\GroupModel();
          
         $groupModel->name=$name[$i];
         $groupModel->program=$program[$i];
         $groupModel->level=$level[$i];
         $groupModel->totalStudent=$total[$i];
         $groupModel->year=$year;
         $groupModel->createdBy=@\Auth::user()->fund;
         $groupModel->save();
          
      }
        return redirect("/groups/create");
       }
     }
  else{
      return redirect("/dashboard");
  }
           
    }
    
    
    /**
     * Create a new task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function assign(Request $request,SystemController $sys)
    {
         // $query=Models\GroupModel::where("createdBy",\Auth::user()->fund)->get();
          $group=$request->input("key");
          $lecturer=$request->input("lecturer");
          $name=$request->input("name");
          for($i=0;$i<count($group);$i++){
                Models\GroupModel::where("id",$group[$i])->update(array("lecturer"=>$sys->getLectureStaffID($lecturer[$i])));
                
                $query=User::where("fund",$sys->getLectureStaffID($lecturer[$i]))->first();
                $groupOld=$query->student_groups;
                if($groupOld==""){
                $newGroup=$name[$i];
                }
                else{
                     $newGroup=$groupOld.",".$name[$i];
                }
                
                User::where("fund",$sys->getLectureStaffID($lecturer[$i]))->update(array("student_groups"=>$newGroup));
          }
         return redirect("/groups/view");
       
    }
     public function  run(Request $request)
    {
         $query=Models\GroupModel::where("createdBy",\Auth::user()->fund)->get();
         foreach($query as $index=> $row){
             
             $level=$row->level;
             $program=$row->program;
             $name=$row->name;
             $total=$row->totalStudent;
             
            $q= Models\StudentModel::where("PROGRAMMECODE",$program)
                     ->where("LEVEL",$level)
                     ->where("STATUS","In school")->where("CLASS_GROUPS",0)->orderby("INDEXNO")->limit($total)->get();
      //print_r(count($q));
            $a=0;
            foreach($q as $key=> $keys){
//                $a++;
//                print_r(count($q));
                Models\StudentModel::where("PROGRAMMECODE",$program)
                     ->where("LEVEL",$level)
                     ->where("STATUS","In school")->where("ID",$keys->ID)->update(array("CLASS_GROUPS"=>$name));
             Models\AcademicRecordsModel::where("student",$keys->ID)->update(array("groups"=>$name));

//                print_r(     
////                                         $a."-".$keys->INDEXNO);echo"<br/>";
     
             }
            // Models\GroupModel::where("id",$row->id)->update(array("run"=>1));
         }
     
        return redirect("/groups/create");
       
    }
    
    public function edit($id){
        }

    public function update(Request $request, $id){
        
    }
    /**
     * Destroy the given task.
     *
     * @param  Request  $request
     * @param  Task  $task
     * @return Response
     */
     public function destroy(Request $request,   SystemController $sys)
    {
        //dd($request->input("id"));
          
            
        Models\GroupModel::where('id',$request->input("id"))->where("createdBy",\Auth::user()->fund)->delete();
     
        return redirect("/groups/create");
    }
}
