<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use App\Models;
 
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
 use Excel;

class StaffController extends Controller
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
    public function getIndex(Request $request)
    {
        
        return view('staff.index');
    }
    public function anyData(Request $request)
    {
         
        $staff = WorkerModel::select([  'id','fullName', 'designation','staffID','department']);


        return Datatables::of($staff)
              
             ->addColumn('action', function ($staffs) {
                 return "<a href=\"edit_bank/$staffs->id/id\" class=\"md-btn md-btn-primary md-btn-small md-btn-wave-light waves-effect waves-button waves-light\"><i title='click to edit' class=\"sidebar-menu-icon material-icons md-18\">edit</a>";
            
                //return' <td> <a href=" "><img class="" style="width:70px;height: auto" src="public/Albums/students/'.$student->INDEXNO.'.JPG" alt=" Picture of Employee Here"    /></a>df</td>';
                          
                                         
            })
            ->setRowId('id')
            ->setRowClass(function ($staffs) {
                return $staffs->id % 2 == 0 ? 'uk-text-success' : 'uk-text-warning';
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
    public function new_receiptno(){
        $receiptno_query = Models\Receiptno::first();
		$receiptno_query->increment("receiptno", 1);
        $receiptno = str_pad($receiptno_query->receiptno, 12, "0", STR_PAD_LEFT);
		
        return $receiptno;
        
    }
    
    public function pad_receiptno($receiptno){
       return str_pad($receiptno, 12, "0", STR_PAD_LEFT);
       }
       public function showFileUpload(Request $request) {
           return view("staff.upload");
       }
       public function uploadStaff(Request $request) {
       if($request->hasFile('file')){
            $file=$request->file('file');
            $user = \Auth::user()->id;
            $name = time() . '-' . $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension());
            $valid_exts = array('csv','xlx','xlsx'); // valid extensions
            $file = $request->file('file');
             $destination = public_path() . '/uploads';
            $path = $request->file('file')->getRealPath();
         if (in_array($ext, $valid_exts)) {
            $data = Excel::load($path, function($reader) {
                        
                    })->get();

            if(!empty($data) && $data->count()){
                $real=1111111;
	foreach ($data as $key => $value) {
                                    
		//$insert[] = ['level'=>$value->level,'fullName' => $value->name, 'staffID' => $value->staff,'department'=>$value->Department,'grade'=>$value->grade,'phone'=>$value->phone];
                //$userAccount[]=['email'=>$value->staff."@ttu.edu.gh",'password'=>$password,'name'=>$value->name,'phone'=>$value->phone,'fund'=>$value->staff];
		 
                $worker=new WorkerModel();
                $worker->level=$value->level;
                $worker->fullName=$value->name;
                $worker->staffID=$value->staff;
                $worker->department='Registry';
                $worker->grade=$value->grade;
                $worker->phone="1212121";
                $worker->save();
                
                 User::create([
                    'name' => $value->name,
                     'department' =>"AC",
                          'fund'=>$value->staff,
                          'staffID' =>$worker->id,
                          'phone' =>$value->phone,
                         'email' =>$value->staff."@ttu.edu.gh",
                   'password' => bcrypt($real),
                ]);
                
                
                
                                }

//				if(!empty($insert)){
//
//					\DB::table('tpoly_workers')->insert($insert);
//                                        
//                                        \DB::table('users')->insert($userAccount);
//					 return redirect('/dashboard')->with("success",  " <span style='font-weight:bold;font-size:13px;'>Staff  successfully uploaded!</span> " );
                              

//				}

			}
        return redirect('/dashboard')->with("success",  " <span style='font-weight:bold;font-size:13px;'>Staff  successfully uploaded!</span> " );
                                    
	}
        else{
              return redirect('/getStaffCSV')->with("error", " <span style='font-weight:bold;font-size:13px;'>Please upload file format must be xlx,csv,xslx!</span> ");
                             
        }
       }
		 
       }
//    public function uploadStaff(Request $request){
//      
//            $user = \Auth::user()->id;
//            $valid_exts = array('csv'); // valid extensions
//            $file = $request->file('file');
//            $name = time() . '-' . $file->getClientOriginalName();
//            if (!empty($file)) {
//
//                $ext = strtolower($file->getClientOriginalExtension());
//                $destination = public_path() . '\uploads';
//                if (in_array($ext, $valid_exts)) {
//                    // Moves file to folder on server
//                    // $file->move($destination, $name);
//                    if (@$file->move($destination, $name)) {
//
//
//
//                        $handle = fopen($destination . "/" . $name, "r");
//                               
//                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
//                            
//                            $num = count($data);
//                             
//                            for ($c = 0; $c < $num; $c++) {
//                                $col[$c] = $data[$c];
//                            }
//
//
//                            $name = $col[0];
//                           
//                            $staffNo = $col[1];
//                            $rank = $col[2];
//                            $dept= $col[3];
//                            $phone= $col[4];
//                               if ($name!="" && $staffNo!=""&&$dept!="") {
//
//
//                                $staff = new \App\Models\WorkerModel();
//                                $staff->fullName = $name;
//
//                                $staff->staffID = $staffNo;
//                                $staff->department = $dept;
//                                $staff->designation= $rank;
//                                 $staff->phone= $phone;
//                                
//                                if ($staff->save()) {
//                                  
//                                   return redirect('/dashboard')->with("success", array(" <span style='font-weight:bold;font-size:13px;'>Staff  successfully uploaded!</span> "));
//                                } else {
//                                    return redirect('/dashboard')->back()->withErrors("Staff could not be uploaded");
//                                }
//                            } else {
//                                echo "<script>alert('Please your files contain empty columns')</script>";
//                            }
//                            $i++;
//                        }
//                        
//                        fclose($handle);
//                           
//                    }
//                } else {
//                    echo "<script>alert('Please upload only csv files')</script>";
//                }
//            } else {
//                echo "<script>alert('Please upload a csv file')</script>";
//            }
//         
//    }
//    // show form for edit resource
    public function edit($id){
        $bank = BankModel::where("ID", $id)->firstOrFail();
        return view('banks.edit')->with('bank', $bank);
    }

    public function update(Request $request, $id){
         $query=  BankModel::where("ID",$id)->update(array("NAME"=>$request->input('bank'),"ACCOUNT_NUMBER"=>$request->input('account')));
         $banks=$request->input('bank');
         if(!$query){
      
          return redirect("/banks")->withErrors("Following banks N<u>o</u> :<span style='font-weight:bold;font-size:13px;'> $banks </span>could not be updated!");
          }else{
           return redirect("/banks")->with("success","Following banks:<span style='font-weight:bold;font-size:13px;'> $banks</span>successfully updated! ");
              
              
          }
    }
    public function directory(Request $request) {
        $query=WorkerModel::query()->paginate(30);
        return view("staff.directory")->with("data",$query);
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
    public function create(Request $request, SystemController $sys) {
        $region = $sys->getRegions();
        $department = $sys->getDepartmentList();
        $religion = $sys->getReligion();
        return view('staff.create')
            ->with('region', $region)->with("department", $department)
            ->with('religion', $religion);
    }

    public function store(Request $request, SystemController $sys) {

            $user = @\Auth::user()->organization;


            $name= $request->input('name');
            $phone= $request->input('phone');
            $department = $request->input('department');
            $staffNo = $request->input('staff');
            $real='1111111';

            $query = new Models\WorkerModel();

            $query->staffID = $staffNo;


            $query->phone = $phone;
            $query->department = $department;

            $query->nationality = "GHANAIAN";


            $query->name = $name;



            if ($query->save()) {

                User::create([
                    'name' => $name,
                     //'fullName' => $name,
                    'department' =>$department,
                    'fund'=>$staffNo,
                    'staffID' =>$query->id,
                    'phone' =>$phone,
                    'role' =>'Lecturer',

                    'password' => bcrypt($real),
                ]);


                return response()->json(['status' => 'success', 'message' => $name . ' added successfully ']);
            } else {

                return response()->json(['status' => 'error', 'message' => 'Error adding staff ']);
            }


}
}
