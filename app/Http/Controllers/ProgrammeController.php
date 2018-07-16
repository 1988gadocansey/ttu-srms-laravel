<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\ProgrammeModel;
use App\Models; 
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
 use Symfony\Component\HttpKernel\Exception\HttpException;

class ProgrammeController extends Controller
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
    public function getIndex(Request $request)
    {
        
        return view('programme.index');
    }
    public function anyData(Request $request)
    {
         
        $program = ProgrammeModel::select([  'ID','DEPTCODE', 'PROGRAMMECODE','PROGRAMME','AFFILAITION','DURATION','MINCREDITS','MAXI_CREDIT','GRADING_SYSTEM']);


        return Datatables::of($program)
              
             ->addColumn('action', function ($programme_) {
                 return "<a href=\"edit_programme/$programme_->ID/id\" class=\"md-btn md-btn-primary md-btn-small md-btn-wave-light waves-effect waves-button waves-light\"><i title='click to edit' class=\"sidebar-menu-icon material-icons md-18\">edit</a>";
            
                //return' <td> <a href=" "><img class="" style="width:70px;height: auto" src="public/Albums/students/'.$student->INDEXNO.'.JPG" alt=" Picture of Employee Here"    /></a>df</td>';
                          
                                         
            })
            ->setRowId('id')
            ->setRowClass(function ($programme_) {
                return $programme_->ID % 2 == 0 ? 'uk-text-success' : 'uk-text-warning';
            })
            ->setRowData([
                'id' => 'test',
            ])
            ->setRowAttr([
                'color' => 'red',
            ])
                  
            ->make(true);
             
            //flash the request so it can still be available to the view or search form and the search parameters shown on the form 
      //$request->flash();
    }

     
    public function create(SystemController $sys) {
       if(@\Auth::user()->role=='Dean' || @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop' || @\Auth::user()->department=='Tptop'){
        $department=$sys->department();
         return view('programme.create')->with('department', $department)
                 ->with('grade', $sys->getGradeSystemIDList());
         }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
         
    }
    public function createClass(SystemController $sys) {
       if(@\Auth::user()->role=='Dean' || @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop' || @\Auth::user()->department=='Tptop'){
       
        // return view('programme.create_class');
                  $this->validate($request, [
            'lower' => 'required',
            'upper'=>'required',
            'class'=>'required',
             
        ]);
        
      
      $total=count($request->input('lower'));
      $class=$request->input('class');
      $upper=$request->input('upper');
      $lower=$request->input('lower');
      
       
      for($i=0;$i<$total;$i++){
         $classModel=new App\Models\ClassModel();
          
         $classModel->lowerBoundary=$lower[$i];
         $classModel->upperBoundary=$upper[$i];
         $classModel->class=$class[$i];
            
         $classModel->save();
          
      }
       if(!$classModel){
      
          return redirect("classes/create")->withErrors(" <span style='font-weight:bold;font-size:13px;'>Classes could not be created </span>could not be added!");
          }else{
           return redirect("classes/view")->with("success","<span style='font-weight:bold;font-size:13px;'> Classes successfully created!</span> ");
              
              
          }
        }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
         
    }
    public function storeClass(Request $request){
        if(@\Auth::user()->role=='Dean' || @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop'  || @\Auth::user()->department=='Tptop'){
       
          
                  $this->validate($request, [
            'lower' => 'required',
            'upper'=>'required',
            'class'=>'required',
             
        ]);
        
      
      $total=count($request->input('lower'));
      $class=$request->input('class');
      $upper=$request->input('upper');
      $lower=$request->input('lower');
      
       
      for($i=0;$i<$total;$i++){
         $classModel= new Models\ClassModel();
          
         $classModel->lowerBoundary=$lower[$i];
         $classModel->upperBoundary=$upper[$i];
         $classModel->class=$class[$i];
            
         $classModel->save();
          
      }
       if(!$classModel){
      
          return redirect("classes/create")->withErrors(" <span style='font-weight:bold;font-size:13px;'>Classes could not be created </span>could not be added!");
          }else{
           return redirect("classes/view")->with("success","<span style='font-weight:bold;font-size:13px;'> Classes successfully created!</span> ");
              
              
          }
        }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
    }
    public function viewClasses(Request $request ) {
         if(@\Auth::user()->role=='Dean' || @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop' || @\Auth::user()->department=='Tptop'){
       
         $data= Models\ClassModel::where('id','!=','')->paginate(100);
         return view('programme.classes')->with('data',$data);
     }
         else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
    }
    /**
     * Create a new task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
         
      if(@\Auth::user()->role=='Dean' || @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop' || @\Auth::user()->department=='Tptop'){
        $this->validate($request, [
            'name' => 'required',
            'department'=>'required',
            'code'=>'required',
            'duration'=>'required',
            'credit'=>'required',
            'grade'=>'required',
        ]);
        //$this->validate($request, [
        //        'title' => 'required|unique:posts|max:255',
        //        'body' => 'required',
        //    ]);
      
      $total=count($request->input('code'));
      $name=$request->input('name');
      $department=$request->input('department');
      $duration=$request->input('duration');
      $credit=$request->input('credit');
      $code=$request->input('code');
      $grade=$request->input('grade');
       
      for($i=0;$i<$total;$i++){
         $program=new ProgrammeModel();
         $program->DEPTCODE=$department[$i];
         $program->PROGRAMMECODE=$code[$i];
         $program->PROGRAMME=$name[$i];
         $program->DURATION=$duration[$i];
         $program->MINCREDITS=$credit[$i];
         $program->GRADING_SYSTEM=$grade[$i];
           
         $program->save();
          
      }
       if(!$program){
      
          return redirect("/programmes")->withErrors("Following programmes N<u>o</u> :<span style='font-weight:bold;font-size:13px;'>programme could not be added </span>could not be added!");
          }else{
           return redirect("/programmes")->with("success","Following programme:<span style='font-weight:bold;font-size:13px;'> programme added </span>successfully added! ");
              
              
      }}
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
          
       
    }
    // show form for edit resource
    public function edit($id, SystemController $sys){
        if(@\Auth::user()->role=='Dean'||@\Auth::user()->role=='Admin' || @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop'){
        $programme= ProgrammeModel::where("ID", $id)->firstOrFail();
        $department=$sys->department();
         return view('programme.edit')->with('department', $department)
                 ->with('grade', $sys->getGradeSystemIDList())
                 ->with('data', $programme);
         }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
        }
    }

    public function update(Request $request, $id){
         if(@\Auth::user()->role=='Dean'||@\Auth::user()->role=='Admin' || @\Auth::user()->department=='top' || @\Auth::user()->department=='Tptop'){
     
        \DB::beginTransaction();
        try {
        $name = $request->input('name');
        $department = $request->input('department');
        $duration = $request->input('duration');
        $credit = $request->input('credit');
        $code = $request->input('code');
        $grade = $request->input('grade');
        $query = ProgrammeModel::where("ID", $id)->update(array("PROGRAMME" => $name, "PROGRAMMECODE" => $code, "DURATION" => $duration, "GRADING_SYSTEM" => $grade, "DEPTCODE" => $department, "MINCREDITS" => $credit));
        
        if (!$query) {

            return redirect("/programmes")->withErrors("Following banks N<u>o</u> :<span style='font-weight:bold;font-size:13px;'> $name could not be updated!</span>");
        } else {
            return redirect("/programmes")->with("success", "Following banks:<span style='font-weight:bold;font-size:13px;'> $name successfully updated!</span> ");
        }
         } catch (\Exception $e) {
            \DB::rollback();
        }
        }
        else{
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'This action is unauthorized.');
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
        
    }
}
