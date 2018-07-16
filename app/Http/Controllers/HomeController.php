<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Khill\Lavacharts\Lavacharts;
use Illuminate\Http\Response;
use App\Models\MessagesModel;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\User;


class HomeController extends Controller
{

    public function __construct()
    {
        
        $this->middleware('auth');
        ini_set('max_execution_time', 180000);  
       
        $user=@\Auth::user()->id;
        $date=new \Datetime();
        @User::where("id", $user)->update(array("last_login"=>$date));
      
        
    }
    public function chartjs()
    {
        $viewer = User::select(\DB::raw("SUM(id) as count"))
            ->orderBy("created_at")
            ->groupBy(\DB::raw("year(created_at)"))
            ->get()->toArray();
        $viewer = array_column($viewer, 'count');

        $click = User::select(\DB::raw("SUM(id) as count"))
            ->orderBy("created_at")
            ->groupBy(\DB::raw("year(created_at)"))
            ->get()->toArray();
        $click = array_column($click, 'count');


        return view('graphs.try')
            ->with('viewer',json_encode($viewer,JSON_NUMERIC_CHECK))
            ->with('click',json_encode($click,JSON_NUMERIC_CHECK));
    }

    /**
     * Display a list of all of the user's task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request,SystemController $sys)
    {   if(@\Auth::user()->department=="Admissions"){
            //$sys->getZenith();
            return redirect("applicants/view");
         }

         if(@\Auth::user()->password=='$2y$10$O1CDHaNMOUhhipT9ftmpyew2IXwIdxBNvUPzJCSDh1V2VS0KlPgx6'){
            //$sys->getZenith();
            return view("users.updateProfile");
         }

         if(@\Auth::user()->password=='$2y$10$O1CDHaNMOUhhipT9ftmpyew2IXwIdxBNvUPzJCSDh1V2VS0KlPgx6'){
            //$sys->getZenith();
            return redirect("change_password");
         }

        if(@\Auth::user()->phone==""){
            return view("users.updateProfile");
        }
        else{
           
            //ini_set('max_execution_time', 50000);
         // $sys->getZenith();
           // $sys->generateIndexNumbers();
           /* $dataGenerator=Models\StudentModel::where("LEVEL","100H")->orWhere("LEVEL","100NTT")
                ->orWhere("LEVEL","100BTT")->orWhere("LEVEL","500")->get();
            foreach($dataGenerator as $row){
                $index=$sys->assignIndex($row->PROGRAMMECODE);
                Models\StudentModel::where("STNO",$row->STNO)->update(array("INDEXNO"=>$index));
                Models\PortalPasswordModel::where("username",$row->STNO)->update(array("username"=>$index));
            }*/

 


        $lastVisit=\Carbon\Carbon::createFromTimeStamp(strtotime(@\Auth::user()->last_login))->diffForHumans();

        $academicDetails=$sys->getSemYear();
        $sem=$academicDetails[0]->SEMESTER;
        $year=$academicDetails[0]->YEAR;

        $studentDetail=Models\StudentModel::query()->where('STATUS','In school')->sum("BILL_OWING");
        $total=@Models\StudentModel::query()->where('STATUS','In school')->count("ID");

        $totalRegistered =Models\StudentModel::query()->where('REGISTERED','1')
                     ->count();
                      
              // $totalRegistered =count($totalRegistered );

               /*
                Gad--- i added the academic year and sem to reduce query weight
               */
       $registered= @Models\AcademicRecordsModel::query()->where('lecturer',@\Auth::user()->fund)
       ->where('sem',$sem)->where('year',$year)
       ->count("id");

        $totalOwing=@$sys->formatMoney($studentDetail);
        //Payment details
        $totalPaid=Models\FeePaymentModel::query()->where('YEAR',$year)->where('FEE_TYPE','School Fees')->sum("AMOUNT");

        $paid=@$sys->formatMoney($totalPaid);
 
        // statistics
         $totalProgram=@Models\StudentModel::query()->where('SYSUPDATE','1')->groupBy("LEVEL")->get();

        return view('dashboard')->with('paid', $paid)
                                ->with('owing', $totalOwing)
                                  ->with('register', $registered)
                                  ->with('total', $total)
                                  ->with('totalRegistered', $totalRegistered)
                                  ->with('data', $totalProgram)
                                ->with('sem', $sem)
                                ->with('year', $year)
                                ->with('lastVisit', $lastVisit);


        }

    }
    public function accountStatement(Request $request, SystemController $sys) {
        $student=@\Auth::user()->username;


        $academicDetails=$sys->getSemYear();
        $sem=$academicDetails[0]->SEMESTER;
        $year=$academicDetails[0]->YEAR;

        $studentDetail=Models\StudentModel::query()->where('STATUS','In School')->where('INDEXNO',$student)->first();


        $outstandingBill=@$sys->formatMoney($studentDetail->BILL_OWING);
        $SemesterBill=@$sys->formatMoney($studentDetail->BILLS);
          //Payment details
        $paymentDetail=  Models\FeePaymentModel::query()->where('INDEXNO',$student)->orderBy('LEVEL','DESC')->orderBy('YEAR','DESC')->paginate(100);
        return view("students.account_statement")->with("transaction", $paymentDetail)
                ->with('balance', $outstandingBill)
                ->with('semesterBill', $paymentDetail);
    }
    /**
     * Create a new task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function buildChart(Request $request)
    {
         $viewer = User::select(\DB::raw("SUM(id) as count"))
        ->orderBy("created_at")
        ->groupBy(\DB::raw("year(created_at)"))
        ->get()->toArray();
    $viewer = array_column($viewer, 'count');

    $click = User::select(\DB::raw("SUM(id) as count"))
        ->orderBy("created_at")
        ->groupBy(\DB::raw("year(created_at)"))
        ->get()->toArray();
    $click = array_column($click, 'count');

    return view('graph')
            ->with('viewer',json_encode($viewer,JSON_NUMERIC_CHECK))
            ->with('click',json_encode($click,JSON_NUMERIC_CHECK));
    }
    public function getLaraChart()
    {
    	$lava = new Lavacharts; // See note below for Laravel

		$popularity = $lava->DataTable();
		$data = \App\Models\CountryUser::select("name as 0","total_users as 1")->get()->toArray();

		$popularity->addStringColumn('Country')
		           ->addNumberColumn('Popularity')
		           ->addRows($data);

		$lava->GeoChart('Popularity', $popularity);

        return view('graph',compact('lava'));
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
