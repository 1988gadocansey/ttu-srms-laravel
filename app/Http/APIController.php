<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentModel;
 
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
 use Response;
class APIController extends Controller
{
     
    /**
     * Create a new controller instance.
     *
     
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('auth');
       
         
    }
      
    public function qualityAssurance(Request $request,$indexno) {
        @StudentModel::where("INDEXNO",$indexno)->update(array("QUALITY_ASSURANCE"=>1));
        // return $this->response->json("status","Student Lecturer Assessment received at main system");
         return Response::json("Student Lecturer Assessment received at main system","200");
    }
    public function liaison(Request $request,$indexno) {
       @ StudentModel::where("INDEXNO",$indexno)->update(array("LIAISON"=>1));
        return Response::json("Student Liaison forms received at main system","200");
        
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
