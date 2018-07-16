<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Models\AcademicCalenderModel;

 

use Yajra\Datatables\Datatables;

use Illuminate\Support\Collection;

use Illuminate\Support\Str;

 

class AcademicCalenderController extends Controller

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

    public function new_receiptno(){

        $receiptno_query = Models\Receiptno::first();

		$receiptno_query->increment("receiptno", 1);

        $receiptno = str_pad($receiptno_query->receiptno, 12, "0", STR_PAD_LEFT);

		

        return $receiptno;

        

    }

    

    public function pad_receiptno($receiptno){

       return str_pad($receiptno, 12, "0", STR_PAD_LEFT);

       }

    /**

     * Display a list of all of the user's task.

     *

     * @param  Request  $request

     * @return Response

     */

    public function getIndex(Request $request)

    {

        

        return view('academicCalender.index');

    }

    public function index(Request $request)

    {

        

        $calender = AcademicCalenderModel::select([  'ID','YEAR', 'SEMESTER','STATUS','ENTER_RESULT','LIAISON','QA','RESULT_BLOCK','QAOPEN','RESULT_DATE'])->orderBy('YEAR','DESC')->paginate('100');

       

          

           return view('academicCalender.index')->with('data',$calender);

    }



     public function createCalender(SystemController $sys)

    {

          $programme=$sys->getProgramList();

        return view('academicCalender.create')->with('programme',$programme);

             

        

    }

    /**

     * update academic calender entries

     * example close online registration

     * example close  entry of marks

     */

    public function updateCalender($item,$action, SystemController $sys) {

        if($action=="closeReg"){

          $query= AcademicCalenderModel::where("ID",$item)->update(array("STATUS"=>"0"));

        }

        elseif($action=="openReg"){

          $query= AcademicCalenderModel::where("ID",$item)->update(array("STATUS"=>"1"));

        }

        elseif($action=="closeMark"){

          $query= AcademicCalenderModel::where("ID",$item)->update(array("ENTER_RESULT"=>"0"));

        }

         elseif($action=="openMark"){

          $query= AcademicCalenderModel::where("ID",$item)->update(array("ENTER_RESULT"=>"1"));

        } 

        elseif($action=="closeLia"){

          $query= AcademicCalenderModel::where("ID",$item)->update(array("LIAISON"=>"0"));

        }

         elseif($action=="openLia"){

          $query= AcademicCalenderModel::where("ID",$item)->update(array("LIAISON"=>"1"));

        } 

        elseif($action=="closeQa"){

          $query= AcademicCalenderModel::where("ID",$item)->update(array("QAOPEN"=>"0"));   

        }

         elseif($action=="openQa"){

          $query= AcademicCalenderModel::where("ID",$item)->update(array("QAOPEN"=>"1"));

        

            

        } 

          

         if($query){

      

          return redirect("/calender")->with("success","<span style='font-weight:bold;font-size:13px;'> Calender successfully updated!!</span> ");

           }

           else{

               return redirect("/calender")->with("error","<span style='font-weight:bold;font-size:13px;'> Calender cannot be updated try again later</span> ");

         

           }

    }

    /**

     * Create a new calender item.

     *

     * @param  Request  $request

     * @return Response

     */

    public function storeCalender(Request $request)

    {

         

        $this->validate($request, [

            'year' => 'required',
            'sem'=>'required',
            'upload'=>'required',
            'qa'=>'required',
            'result'=>'required',
            

        ]);

      $year=$request->input('year');
      $sem=$request->input('sem');
      $upload=$request->input('upload');
      $qa=$request->input('qa');
      $result=$request->input('result');
      

       

       

          $calender= AcademicCalenderModel::where("id",9)->update(array('YEAR'=>$year, 'SEMESTER'=>$sem, 'RESULT_DATE'=>$upload, 'QA'=>$qa, 'RESULT_BLOCK'=>$result));

           
       if(!$calender){

      

          return redirect("/calender")->withErrors("<span style='font-weight:bold;font-size:13px;'>Academic year could not be added </span>could not be added!");

          }else{

           return redirect("/calender")->with("success","<span style='font-weight:bold;font-size:13px;'>Academic year updated </span>successfully added! ");

              

              

          }

       

          

       

    }

    

    /**

     * Destroy the given task.

     *

     * @param  Request  $request

     * @param  Task  $task

     * @return Response

     */

    public function destroy(Request $request )

    {

       // $this->authorize('destroy');

        //before delete check if the year is opened or not

        

        $stmt=AcademicCalenderModel::where('ID',$request->input("id"))->get();

        

        $semYear=$stmt[0]->STATUS; 

        $markYear=$stmt[0]->ENTER_RESULT;

       // dd($stmt);

        if($semYear==0&&$markYear==0){

       $query = AcademicCalenderModel::where('ID',$request->input("id"))->delete();

         

         if ($query) {

               return redirect("/calender")->with("success","<span style='font-weight:bold;font-size:13px;'> Calender </span>successfully deleted! ");

         

        }

        

         }

        else{

           

             return redirect("/calender")->with("error","<span style='font-weight:bold;font-size:13px;'> Calender cannot be deleted because is in use.... that's open !!</span> ");

         

        }

    }

}

