<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GradeSystemModel;
 
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
 
class GradeController extends Controller
{
     
    /**
     * Create a new controller instance.
     *
     
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
         if( @\Auth::user()->department!='top'){
            redirect("/dashboard");
        }
         
    }
     public function log_query() {
        \DB::listen(function ($sql, $binding, $timing) {
            \Log::info('showing query', array('sql' => $sql, 'bindings' => $binding));
        }
        );
    }
 
    /**
     * Display a list of all of the user's task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        $data=  GradeSystemModel::where("value","!=","")->groupBy("type")->orderBy("type","ASC")->paginate(100);
         return view('programme.grade_system')->with('data',$data);
    }
     
     
    public function create(SystemController $sys) {
        $grades=$sys->WASSCE_Grades();
         return view('programme.create_grade')->with('grade', $grades);
    }
    public function show(Request $request, $type,SystemController $sys) {
         $data=  GradeSystemModel::where("type",$type)->paginate(100);
          $grades=$sys->WASSCE_Grades();
         return view('programme.show_grade')->with('data',$data)->with('grade', $grades)
                 ->with('type',$type);
    }
    /**
     * Create a new task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
         //dd($request);
          
        $this->validate($request, [
            'grade' => 'required',
            'type'=>'required',
            'upper'=>'required',
            'lower'=>'required',
            'value'=>'required'
        ]);

      
      $total=count($request->input('value'));
      
      $type=$request->input('type');
      $value=$request->input('value');
      $upper=$request->input('upper');
      $lower=$request->input('lower');
      $grade=$request->input('grade');
       
      for($i=0;$i<$total;$i++){
         $data=new GradeSystemModel();
         $data->grade=$grade[$i];
         $data->lower=$lower[$i];
         $data->upper=$upper[$i];
         $data->value=$value[$i];
         $data->type=$type;
           
         $data->save();
          
      }
       if(!$data){
      
          return redirect("/create_grade")->withErrors("<span style='font-weight:bold;font-size:13px;'>Grades could not be created!</span>");
          }else{
           return redirect("/grade_system")->with("success"," <span style='font-weight:bold;font-size:13px;'>Grades added successfully created!</span> ");
              
              
          }
       
          
       
    }
    // show form for edit resource
    public function edit($id){
         
    }

    public function update(Request $request){
       
         \DB::beginTransaction();
        try {       
      $total= $request->input('upper') ;
      
      $key=$request->input('key');
      $type=$request->input('type');
      $value=$request->input('value');
      $upper=$request->input('upperLimit');
      $lower=$request->input('lower');
      $grade=$request->input('grade');
       
      for($i=0;$i<$total;$i++){
          
         $keyArr=$key[$i];
         
         $gradeArr=$grade[$i];
         $lowerArr=$lower[$i];
         $upperArr=$upper[$i];
         $valueArr=$value[$i];
         
           
       $query= GradeSystemModel::where("id", $keyArr)->update(array("grade" => $gradeArr, "lower" => $lowerArr, "upper" => $upperArr, "value" => $valueArr));

        \DB::commit();
          
      }
       if(!$query){
      
          return redirect()->back()->withErrors("<span style='font-weight:bold;font-size:13px;'>Grades could not be created!</span>");
          }else{
           return redirect("/grade_system/$type/slug")->with("success"," <span style='font-weight:bold;font-size:13px;'>Grades added successfully created!</span> ");
              
              
                }
               
         }
          catch (\Exception $e) {
            \DB::rollback();
        } 
       
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
